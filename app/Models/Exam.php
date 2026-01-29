<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import class relasi
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi ke User (Guru) -> Pakai BelongsTo
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Relasi ke Soal -> Pakai HasMany
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
    
    // Relasi ke Sesi Ujian Siswa -> Pakai HasMany
    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }
}