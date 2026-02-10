<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamSession; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 
use App\Models\Answer;
use App\Models\Option;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; 

class SiswaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Filter: Ambil ujian dimana kelas_id di tabel exams SAMA DENGAN kelas_id di tabel users
        $exams = Exam::where('kelas_id', $user->kelas_id)
            ->with(['sessions' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();

        return view('dashboard.siswa.index', compact('exams'));
    }

    // 1. Logic Persiapan Sebelum Masuk Ujian
    public function startExam(Request $request, $id)
    {
        // Ambil objek User yang sedang login (BUKAN cuma ID-nya)
        $user_id = Auth::user(); 
        $exam = Exam::findOrFail($id);

        // Proteksi: Bandingkan kelas_id ujian dengan kelas_id user
        if ($exam->kelas_id != $user_id->kelas_id) {
            return redirect()->back()->with('error', 'Maaf, ujian ini bukan untuk kelas Anda.');
        }

        // 1. Validasi kecocokan token
        if ($request->token !== $exam->token) {
            return redirect()->back()->with('error', 'Token yang Anda masukkan salah atau tidak berlaku!');
        }
        
        // 2. Cek apakah sesi sudah ada
        $existingSession = ExamSession::where('user_id', $user_id)
                                    ->where('exam_id', $id)
                                    ->first();

        // 3. Jika belum ada, buat sesi baru
        if (!$existingSession) {
            ExamSession::create([
                'user_id' => $user_id,
                'exam_id' => $id,
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addMinutes($exam->duration),
                'status' => 'ongoing'
            ]);
        }

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

        if (!$session) {
            return redirect()->route('dashboard.siswa');
        }

        // Ambil data ujian
        $exam = Exam::findOrFail($id);

        // ACAK SOAL DISINI:
        // Kita ambil soal yang berelasi dengan exam_id ini secara acak
        $questions = $exam->questions()->with('options')->inRandomOrder(Auth::id())->get();

        // Masukkan kembali soal yang sudah diacak ke dalam object exam agar Blade tidak error
        $exam->setRelation('questions', $questions);

        return view('dashboard.siswa.ujian', compact('exam', 'session'));
    }

    public function saveAnswer(Request $request)
{
    // 1. Validasi: option_id dan essay_answer dibuat opsional (nullable)
        $request->validate([
            'session_id'  => 'required',
            'question_id' => 'required',
            'option_id'   => 'nullable', // Dibuat opsional agar Essay bisa lewat
            'essay_answer'=> 'nullable'  // Tambahkan ini untuk menampung teks essay
        ]);

        // 2. Logika Auto-Correction hanya untuk Pilihan Ganda
        $isCorrect = false;
        if ($request->filled('option_id')) {
            $option = Option::find($request->option_id);
            $isCorrect = $option ? $option->is_correct : false;
        }

        // 3. Simpan atau Update Jawaban
        // Pastikan di Model 'Answer' sudah ada 'essay_answer' di dalam $fillable
        Answer::updateOrCreate(
            [
                'exam_session_id' => $request->session_id,
                'question_id'     => $request->question_id
            ],
            [
                'option_id'    => $request->option_id,
                'essay_answer' => $request->essay_answer, // Simpan teks essay ke database
                'is_correct'   => $isCorrect
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

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        DB::beginTransaction();

        try {
            if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
                fgetcsv($handle); // Lewati baris header

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Kolom CSV: 0=Nama, 1=Email, 2=Password, 3=Nama Kelas, 4=Angkatan
                    
                    // Cari ID Kelas berdasarkan nama kelas di CSV
                    $kelas = Kelas::where('nama_kelas', trim($data[3]))->first();
                    
                    if ($kelas) {
                        User::create([
                            'name' => $data[0],
                            'email' => $data[1],
                            'password' => Hash::make($data[2]),
                            'role' => 'siswa',
                            'kelas_id' => $kelas->id,
                            'angkatan' => $data[4],
                        ]);
                    }
                }
                fclose($handle);
            }

            DB::commit();
            return back()->with('success', 'Data siswa berhasil diimport!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}