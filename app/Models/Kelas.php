<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    // Supaya nama tabelnya dikenali
    protected $table = 'kelas'; 
    protected $guarded = [];

    // Satu kelas punya banyak siswa
    public function students()
    {
        return $this->hasMany(User::class, 'kelas_id');
    }

    protected static function booted()
    {
        static::deleting(function ($kelas) {
            // Saat kelas dihapus, hapus semua siswa yang kelas_id-nya sama
            $kelas->users()->delete();
        });
    }

    // relasi ke user
    public function users()
    {
        return $this->hasMany(User::class, 'kelas_id');
    }

    // relasi ke ujian
    public function exams()
    {
        return $this->hasMany(Exam::class, 'kelas_id');
    }
}