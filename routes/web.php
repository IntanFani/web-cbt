<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaAdminController;
use Illuminate\Support\Facades\Auth;


// 1. Route untuk Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'showLoginForm']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// 2. Route untuk Logout (Harus bisa diakses yang sudah login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// 3. Route untuk User yang Sudah Login (GURU & SISWA)
Route::middleware('auth')->group(function () {
    
    // Dashboard Guru (Masih Dummy)
    Route::get('/dashboard/guru', function () {
        return 'Halo Pak Guru! Ini Dashboard Guru.';
    });
    
    // Dashboard Siswa (SUDAH BENAR & AMAN)
    // Kita pindahkan ke dalam middleware 'auth' supaya tidak bisa diakses tanpa login
    Route::get('/dashboard/siswa', [SiswaController::class, 'index'])->name('dashboard.siswa');

    // Route untuk Trigger Mulai Ujian (Create Session)
    Route::get('/ujian/{id}/start', [SiswaController::class, 'startExam'])->name('ujian.start');
    
    // Route Halaman Pengerjaan Soal
    Route::get('/ujian/{id}/kerjakan', [SiswaController::class, 'showExam'])->name('ujian.show');

    // ... route ujian lainnya ...
    Route::post('/ujian/simpan-jawaban', [SiswaController::class, 'saveAnswer'])->name('ujian.simpan');

    // Route untuk Finish Ujian
    Route::post('/ujian/{id}/selesai', [SiswaController::class, 'finishExam'])->name('ujian.selesai');

    // === ROUTE DASHBOARD GURU ===
    // 1. Halaman Utama Dashboard
    Route::get('/dashboard/guru', [GuruController::class, 'index'])->name('dashboard.guru');
    
    // 2. Halaman Buat Ujian
    Route::get('/ujian/buat', [GuruController::class, 'createExam'])->name('ujian.create');
    
    // 3. Proses Simpan Ujian
    Route::post('/ujian/simpan', [GuruController::class, 'storeExam'])->name('ujian.store');

    // 4. Kelola Soal
    Route::get('/ujian/{id}/soal', [GuruController::class, 'manageQuestions'])->name('ujian.questions');
    Route::post('/ujian/{id}/soal', [GuruController::class, 'storeQuestion'])->name('questions.store');
    Route::delete('/soal/{id}', [GuruController::class, 'deleteQuestion'])->name('questions.delete');

    // 5. Route Hapus Ujian
    Route::delete('/ujian/{id}/hapus', [GuruController::class, 'deleteExam'])->name('ujian.delete');

    // 6. Route Lihat Hasil Siswa
    Route::get('/ujian/{id}/hasil', [GuruController::class, 'examResults'])->name('ujian.results');
    Route::get('/ujian/{id}/export', [GuruController::class, 'exportExcel'])->name('ujian.export');

    // 7. Route Edit Ujian
    Route::get('/ujian/{id}/edit', [GuruController::class, 'edit'])->name('ujian.edit');
    Route::put('/ujian/{id}', [GuruController::class, 'update'])->name('ujian.update');

    // 8. Route Reset Ujian Siswa
    Route::delete('/ujian/reset/{id}', [GuruController::class, 'resetExam'])->name('ujian.reset');

    // Route Reset Massal (Per Kelas/Angkatan)
    Route::post('/ujian/{id}/reset-bulk', [GuruController::class, 'resetBulk'])->name('ujian.resetBulk');

    // Route Manajemen Kelas
    Route::get('/kelas', [App\Http\Controllers\KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [App\Http\Controllers\KelasController::class, 'store'])->name('kelas.store');
    Route::delete('/kelas/{id}', [App\Http\Controllers\KelasController::class, 'destroy'])->name('kelas.delete');

    // === MANAJEMEN SISWA ===
    Route::get('/kelola-siswa', [SiswaAdminController::class, 'index'])->name('siswa.index');
    Route::get('/kelola-siswa/tambah', [SiswaAdminController::class, 'create'])->name('siswa.create');
    Route::post('/kelola-siswa', [SiswaAdminController::class, 'store'])->name('siswa.store');
    Route::get('/kelola-siswa/{id}/edit', [SiswaAdminController::class, 'edit'])->name('siswa.edit');
    Route::put('/kelola-siswa/{id}', [SiswaAdminController::class, 'update'])->name('siswa.update');
    Route::delete('/kelola-siswa/{id}', [SiswaAdminController::class, 'destroy'])->name('siswa.delete');

    // Rute Penyelamat (Redirect Loop Fix)
    // Kalau user login maksa buka halaman login, dilempar ke /home, lalu kita tangkap di sini
    Route::get('/home', function() {
        $user = Auth::user();
        if ($user->role === 'guru') {
            return redirect('/dashboard/guru');
        }
        return redirect('/dashboard/siswa');
    });
});