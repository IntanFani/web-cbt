<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamSession; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 
use App\Models\Answer;
use App\Models\Option;

class SiswaController extends Controller
{
    public function index()
    {
        // Ambil ID siswa yang sedang login
        $userID = Auth::id();

        // Ambil semua ujian + cek status pengerjaan siswa tersebut
        $exams = Exam::with(['sessions' => function($query) use ($userID) {
            $query->where('user_id', $userID);
        }])->get();

        // Return ke view yang sama (dashboard.siswa.index)
        return view('dashboard.siswa.index', compact('exams'));
    }

    // 1. Logic Persiapan Sebelum Masuk Ujian
    public function startExam($id)
    {
        $user_id = Auth::id();
        
        // Cek apakah siswa sudah pernah mulai ujian ini?
        $existingSession = ExamSession::where('user_id', $user_id)
                                      ->where('exam_id', $id)
                                      ->first();

        // Kalau belum ada sesi, kita buatkan sesi baru
        if (!$existingSession) {
            $exam = Exam::findOrFail($id);
            
            ExamSession::create([
                'user_id' => $user_id,
                'exam_id' => $id,
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addMinutes($exam->duration),
                'status' => 'ongoing'
            ]);
        }

        // Redirect ke halaman pengerjaan soal
        return redirect()->route('ujian.show', $id);
    }

    // 2. Logic Menampilkan Halaman Soal
    public function showExam($id)
    {
        $user_id = Auth::id();
        
        // Ambil sesi ujian siswa (pastikan valid)
        $session = ExamSession::where('user_id', $user_id)
                              ->where('exam_id', $id)
                              ->where('status', 'ongoing')
                              ->first();

        // Kalau tidak ada sesi (misal nembak URL tanpa klik Start), tendang balik
        if (!$session) {
            return redirect()->route('dashboard.siswa');
        }

        // Ambil data ujian beserta soal dan opsinya
        $exam = Exam::with(['questions.options'])->findOrFail($id);

        return view('dashboard.siswa.ujian', compact('exam', 'session'));
    }

    public function saveAnswer(Request $request)
    {
        // 1. Validasi data yang dikirim dari JS
        $request->validate([
            'session_id' => 'required',
            'question_id' => 'required',
            'option_id' => 'required'
        ]);

        // 2. Cek apakah jawaban ini benar/salah (Auto-Correction)
        $option = Option::find($request->option_id);
        $isCorrect = $option ? $option->is_correct : false;

        // 3. Simpan atau Update Jawaban (Pakai updateOrCreate biar rapi)
        Answer::updateOrCreate(
            [
                'exam_session_id' => $request->session_id, // Kunci pencarian (WHERE)
                'question_id' => $request->question_id
            ],
            [
                'option_id' => $request->option_id,        // Data yang diupdate
                'is_correct' => $isCorrect
            ]
        );

        return response()->json(['status' => 'success']);
    }

    public function finishExam($id)
    {
        // 1. Ambil sesi ujian siswa saat ini
        $session = ExamSession::where('user_id', Auth::id())
                            ->where('exam_id', $id)
                            ->where('status', 'ongoing')
                            ->first();

        // Kalau sesi tidak ditemukan (misal user iseng nembak URL), kembalikan
        if (!$session) {
            return redirect()->route('dashboard.siswa');
        }

        // 2. Hitung Nilai Otomatis
        // Ambil total soal dari relasi exam -> questions
        $totalSoal = $session->exam->questions->count();
        
        // Ambil jumlah jawaban benar dari tabel answers milik sesi ini
        $jawabanBenar = $session->answers->where('is_correct', true)->count();
        
        // Rumus Nilai: (Benar / Total) * 100
        $nilai = ($totalSoal > 0) ? round(($jawabanBenar / $totalSoal) * 100, 2) : 0;

        // 3. Update Status Sesi jadi 'completed' & Simpan Nilai
        $session->update([
            'status' => 'completed',
            'score' => $nilai,
            // Kita kunci waktu selesainya di detik ini
            'end_time' => Carbon::now() 
        ]);

        // 4. Redirect ke Dashboard dengan pesan sukses
        return redirect()->route('dashboard.siswa')->with('success', 'Selamat! Ujian telah selesai. Nilai Anda: ' . $nilai);
    }

    // 5. History Nilai
    public function history()
    {
        // Ambil sesi ujian milik siswa yang sedang login dan statusnya 'completed'
        $histories = ExamSession::with('exam.teacher')
                        ->where('user_id', Auth::id())
                        ->where('status', 'completed')
                        ->latest()
                        ->get();

        return view('dashboard.siswa.history', compact('histories'));
    }
}