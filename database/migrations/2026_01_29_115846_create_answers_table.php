<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            // option_id boleh null jika siswa belum jawab atau soal essay
            $table->foreignId('option_id')->nullable()->constrained('options')->onDelete('cascade'); 
            $table->text('essay_answer')->nullable(); // Jaga-jaga kalau mau fitur essay
            $table->boolean('is_correct')->nullable(); // Hasil koreksi sistem
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
