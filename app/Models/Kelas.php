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
}