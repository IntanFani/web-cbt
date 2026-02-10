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
use Illuminate\Support\Facades\Storage;

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
        // KIRIM VARIABEL CLASSES AGAR MODAL TIDAK ERROR
        $classes = Kelas::all(); 
        
        return view('dashboard.guru.ujian.index', compact('exams', 'classes'));
    }

    // B. Halaman Buat Ujian Baru
    public function createExam()
    {
        $classes = Kelas::all();
        // Path View Baru: dashboard.guru.ujian.create
        return view('dashboard.guru.ujian.create');
    }

    // C. Proses Simpan Ujian
    public function storeExam(Request $request)
    {
        $request->validate([
        'title' => 'required',
        'kelas_id' => 'required', // Validasi kelas wajib diisi
        'duration' => 'required|numeric',
        ]);

        $exam = Exam::create([
            'teacher_id' => Auth::id(),
            'kelas_id' => $request->kelas_id,
            'title' => $request->title,
            'duration' => $request->duration,
            'token' => strtoupper(Str::random(5)), // Generate token otomatis
        ]);

       return redirect()->route('ujian.questions', $exam->id)->with('success', 'Ujian berhasil dibuat! Silakan tambah soal.');
    }

    // D. Halaman Edit Ujian
    public function edit($id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        $classes = Kelas::all();

        // Path View Baru: dashboard.guru.ujian.edit
        return view('dashboard.guru.ujian.edit', compact('exam','classes'));
    }

    // E. Proses Update Ujian
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'duration' => 'required|integer|min:1',
            'kelas_id' => 'required'
        ]);

        $exam = Exam::findOrFail($id);
        
        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        $exam->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'kelas_id' => $request->kelas_id,
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
        // 1. Validasi disesuaikan dengan kolom database 'image' dan tipe 'essay'
        $request->validate([
            'question_text' => 'required',
            'type' => 'required|in:pilihan_ganda,benar_salah,essay', // disesuaikan dengan enum database
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
            'options' => 'required_if:type,pilihan_ganda|array',
            'correct_answer' => 'required_if:type,pilihan_ganda|integer',
            'correct_answer_bs' => 'required_if:type,benar_salah|in:1,0', // 1 untuk Benar, 0 untuk Salah
        ]);

        $exam = Exam::findOrFail($id);
        if($exam->teacher_id != Auth::id()) { abort(403); }

        // 2. Handle Upload Gambar (Nama kolom database: image)
        $imagePath = null;
        if ($request->hasFile('question_image')) {
            // Menyimpan di folder 'questions' dalam disk public
            $imagePath = $request->file('question_image')->store('questions', 'public');
        }

        // 3. Buat Soal (Nama kolom sesuai migration: image & type)
        $question = Question::create([
            'exam_id' => $id,
            'question_text' => $request->question_text,
            'image' => $imagePath, // Sesuai kolom migration kamu
            'type' => $request->type
        ]);

        // 4. Logika Penyimpanan Opsi berdasarkan Tipe
        if ($request->type == 'pilihan_ganda') {
            foreach ($request->options as $index => $optionText) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => ($index == $request->correct_answer)
                ]);
            }
        } 
        elseif ($request->type == 'benar_salah') {
            // Simpan dua opsi standar: Benar dan Salah
            // Opsi Benar
            Option::create([
                'question_id' => $question->id,
                'option_text' => 'Benar',
                'is_correct' => ($request->correct_answer_bs == "1")
            ]);
            // Opsi Salah
            Option::create([
                'question_id' => $question->id,
                'option_text' => 'Salah',
                'is_correct' => ($request->correct_answer_bs == "0")
            ]);
        }
        // Jika tipe 'essay', tabel options tetap kosong sesuai rencana

        return redirect()->back()->with('success', 'Soal berhasil ditambahkan!');
    }

    // C. Hapus Soal (Update: Hapus File Gambar dari Storage)
    public function deleteQuestion($id)
    {
        $question = Question::findOrFail($id);
        
        if($question->exam->teacher_id != Auth::id()) { abort(403); }

        // Hapus file gambar jika ada sebelum record dihapus
        if ($question->question_image) {
            Storage::disk('public')->delete($question->question_image);
        }

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

        if ($exam->teacher_id != Auth::id()) {
            abort(403);
        }

        $file = $request->file('file');
        DB::beginTransaction();

        try {
            if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
                
                // 1. LEWATI HEADER
                fgetcsv($handle); 

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Validasi minimal kolom (Soal, Tipe, Opsi1, Opsi2, Opsi3, Opsi4, Jawaban)
                    if (count($data) < 7) continue; 

                    $questionText = $data[0];
                    $type = strtolower(trim($data[1])); // Kolom 2: type (pilihan_ganda, benar_salah, essay)

                    // A. Buat Soal
                    $question = Question::create([
                        'exam_id' => $exam->id,
                        'question_text' => $questionText,
                        'type' => $type
                    ]);

                    // B. Logika berdasarkan Tipe
                    if ($type == 'pilihan_ganda') {
                        $correctKey = strtoupper(trim($data[6])); // Kolom 7: Jawaban (A/B/C/D)
                        $keyMap = ['A' => 2, 'B' => 3, 'C' => 4, 'D' => 5]; // Mapping ke kolom index CSV
                        $correctIndex = $keyMap[$correctKey] ?? 0;

                        for ($i = 2; $i <= 5; $i++) {
                            Option::create([
                                'question_id' => $question->id,
                                'option_text' => $data[$i],
                                'is_correct' => ($i == $correctIndex) ? 1 : 0 
                            ]);
                        }

                    } elseif ($type == 'benar_salah') {
                        // Ambil jawaban: "Benar" atau "Salah" (ada di kolom index 6)
                        $answerBS = ucfirst(strtolower(trim($data[6]))); 
                        
                        $optionsBS = ['Benar', 'Salah'];
                        foreach ($optionsBS as $optText) {
                            Option::create([
                                'question_id' => $question->id,
                                'option_text' => $optText,
                                'is_correct' => ($optText == $answerBS) ? 1 : 0
                            ]);
                        }

                    } elseif ($type == 'essay') {
                        // Untuk essay, biasanya tidak ada opsi yang disimpan di tabel options
                        // atau bisa dikosongkan saja.
                    }
                }
                fclose($handle);
            }

            DB::commit();
            return back()->with('success', 'Import soal berbagai tipe berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
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