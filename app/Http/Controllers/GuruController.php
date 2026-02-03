<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Exam;
use App\Models\Question; 
use App\Models\Option;
use App\Models\ExamSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; 

class GuruController extends Controller
{
    // ==========================================
    // 1. DASHBOARD UTAMA
    // ==========================================
    public function index()
    {
        $teacherId = Auth::id();

        // DATA KARTU STATISTIK
        // 1. Total Siswa (Ambil semua user role siswa)
        $totalSiswa = User::where('role', 'siswa')->count();

        // 2. Total Ujian (Yang dibuat oleh guru ini)
        $totalUjian = Exam::where('teacher_id', $teacherId)->count();

        // 3. Rata-rata Nilai (Dari semua siswa yang mengerjakan ujian guru ini)
        $avgNilai = ExamSession::whereHas('exam', function($q) use ($teacherId) {
                        $q->where('teacher_id', $teacherId);
                    })
                    ->where('status', 'completed')
                    ->avg('score');

        // 4. Total Ujian Selesai
        $totalSelesai = ExamSession::whereHas('exam', function($q) use ($teacherId) {
                        $q->where('teacher_id', $teacherId);
                    })
                    ->where('status', 'completed')
                    ->count();

        // DATA TABEL & LIST
        // 5. Tabel Penyelesaian Terkini (5 Data terakhir)
        $recentActivities = ExamSession::with(['user', 'exam'])
                            ->whereHas('exam', function($q) use ($teacherId) {
                                $q->where('teacher_id', $teacherId);
                            })
                            ->where('status', 'completed')
                            ->latest('updated_at')
                            ->take(5)
                            ->get();

        // 6. List Ujian Terbaru
        $recentExams = Exam::where('teacher_id', $teacherId)
                            ->latest()
                            ->take(3)
                            ->get();

        // Tetap di folder dashboard/guru (karena ini dashboard utama)
        return view('dashboard.guru.index', compact(
            'totalSiswa', 
            'totalUjian', 
            'avgNilai', 
            'totalSelesai', 
            'recentActivities', 
            'recentExams'
        ));
    }

    // ==========================================
    // 2. MANAJEMEN UJIAN (CRUD)
    // ==========================================
    
    // A. Halaman Daftar Ujian (Index)
    public function manageExams()
    {
        $exams = Exam::where('teacher_id', Auth::id())->latest()->get();
        // Path View Baru: dashboard.guru.ujian.index
        return view('dashboard.guru.ujian.index', compact('exams'));
    }

    // B. Halaman Buat Ujian Baru
    public function createExam()
    {
        // Path View Baru: dashboard.guru.ujian.create
        return view('dashboard.guru.ujian.create');
    }

    // C. Proses Simpan Ujian
    public function storeExam(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        $exam = Exam::create([
            'teacher_id' => Auth::id(),
            'title' => $request->title,
            'duration' => $request->duration,
            'token' => Str::upper(Str::random(6)), 
            'random_question' => $request->has('random_question'),
        ]);

        // Setelah buat ujian, langsung arahkan ke halaman input soal biar praktis
        return redirect()->route('ujian.questions', $exam->id)->with('success', 'Ujian berhasil dibuat! Silakan tambah soal.');
    }

    // D. Halaman Edit Ujian
    public function edit($id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        // Path View Baru: dashboard.guru.ujian.edit
        return view('dashboard.guru.ujian.edit', compact('exam'));
    }

