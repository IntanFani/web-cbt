<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaAdminController;
use App\Http\Controllers\KelasController; // Pastikan ini di-import
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Route untuk Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'showLoginForm']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// 2. Route untuk Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// 3. Route Utama (Perlu Login)
Route::middleware('auth')->group(function () {

    // ==========================================
    // AREA SISWA
    // ==========================================
    Route::get('/dashboard/siswa', [SiswaController::class, 'index'])->name('dashboard.siswa');
    Route::get('/riwayat-ujian', [SiswaController::class, 'history'])->name('ujian.history');
    
    // Ujian Siswa
    Route::post('/ujian/{id}/start', [SiswaController::class, 'startExam'])->name('ujian.start');
    Route::get('/ujian/{id}/kerjakan', [SiswaController::class, 'showExam'])->name('ujian.show');
    Route::post('/ujian/simpan-jawaban', [SiswaController::class, 'saveAnswer'])->name('ujian.simpan');
    Route::post('/ujian/{id}/selesai', [SiswaController::class, 'finishExam'])->name('ujian.selesai');


    // ==========================================
    // AREA GURU
    // ==========================================
    
    // Dashboard Utama Guru (Statistik)
    Route::get('/dashboard/guru', [GuruController::class, 'index'])->name('dashboard.guru');

    // --- A. MANAJEMEN UJIAN ---
    // Halaman Daftar Ujian (BARU)
    Route::get('/ujian', [GuruController::class, 'manageExams'])->name('ujian.index');
    
    // CRUD Ujian
    Route::get('/ujian/buat', [GuruController::class, 'createExam'])->name('ujian.create');
    Route::post('/ujian/simpan', [GuruController::class, 'storeExam'])->name('ujian.store');
    Route::get('/ujian/{id}/edit', [GuruController::class, 'edit'])->name('ujian.edit');
    Route::put('/ujian/{id}', [GuruController::class, 'update'])->name('ujian.update');
    Route::delete('/ujian/{id}/hapus', [GuruController::class, 'deleteExam'])->name('ujian.delete');

    // --- B. KELOLA SOAL ---
    Route::get('/ujian/{id}/soal', [GuruController::class, 'manageQuestions'])->name('ujian.questions');
    Route::post('/ujian/{id}/soal', [GuruController::class, 'storeQuestion'])->name('questions.store');
    Route::delete('/soal/{id}', [GuruController::class, 'deleteQuestion'])->name('questions.delete');
    
    // Import Soal CSV (BARU)
    Route::post('/ujian/{id}/import-soal', [GuruController::class, 'importQuestions'])->name('ujian.importQuestions');

    // --- C. HASIL & LAPORAN ---
    Route::get('/ujian/{id}/hasil', [GuruController::class, 'examResults'])->name('ujian.results');
    Route::get('/ujian/{id}/export', [GuruController::class, 'exportExcel'])->name('ujian.export');

    // --- D. RESET NILAI ---
    // Reset Perorangan
    Route::delete('/ujian/reset/{id}', [GuruController::class, 'resetExam'])->name('ujian.reset');
    // Reset Massal (Per Kelas/Angkatan)
    Route::post('/ujian/{id}/reset-bulk', [GuruController::class, 'resetBulk'])->name('ujian.resetBulk');

    // --- E. MANAJEMEN KELAS ---
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.delete');
    // Kenaikan Kelas Massal (BARU)
    Route::post('/kelas/naik-kelas', [KelasController::class, 'promote'])->name('kelas.promote');

    // --- F. MANAJEMEN SISWA ---
    Route::get('/kelola-siswa', [SiswaAdminController::class, 'index'])->name('siswa.index');
    Route::get('/kelola-siswa/tambah', [SiswaAdminController::class, 'create'])->name('siswa.create');
    Route::post('/kelola-siswa', [SiswaAdminController::class, 'store'])->name('siswa.store');
    Route::get('/kelola-siswa/{id}/edit', [SiswaAdminController::class, 'edit'])->name('siswa.edit');
    Route::put('/kelola-siswa/{id}', [SiswaAdminController::class, 'update'])->name('siswa.update');
    Route::delete('/kelola-siswa/{id}', [SiswaAdminController::class, 'destroy'])->name('siswa.delete');
    Route::post('/siswa/import', [SiswaController::class, 'importExcel'])->name('siswa.import');

    // ==========================================
    // LOGIC REDIRECT
    // ==========================================
    Route::get('/home', function() {
        $user = Auth::user();
        if ($user->role === 'guru') {
            return redirect('/dashboard/guru');
        }
        return redirect('/dashboard/siswa');
    });

});