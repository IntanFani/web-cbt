<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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

    public function kelas()
    {
        // Ujian ini dimiliki oleh satu kelas
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function import(Request $request, Exam $exam)
    {
        // 1. Validasi File
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        // 2. Buka File CSV
        $file = $request->file('file');
        $fileHandle = fopen($file->getRealPath(), 'r');

        // Lewati baris pertama (Header: soal, opsi_a, dst)
        fgetcsv($fileHandle);

        // 3. Loop baris demi baris
        while (($row = fgetcsv($fileHandle)) !== false) {
            // Struktur Row berdasarkan CSV yang saya buat tadi:
            // [0] => Soal
            // [1] => Opsi A
            // [2] => Opsi B
            // [3] => Opsi C
            // [4] => Opsi D
            // [5] => Kunci (A/B/C/D)

            // Pastikan baris tidak kosong
            if (count($row) < 6) continue; 

            // Simpan Soal
            $question = $exam->questions()->create([
                'question_text' => $row[0],
                'type' => 'pilihan_ganda', // Default type
            ]);

            // Siapkan Array Opsi dari CSV
            $optionsData = [
                $row[1], // Index 0 (A)
                $row[2], // Index 1 (B)
                $row[3], // Index 2 (C)
                $row[4]  // Index 3 (D)
            ];

            // Ambil Kunci Jawaban (Bersihkan spasi & jadikan huruf besar)
            // Contoh: " a " menjadi "A"
            $correctKey = strtoupper(trim($row[5])); 

            // Mapping Huruf ke Index Array
            $keyMapping = [
                'A' => 0,
                'B' => 1,
                'C' => 2,
                'D' => 3
            ];

            // Cek index mana yang benar berdasarkan huruf kunci
            // Jika kunci tidak valid (misal 'E'), default ke -1 (tidak ada yang benar)
            $correctIndex = $keyMapping[$correctKey] ?? -1;

            // Loop untuk menyimpan 4 opsi jawaban
            foreach ($optionsData as $index => $optionText) {
                $question->options()->create([
                    'option_text' => $optionText,
                    // PERBAIKAN UTAMA DISINI:
                    // Bandingkan index saat ini dengan index kunci jawaban
                    'is_correct' => ($index === $correctIndex) ? 1 : 0
                ]);
            }
        }

        fclose($fileHandle);

        return redirect()->back()->with('success', 'Soal berhasil diimport!');
    }
}