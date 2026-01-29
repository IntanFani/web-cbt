<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import class relasi
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Jawaban ini milik sesi ujian yang mana?
    // Karena nama kolom di database 'exam_session_id', nama fungsi sebaiknya 'examSession'
    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    // Relasi: Jawaban ini untuk soal yang mana?
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // Relasi: Jawaban ini memilih opsi yang mana? (Bisa null kalau essay)
    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}