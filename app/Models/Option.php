<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import class relasi
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Opsi ini milik soal yang mana?
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}