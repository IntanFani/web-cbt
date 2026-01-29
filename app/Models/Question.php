<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import class relasi
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Soal ini masuk di ujian mana?
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    // Relasi: Soal ini punya opsi jawaban apa saja? (A, B, C, D)
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    // Relasi: Siapa saja yang sudah menjawab soal ini?
    // (Berguna buat fitur analisis butir soal nanti)
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}