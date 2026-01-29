<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Bikin Akun Guru
        $guru = User::create([
            'name' => 'Pak Budi Guru',
            'email' => 'guru@sekolah.com',
            'password' => Hash::make('password'), // passwordnya: password
            'role' => 'guru',
            'nis_nip' => '19800101',
        ]);

        // 2. Bikin Akun Siswa
        User::create([
            'name' => 'Fani Siswa',
            'email' => 'siswa@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'nis_nip' => '20230001',
        ]);

        // 3. Bikin 1 Ujian Matematika
        $exam = Exam::create([
            'teacher_id' => $guru->id,
            'title' => 'Ujian Akhir Semester - Matematika',
            'duration' => 90, // 90 menit
            'random_question' => true,
        ]);

        // 4. Bikin 5 Soal Dummy + Jawabannya
        $questions = [
            [
                'q' => 'Hasil dari 1 + 1 adalah...',
                'options' => [
                    ['text' => '2', 'correct' => true],
                    ['text' => '11', 'correct' => false],
                    ['text' => '3', 'correct' => false],
                    ['text' => '0', 'correct' => false],
                ]
            ],
            [
                'q' => 'Siapa penemu gaya gravitasi?',
                'options' => [
                    ['text' => 'Albert Einstein', 'correct' => false],
                    ['text' => 'Isaac Newton', 'correct' => true],
                    ['text' => 'Thomas Edison', 'correct' => false],
                    ['text' => 'Nikola Tesla', 'correct' => false],
                ]
            ],
            [
                'q' => 'Ibukota Indonesia saat ini adalah...',
                'options' => [
                    ['text' => 'Bandung', 'correct' => false],
                    ['text' => 'Surabaya', 'correct' => false],
                    ['text' => 'Jakarta', 'correct' => true],
                    ['text' => 'Medan', 'correct' => false],
                ]
            ],
            [
                'q' => 'Bahasa pemrograman untuk membuat web backend adalah...',
                'options' => [
                    ['text' => 'HTML', 'correct' => false],
                    ['text' => 'CSS', 'correct' => false],
                    ['text' => 'PHP', 'correct' => true],
                    ['text' => 'Photoshop', 'correct' => false],
                ]
            ],
            [
                'q' => 'Laravel adalah framework dari bahasa...',
                'options' => [
                    ['text' => 'Java', 'correct' => false],
                    ['text' => 'Python', 'correct' => false],
                    ['text' => 'PHP', 'correct' => true],
                    ['text' => 'JavaScript', 'correct' => false],
                ]
            ],
        ];

        foreach ($questions as $q) {
            $question = Question::create([
                'exam_id' => $exam->id,
                'question_text' => $q['q'],
                'type' => 'pilihan_ganda'
            ]);

            foreach ($q['options'] as $opt) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['correct']
                ]);
            }
        }
    }
}