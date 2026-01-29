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
use Illuminate\Support\Str; // Untuk bikin token acak

class GuruController extends Controller
{
    // 1. Tampilkan Dashboard (Daftar Ujian)
    public function index()
    {
        // Ambil ujian milik guru yang sedang login, urutkan dari yang terbaru
        $exams = Exam::where('teacher_id', Auth::id())->latest()->get();
        return view('dashboard.guru.index', compact('exams'));
    }

    // 2. Tampilkan Halaman Buat Ujian Baru
    public function createExam()
    {
        return view('dashboard.guru.create');
    }

    // 3. Proses Simpan Ujian ke Database
    public function storeExam(Request $request)
    {
        // Validasi input
        $request->validate([
            'title' => 'required|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        // Simpan ke database
        Exam::create([
            'teacher_id' => Auth::id(),
            'title' => $request->title,
            'duration' => $request->duration,
            // Generate Token otomatis 6 karakter (Contoh: X7B9A2)
            'token' => Str::upper(Str::random(6)), 
            'random_question' => $request->has('random_question'),
        ]);

        return redirect()->route('dashboard.guru')->with('success', 'Ujian berhasil dibuat!');
    }

    // 4. Halaman Kelola Soal (Menampilkan daftar soal di ujian tsb)
    public function manageQuestions($id)
    {
        $exam = Exam::with(['questions.options'])->findOrFail($id);
        
        // Pastikan ujian ini milik guru yang sedang login (Security)
        if($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        return view('dashboard.guru.questions', compact('exam'));
    }

    // 5. Proses Simpan Soal Baru
    public function storeQuestion(Request $request, $id)
    {
        $request->validate([
            'question_text' => 'required',
            'options' => 'required|array|min:2', // Minimal 2 opsi
            'correct_answer' => 'required|integer', // Index opsi yang benar (0, 1, 2, atau 3)
        ]);

        $exam = Exam::findOrFail($id);

        // A. Simpan Soalnya dulu
        $question = Question::create([
            'exam_id' => $id,
            'question_text' => $request->question_text,
            'type' => 'pilihan_ganda'
        ]);

        // B. Simpan Opsi Jawabannya (Looping A, B, C, D)
        foreach ($request->options as $index => $optionText) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => ($index == $request->correct_answer) // Cek apakah ini jawaban benar
            ]);
        }

        return redirect()->back()->with('success', 'Soal berhasil ditambahkan!');
    }
    
    // 6. Hapus Soal
    public function deleteQuestion($id)
    {
        $question = Question::findOrFail($id);
        // Cek kepemilikan via relasi exam->teacher_id (Opsional tapi disarankan)
        $question->delete(); // Opsi jawaban akan terhapus otomatis (Cascade) jika di-setting di database, kalau tidak manual.
        // Di Laravel model delete biasanya aman.
        
        return redirect()->back()->with('success', 'Soal dihapus.');
    }

    // 7. Hapus Ujian (Beserta Soal & Nilainya)
    public function deleteExam($id)
    {
        $exam = Exam::findOrFail($id);

        // Security: Pastikan yang hapus adalah pembuat ujian
        if ($exam->teacher_id != Auth::id()) {
            abort(403, 'Akses Ditolak');
        }

        // Hapus Data Turunan (Opsional, jika database tidak setting ON DELETE CASCADE)
        // 1. Hapus semua soal di ujian ini
        $exam->questions()->delete(); 
        // 2. Hapus semua sesi ujian (riwayat siswa mengerjakan)
        $exam->sessions()->delete();

        // Akhirnya hapus ujiannya
        $exam->delete();

        return redirect()->back()->with('success', 'Ujian dan semua datanya berhasil dihapus!');
    }

