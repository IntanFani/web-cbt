<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Menambahkan kolom kelas_id setelah teacher_id
            $table->unsignedBigInteger('kelas_id')->nullable()->after('teacher_id');
            
            // Opsional: Jika kamu punya tabel 'kelas', tambahkan foreign key agar data konsisten
            // $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('kelas_id');
        });
    }
};
