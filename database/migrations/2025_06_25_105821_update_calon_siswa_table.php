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
     
        Schema::dropIfExists('calon_siswa');   
        Schema::create('calon_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelombang_id')->constrained('gelombang')->onDelete('cascade');
            $table->string('nomor_pendaftaran', 50)->unique();
            $table->string('nisn')->nullable();
            $table->string('nis')->nullable();
            $table->string('nama_lengkap', 255);
            $table->string('profile_picture')->default('images/pp/default.jpg')->nullable();
            $table->string('surat_kelulusan')->nullable();
            $table->string('akta_kelahiran')->nullable();
            $table->string('kartu_keluarga')->nullable();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nomor_hp_siswa', 20)->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->integer('anak_ke')->nullable();
            $table->integer('jumlah_saudara')->nullable();
            $table->string('cita_cita')->nullable();
            $table->string('hobi')->nullable();
            $table->float('berat_badan')->nullable();
            $table->float('tinggi_badan')->nullable();
            $table->text('riwayat_penyakit')->nullable();

            // Informasi Ayah
            $table->string('nama_ayah')->nullable();
            $table->string('status_ayah')->nullable(); // contoh: hidup, wafat
            $table->string('tempat_lahir_ayah')->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('penghasilan_ayah')->nullable();
            $table->string('nomor_hp_ayah', 20)->nullable();

            // Informasi Ibu
            $table->string('nama_ibu')->nullable();
            $table->string('status_ibu')->nullable();
            $table->string('tempat_lahir_ibu')->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('penghasilan_ibu')->nullable();
            $table->string('nomor_hp_ibu', 20)->nullable();

            // Kontak dan status pendaftaran
            $table->timestamp('tanggal_pendaftaran');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('disetujui_oleh_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