    // E. Proses Update Ujian
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        $exam = Exam::findOrFail($id);
        
        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        $exam->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'random_question' => $request->has('random_question') ? 1 : 0,
        ]);

        return redirect()->route('ujian.index')->with('success', 'Konfigurasi ujian berhasil diperbarui!');
    }

    // F. Hapus Ujian
    public function deleteExam($id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        $exam->questions()->delete(); 
        $exam->sessions()->delete();
        $exam->delete();

        return redirect()->route('ujian.index')->with('success', 'Ujian berhasil dihapus permanen.');
    }

    // ==========================================
    // 3. KELOLA SOAL
    // ==========================================

    // A. Halaman Daftar Soal
    public function manageQuestions($id)
    {
        $exam = Exam::with(['questions.options'])->findOrFail($id);
        
        if($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        // Path View Baru: dashboard.guru.ujian.questions
        return view('dashboard.guru.ujian.questions', compact('exam'));
    }

    // B. Simpan Soal Manual
    public function storeQuestion(Request $request, $id)
    {
        $request->validate([
            'question_text' => 'required',
            'options' => 'required|array|min:2',
            'correct_answer' => 'required|integer',
        ]);

        $exam = Exam::findOrFail($id);

        if($exam->teacher_id != Auth::id()) { abort(403); }

        $question = Question::create([
            'exam_id' => $id,
            'question_text' => $request->question_text,
            'type' => 'pilihan_ganda'
        ]);

        foreach ($request->options as $index => $optionText) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => ($index == $request->correct_answer)
            ]);
        }

        return redirect()->back()->with('success', 'Soal berhasil ditambahkan!');
    }
    
    // C. Hapus Soal
    public function deleteQuestion($id)
    {
        $question = Question::findOrFail($id);
        
        // Cek kepemilikan via relasi exam
        if($question->exam->teacher_id != Auth::id()) { abort(403); }

        $question->delete(); 
        
        return redirect()->back()->with('success', 'Soal berhasil dihapus.');
    }

    // D. Import Soal dari CSV
    public function importQuestions(Request $request, $id)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt'
    ]);

    $exam = Exam::findOrFail($id);

    // Cek otorisasi pemilik ujian
    if ($exam->teacher_id != Auth::id()) {
        abort(403);
    }

    $file = $request->file('file');

    // Gunakan Transaction agar jika error di tengah jalan, tidak ada data setengah-setengah yang masuk
    DB::beginTransaction();

    try {
        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            
            // 1. LEWATI HEADER (Baris Pertama)
            fgetcsv($handle); 

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                
                // Validasi kolom minimal 6
                if (count($data) < 6) continue; 

                // Buat Soal
                $question = Question::create([
                    'exam_id' => $exam->id,
                    'question_text' => $data[0], // Kolom 1: Soal
                    'type' => 'pilihan_ganda'
                ]);

                // Ambil Kunci Jawaban (Bersihkan spasi & Huruf Besar)
                // Contoh: " a " -> "A"
                $correctKey = strtoupper(trim($data[5])); 

                // Mapping Huruf ke Angka Urutan (1-4)
                // A = 1, B = 2, C = 3, D = 4
                $keyMap = [
                    'A' => 1,
                    'B' => 2,
                    'C' => 3,
                    'D' => 4
                ];

                // Jika kunci tidak valid, default ke 0 (tidak ada yang benar)
                $correctIndex = $keyMap[$correctKey] ?? 0;

                // Loop Opsi (Index CSV 1 s/d 4)
                // $i = 1 (A), $i = 2 (B), dst...
                for ($i = 1; $i <= 4; $i++) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $data[$i], // Ambil teks dari kolom 1, 2, 3, 4
                        // Bandingkan urutan loop ($i) dengan hasil mapping kunci ($correctIndex)
                        'is_correct' => ($i == $correctIndex) ? 1 : 0 
                    ]);
                }
            }
            fclose($handle);
        }

        DB::commit(); // Simpan permanen jika sukses
        return back()->with('success', 'Import soal berhasil!');

    } catch (\Exception $e) {
        DB::rollBack(); // Batalkan semua jika ada error
        return back()->with('error', 'Gagal import: ' . $e->getMessage());
    }
}
    // ==========================================
    // 4. HASIL & LAPORAN
    // ==========================================

    // A. Halaman Hasil Ujian (Dengan Filter)
    public function examResults(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        // Data Filter Dropdown
        $allKelas = Kelas::all();
        $allAngkatan = User::where('role', 'siswa')
                        ->whereNotNull('angkatan')
                        ->select('angkatan')
                        ->distinct()
                        ->orderBy('angkatan', 'desc')
                        ->pluck('angkatan');

        // Query Dasar
        $query = ExamSession::with(['user.kelas'])->where('exam_id', $id)->where('status', 'completed');

        // Terapkan Filter
        if ($request->filled('filter_kelas')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('kelas_id', $request->filter_kelas);
            });
        }

        if ($request->filled('filter_angkatan')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('angkatan', $request->filter_angkatan);
            });
        }

        $sessions = $query->orderBy('score', 'desc')->get();

        // Path View Baru: dashboard.guru.ujian.results
        return view('dashboard.guru.ujian.results', compact('exam', 'sessions', 'allKelas', 'allAngkatan'));
    }

    // B. Reset Ujian Perorangan
    public function resetExam($sessionId)
    {
        $session = ExamSession::with('exam')->findOrFail($sessionId);

        if ($session->exam->teacher_id != Auth::id()) {
            abort(403);
        }

        $session->answers()->delete();
        $session->delete();

        return redirect()->back()->with('success', 'Ujian siswa di-reset.');
    }

    // C. Reset Massal (Bulk Reset)
    public function resetBulk(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        
        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        $query = ExamSession::where('exam_id', $id);

        if (!$request->filled('filter_kelas') && !$request->filled('filter_angkatan')) {
            return back()->with('error', 'Pilih filter Kelas/Angkatan dulu!');
        }

        if ($request->filled('filter_kelas')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('kelas_id', $request->filter_kelas);
            });
        }

        if ($request->filled('filter_angkatan')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('angkatan', $request->filter_angkatan);
            });
        }

        $count = $query->count();

        if ($count == 0) {
            return back()->with('error', 'Tidak ada data untuk di-reset.');
        }

        $sessions = $query->get();
        foreach($sessions as $session) {
            $session->answers()->delete(); 
            $session->delete();            
        }

        return back()->with('success', "Berhasil me-reset $count siswa.");
    }

    // D. Export Excel
    public function exportExcel(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        // Logic Filter Sama Persis dengan examResults
        $query = ExamSession::with(['user.kelas'])->where('exam_id', $id)->where('status', 'completed');

        if ($request->filled('filter_kelas')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('kelas_id', $request->filter_kelas);
            });
        }

        if ($request->filled('filter_angkatan')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('angkatan', $request->filter_angkatan);
            });
        }

        $sessions = $query->orderBy('score', 'desc')->get();

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Nilai_Ujian_{$exam->title}.xls");

        // View khusus export (tidak perlu pindah folder karena cuma tabel murni)
        return view('dashboard.guru.export_excel', compact('exam', 'sessions'));
    }
}