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
        Schema::dropIfExists('pembayaran_spp');
        Schema::dropIfExists('penetapan_spp_siswa');
        Schema::dropIfExists('pengaturan_spp');
        Schema::dropIfExists('absensi_guru');
        Schema::dropIfExists('absensi_siswa');
        Schema::dropIfExists('jadwal_pelajaran');
        Schema::dropIfExists('pengumuman');
        Schema::dropIfExists('gelombang');
        Schema::dropIfExists('staf');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('kurikulum');
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('semester');
        Schema::dropIfExists('users');

        // --- PENGGUNA & PERAN ---
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('password');
            $table->string('email')->unique()->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->enum('role', ['admin', 'siswa', 'guru', 'tata_usaha', 'staff_ppdb', 'staff']);
            $table->string('profile_picture')->default('images/pp/default.jpg')->nullable();
            $table->timestamps();
        });

        // --- DATA MASTER ---
        Schema::create('semester', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // e.g., 'Ganjil 2024/2025', 'Genap 2024/2025'
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->boolean('is_aktif')->default(false);
            $table->timestamps();
        });

        Schema::create('enrollment', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->foreignId('kelas_id')->nullable()->unique()->constrained('kelas')->onDelete('cascade');
            $table->foreignId('siswa_id')->nullable()->unique()->constrained('siswa')->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained('guru')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->unique()->constrained('semester')->onDelete('cascade');
            $table->foreignId('kurikulum_id')->nullable()->unique()->constrained('kurikulum')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // e.g., 'X IPA 1', 'XI IPS 2'
            $table->foreignId('guru_id')->nullable()->unique()->constrained('siswa')->onDelete('cascade');
            $table->string('tingkat')->nullable();
            $table->integer('kapasitas')->default(35)->nullable();
            $table->timestamps();
        });

        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255)->unique(); // e.g., 'Matematika', 'Sejarah', 'Fiqih'
            $table->string('kode', 50)->unique()->nullable();
            $table->string('jenis', 50)->nullable(); // e.g., 'reguler', 'kepesantrenan'
            $table->timestamps();
        });

        Schema::create('kurikulum', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255)->unique(); // e.g., 'Kurikulum Nasional 2013'
            $table->text('deskripsi')->nullable();
            $table->integer('tahun_mulai')->nullable();
            $table->integer('tahun_berakhir')->nullable();
            $table->boolean('is_aktif')->default(false);
            $table->timestamps();
        });

        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('set null'); // BISA NULL
            $table->string('nisn')->unique();
            $table->string('nis')->unique()->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nomor_telepon_orang_tua')->nullable();
            $table->string('qr_code_data')->nullable(); // Data untuk QR code
            $table->enum('status', ['aktif', 'non-aktif', 'lulus'])->default('aktif'); // aktif, non-aktif, lulus
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null'); // ID kelas utama
            $table->timestamps();
        });

        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('set null'); // BISA NULL
            $table->string('nip')->unique()->nullable();
            $table->string('nama_lengkap', 255);
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('qr_code_data')->nullable(); // Data untuk QR code
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif'); // aktif, non-aktif
            $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajaran')->onDelete('set null');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null'); // ID kelas jika dia seorang wali kelas
            $table->timestamps();
        });

        Schema::create('staf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade'); // User ID staf akan selalu ada
            $table->string('nip')->unique()->nullable();
            $table->string('nama_lengkap', 255);
            $table->string('jabatan')->nullable();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->timestamps();
        });

        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->text('konten');
            $table->foreignId('diposting_oleh_user_id')->constrained('users')->onDelete('cascade');
            $table->string('target_peran')->nullable(); // e.g., 'semua', 'siswa', 'guru', 'tata_usaha'
            $table->timestamp('tanggal_publikasi')->nullable();
            $table->timestamps();
        });

        // --- MANAJEMEN KELAS & JADWAL ---
        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained('guru')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->integer('hari'); // 1=Senin, 7=Minggu
            $table->foreignId('kurikulum_id')->nullable()->constrained('kurikulum')->onDelete('set null');
            $table->timestamps();
        });

        // --- MANAJEMEN ABSENSI ---
        Schema::create('absensi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->date('tanggal_absensi');
            $table->time('waktu_absensi'); // Waktu absensi dilakukan
            $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alpha']); // e.g., 'Hadir', 'Izin', 'Sakit', 'Alpha'
            $table->enum('mode_absensi', ['scan_qr', 'manual'])->default('manual'); // 'scan_qr', 'manual'
            $table->foreignId('pencatat_user_id')->nullable()->constrained('users')->onDelete('set null'); // NULLable jika scan_qr otomatis
            $table->string('qr_code_terscan', 255)->nullable(); // Data QR code yang discan (NULL jika manual)
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('absensi_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->date('tanggal_absensi');
            $table->time('waktu_absensi'); // Waktu absensi dilakukan
            $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alpha']); // e.g., 'Hadir', 'Izin', 'Sakit', 'Alpha'
            $table->enum('mode_absensi', ['scan_qr', 'manual'])->default('manual'); // 'scan_qr', 'manual'
            $table->foreignId('pencatat_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('qr_code_terscan', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // --- MANAJEMEN SPP ---
        Schema::create('pengaturan_spp', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255); // e.g., 'SPP Tahun Ajaran 2024/2025'
            $table->decimal('jumlah'); // Jumlah default
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->timestamps();
        });

        Schema::create('penetapan_spp_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('pengaturan_spp_id')->constrained('pengaturan_spp')->onDelete('cascade');
            $table->enum('status', ['belum_dibayar', 'lunas', 'sebagian_dibayar', 'jatuh_tempo'])->default('belum_dibayar'); // 'belum_dibayar', 'lunas', 'sebagian_dibayar', 'jatuh_tempo'
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->timestamps();
        });

        Schema::create('pembayaran_spp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('penetapan_spp_id')->nullable()->constrained('penetapan_spp_siswa')->onDelete('set null'); // NULLable jika pembayaran umum
            $table->decimal('jumlah_dibayar', 10, 2);
            $table->timestamp('tanggal_pembayaran');
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'qris'])->nullable(); // e.g., 'tunai', 'transfer', 'QRIS'
            $table->foreignId('teller_user_id')->constrained('users')->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // --- PPDB (Penerimaan Peserta Didik Baru) ---
        Schema::create('gelombang', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255); // e.g., 'SPP Tahun Ajaran 2024/2025'
            $table->integer('kouta')->default(0);
            $table->integer('kouta_terisi')->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('calon_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelombang_id')->constrained('gelombang')->onDelete('cascade');
            $table->string('nomor_pendaftaran', 50)->unique();
            $table->string('nisn')->nullable(); // Tambahan: NISN calon siswa
            $table->string('nis')->nullable(); // Tambahan: NIS calon siswa
            $table->string('nama_lengkap', 255);
            $table->string('profile_picture')->default('images/pp/default.jpg')->nullable();
            $table->string('surat_kelulusan')->nullable();
            $table->string('akta_kelahiran')->nullable();
            $table->string('kartu_keluarga')->nullable();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama_orang_tua', 255)->nullable();
            $table->string('nomor_telepon_orang_tua', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->timestamp('tanggal_pendaftaran');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu'); // 'menunggu', 'disetujui', 'ditolak'
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
        // Urutan drop tabel harus terbalik dari urutan create karena ada foreign key
        Schema::dropIfExists('calon_siswa');
        Schema::dropIfExists('pembayaran_spp');
        Schema::dropIfExists('penetapan_spp_siswa');
        Schema::dropIfExists('pengaturan_spp');
        Schema::dropIfExists('absensi_guru');
        Schema::dropIfExists('absensi_siswa');
        Schema::dropIfExists('jadwal_pelajaran');
        Schema::dropIfExists('pengumuman');
        Schema::dropIfExists('staf');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('kurikulum');
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('semester');
        Schema::dropIfExists('users');
    }
};