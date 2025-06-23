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
        Schema::table('kelas', function (Blueprint $table) {
            // Tambahkan kolom guru_id setelah 'kapasitas'
            $table->foreignId('guru_id')
                  ->nullable()
                  ->constrained('guru') // Merujuk ke tabel 'guru'
                  ->onDelete('set null') // Jika guru dihapus, set ID ini menjadi null
                  ->after('kapasitas'); // Posisi kolom
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('guru_id'); // Menghapus foreign key constraint
            $table->dropColumn('guru_id'); // Menghapus kolom
        });
    }
};