    // 8. Halaman Hasil / Rekap Nilai Siswa 
    public function examResults(Request $request, $id) // Tambah Request $request
    {
        $exam = Exam::findOrFail($id);

        // Security: Cek kepemilikan
        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        // A. SIAPKAN DATA FILTER (Dropdown)
        $allKelas = Kelas::all();
        // Ambil daftar angkatan unik dari tabel user
        $allAngkatan = User::where('role', 'siswa')
                        ->whereNotNull('angkatan')
                        ->select('angkatan')
                        ->distinct()
                        ->orderBy('angkatan', 'desc')
                        ->pluck('angkatan');

        // B. QUERY UTAMA (Ambil sesi ujian)
        $query = ExamSession::with(['user.kelas'])->where('exam_id', $id)->where('status', 'completed');

        // C. TERAPKAN LOGIKA FILTER
        // 1. Filter Kelas
        if ($request->filled('filter_kelas')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('kelas_id', $request->filter_kelas);
            });
        }

        // 2. Filter Angkatan
        if ($request->filled('filter_angkatan')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('angkatan', $request->filter_angkatan);
            });
        }

        // D. EKSEKUSI QUERY (Urutkan Nilai Tertinggi)
        $sessions = $query->orderBy('score', 'desc')->get();

        return view('dashboard.guru.results', compact('exam', 'sessions', 'allKelas', 'allAngkatan'));
    }

    // 9. Tampilkan Form Edit
    public function edit($id)
    {
        $exam = Exam::findOrFail($id);

        // Security: Cek kepemilikan
        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        return view('dashboard.guru.edit', compact('exam'));
    }

    // 10. Proses Update Data
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        $exam = Exam::findOrFail($id);
        
        // Security check
        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        // Update Data
        $exam->update([
            'title' => $request->title,
            'duration' => $request->duration,
            // Kita pakai teknik ternary untuk checkbox
            'random_question' => $request->has('random_question') ? 1 : 0,
        ]);

        return redirect()->route('dashboard.guru')->with('success', 'Ujian berhasil diperbarui!');
    }

    // 11. Reset Ujian Siswa (Remedial)
    public function resetExam($sessionId)
    {
        // Cari sesi ujian berdasarkan ID
        $session = ExamSession::with('exam')->findOrFail($sessionId);

        // Security: Pastikan ujian ini milik guru yang sedang login
        if ($session->exam->teacher_id != Auth::id()) {
            abort(403);
        }

        // Hapus jawaban siswa (detailnya)
        $session->answers()->delete();
        
        // Hapus sesi ujiannya (header)
        $session->delete();

        return redirect()->back()->with('success', 'Ujian siswa berhasil di-reset. Siswa bisa mengerjakan ulang.');
    }

    // 12. Reset Massal (Per Kelas / Per Angkatan)
    public function resetBulk(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        
        // Security Check
        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        // Mulai Query target yang mau di-reset
        $query = ExamSession::where('exam_id', $id);

        // SAFETY: Wajib pilih filter, jangan sampai reset semua tanpa sengaja
        if (!$request->filled('filter_kelas') && !$request->filled('filter_angkatan')) {
            return back()->with('error', 'Harap pilih Kelas atau Angkatan terlebih dahulu sebelum melakukan Reset Massal!');
        }

        // Terapkan Filter Kelas
        if ($request->filled('filter_kelas')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('kelas_id', $request->filter_kelas);
            });
        }

        // Terapkan Filter Angkatan
        if ($request->filled('filter_angkatan')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('angkatan', $request->filter_angkatan);
            });
        }

        // Hitung data
        $count = $query->count();

        if ($count == 0) {
            return back()->with('error', 'Tidak ada data siswa yang cocok untuk di-reset.');
        }

        // Eksekusi Hapus (Looping biar bersih sampai ke jawaban detail)
        $sessions = $query->get();
        foreach($sessions as $session) {
            $session->answers()->delete(); // Hapus detail jawaban
            $session->delete();            // Hapus sesi header
        }

        return back()->with('success', "Berhasil me-reset ujian milik $count siswa.");
    }

    // 13. Export Nilai ke Excel
    public function exportExcel(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        // Security Check
        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        // --- COPY LOGIKA FILTER DARI FUNGSI examResults ---
        // Supaya kalau guru sedang memfilter "Kelas A", yang terdownload juga cuma "Kelas A"
        
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
        // --- AKHIR LOGIKA FILTER ---

        // Teknik Header untuk memforce download file Excel
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Nilai_Ujian_{$exam->title}.xls");

        // Kita return view khusus yang isinya cuma TABEL saja (tanpa navbar, sidebar, dll)
        return view('dashboard.guru.export_excel', compact('exam', 'sessions'));
    }
}