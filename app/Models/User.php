<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['id'];

    // Relasi 1: Guru punya banyak ujian
    public function exams() {
        return $this->hasMany(Exam::class, 'teacher_id');
    }

    // Relasi 2: Siswa masuk ke satu Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // ==========================================
    // TAMBAHAN PENTING (SOLUSI ERROR)
    // ==========================================
    
    // Relasi 3: Siswa memiliki banyak riwayat ujian (ExamSession)
    // Fungsi inilah yang dipanggil oleh withAvg('examSessions', ...) di Controller/View
    public function examSessions()
    {
        return $this->hasMany(ExamSession::class, 'user_id');
    }
}