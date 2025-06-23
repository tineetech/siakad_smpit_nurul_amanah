<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enrollment', function (Blueprint $table) {
            $table->id();
            // Kolom untuk nama enrollment, opsional sesuai permintaan
            $table->string('nama')->nullable()->comment('Nama deskriptif untuk enrollment (opsional)');

            // Foreign keys ke tabel utama
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->foreignId('kurikulum_id')->constrained('kurikulum')->onDelete('cascade');

            // Foreign keys untuk siswa atau guru (salah satu harus diisi)
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained('guru')->onDelete('cascade');

            $table->timestamps();

            // Opsional: Jika Anda ingin memastikan kombinasi unik (misal: 1 siswa hanya bisa di 1 kelas per semester per kurikulum)
            // Namun, untuk fleksibilitas, saya tidak akan menambahkan ini secara default
            // $table->unique(['kelas_id', 'siswa_id', 'semester_id', 'kurikulum_id'], 'unique_student_enrollment');
            // $table->unique(['kelas_id', 'guru_id', 'semester_id', 'kurikulum_id'], 'unique_teacher_enrollment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment');
    }
};