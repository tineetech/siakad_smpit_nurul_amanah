<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Untuk timestamps

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $now = Carbon::now();

        $mataPelajaran = [
            // Mata Pelajaran Reguler
            [
                'nama' => 'IPA',
                'kode' => 'IPA001',
                'jenis' => 'reguler',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'IPS',
                'kode' => 'IPS001',
                'jenis' => 'reguler',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Matematika',
                'kode' => 'MTK001',
                'jenis' => 'reguler',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'B.Inggris',
                'kode' => 'BIG001',
                'jenis' => 'reguler',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'B.Indonesia',
                'kode' => 'BIN001',
                'jenis' => 'reguler',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'B.Sunda',
                'kode' => 'BSN001',
                'jenis' => 'reguler',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Seni Budaya',
                'kode' => 'SNB001',
                'jenis' => 'reguler',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'PAI', // Pendidikan Agama Islam
                'kode' => 'PAI001',
                'jenis' => 'reguler',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Mata Pelajaran Kepesantrenan
            [
                'nama' => 'Shorof',
                'kode' => 'SHR001',
                'jenis' => 'kepesantrenan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Nahwu',
                'kode' => 'NHW001',
                'jenis' => 'kepesantrenan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Al-Imla',
                'kode' => 'AIM001',
                'jenis' => 'kepesantrenan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Alquran Tahfidz',
                'kode' => 'AQT001',
                'jenis' => 'kepesantrenan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Thamrin Lughoh',
                'kode' => 'TML001',
                'jenis' => 'kepesantrenan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Al-Muthala\'ah',
                'kode' => 'AMT001',
                'jenis' => 'kepesantrenan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Hadist',
                'kode' => 'HDT001',
                'jenis' => 'kepesantrenan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insert data ke tabel mata_pelajaran
        DB::table('mata_pelajaran')->insert($mataPelajaran);
    }
}