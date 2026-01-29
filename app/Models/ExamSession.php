<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import class relasi yang dibutuhkan
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Sesi ujian ini milik siapa? (Siswa/User)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Sesi ini mengerjakan ujian apa?
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    // Relasi: Sesi ini punya jawaban apa saja?
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}