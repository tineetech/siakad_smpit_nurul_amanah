<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Tambahkan ini
use Carbon\Carbon;
use App\Models\User; // Tambahkan ini

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Data siswa mentah
        $siswaData = [
            // 5 Data Siswa Laki-laki
            [
                'nisn' => '0103213943',
                'nis' => null,
                'nama_lengkap' => 'Alvito Adianova',
                'jenis_kelamin' => 'laki-laki',
                'tempat_lahir' => 'Kab. Garut',
                'tanggal_lahir' => '2010-10-25',
                'agama' => 'Islam',
                'nama_ayah' => 'Bapak Adianova',
                'nama_ibu' => 'Ibu Adianova',
                'nomor_telepon_orang_tua' => '08111222333',
                'status' => 'aktif',
                'kelas_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nisn' => '0104705549',
                'nis' => null,
                'nama_lengkap' => 'Awan Maulana Fazri',
                'jenis_kelamin' => 'laki-laki',
                'tempat_lahir' => 'Cianjur',
                'tanggal_lahir' => '2010-12-01',
                'agama' => 'Islam',
                'nama_ayah' => 'Bapak Maulana',
                'nama_ibu' => 'Ibu Fazri',
                'nomor_telepon_orang_tua' => '08111222334',
                'status' => 'aktif',
                'kelas_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nisn' => '0109209369',
                'nis' => null,
                'nama_lengkap' => 'Fahsa Fardian Utami',
                'jenis_kelamin' => 'laki-laki',
                'tempat_lahir' => 'Garut',
                'tanggal_lahir' => '2010-11-11',
                'agama' => 'Islam',
                'nama_ayah' => 'Bapak Fardian',
                'nama_ibu' => 'Ibu Utami',
                'nomor_telepon_orang_tua' => '08111222335',
                'status' => 'aktif',
                'kelas_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nisn' => '0112775878',
                'nis' => null,
                'nama_lengkap' => 'Muhammad Agafi Joma Putra',
                'jenis_kelamin' => 'laki-laki',
                'tempat_lahir' => 'Garut',
                'tanggal_lahir' => '2011-05-19',
                'agama' => 'Islam',
                'nama_ayah' => 'Bapak Joma',
                'nama_ibu' => 'Ibu Agafi',
                'nomor_telepon_orang_tua' => '08111222336',
                'status' => 'aktif',
                'kelas_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nisn' => '01801030',
                'nis' => null,
                'nama_lengkap' => 'Reza Maulana Fauzi',
                'jenis_kelamin' => 'laki-laki',
                'tempat_lahir' => 'Tasikmalaya',
                'tanggal_lahir' => '2011-06-24',
                'agama' => 'Islam',
                'nama_ayah' => 'Bapak Fauzi',
                'nama_ibu' => 'Ibu Reza',
                'nomor_telepon_orang_tua' => '08111222337',
                'status' => 'aktif',
                'kelas_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // 5 Data Siswa Perempuan
            [
                'nisn' => '0102375627',
                'nis' => null,
                'nama_lengkap' => 'Elsa Rahmadani',
                'jenis_kelamin' => 'perempuan',
                'tempat_lahir' => 'Tasikmalaya',
                'tanggal_lahir' => '2010-08-31',
                'agama' => 'Islam',
                'nama_ayah' => 'Bapak Rahmadani',
                'nama_ibu' => 'Ibu Elsa',
                'nomor_telepon_orang_tua' => '08111222338',
                'status' => 'aktif',
                'kelas_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nisn' => '010840234',
                'nis' => null,
                'nama_lengkap' => 'Greysia Sakha Muhammad Sabrani',
                'jenis_kelamin' => 'perempuan',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2010-10-27',
                'agama' => 'Islam',
                'nama_ayah' => 'Bapak Muhammad',
                'nama_ibu' => 'Ibu Sabrani',
                'nomor_telepon_orang_tua' => '08111222339',
                'status' => 'aktif',
                'kelas_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nisn' => '0113378430',
                'nis' => null,
                'nama_lengkap' => 'MUHAMMAD FARUG AL-MUTAALI',
                'jenis_kelamin' => 'perempuan',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2011-01-15',
                'agama' => 'Islam',
                'nama_ayah' => 'Bapak Faruq',
                'nama_ibu' => 'Ibu Mutaali',
                'nomor_telepon_orang_tua' => '08111222340',
                'status' => 'aktif',
                'kelas_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nisn' => '01033423',
                'nis' => null,
                'nama_lengkap' => 'Nazla Farhatul Aisy',
                'jenis_kelamin' => 'perempuan',
                'tempat_lahir' => 'Tasikmalaya',
                'tanggal_lahir' => '2010-10-20',
                'agama' => 'Islam',
                'nama_ayah' => 'Bapak Aisy',
                'nama_ibu' => 'Ibu Nazla',
                'nomor_telepon_orang_tua' => '08111222341',
                'status' => 'aktif',
                'kelas_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nisn' => '011688926',
                'nis' => null,
                'nama_lengkap' => 'Rahma Nafilatusania',
                'jenis_kelamin' => 'perempuan',
                'tempat_lahir' => 'Tasikmalaya',
                'tanggal_lahir' => '2011-06-20',
                'agama' => 'Islam',
                'nama_ayah' => 'Bapak Nafilatusania',
                'nama_ibu' => 'Ibu Rahma',
                'nomor_telepon_orang_tua' => '08111222342',
                'status' => 'aktif',
                'kelas_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Memproses setiap data siswa untuk membuat user dan memperbarui qr_code_data
        foreach ($siswaData as $key => $siswa) {
            // 1. Buat data user
            $namaLengkapBersih = strtolower(str_replace(' ', '', $siswa['nama_lengkap']));
            $email = $namaLengkapBersih . '@gmail.com';
            $password = "siswa123";

            $user = User::updateOrCreate(
                ['email' => $email], // Cari berdasarkan email
                [
                    'name' => $siswa['nama_lengkap'],
                    'password' => Hash::make($password),
                    'role' => 'siswa', // Sesuaikan dengan enum role di tabel users Anda
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            // 2. Kaitkan user_id ke data siswa
            $siswaData[$key]['user_id'] = $user->id;

            // 3. Perbarui qr_code_data dengan user_id yang sebenarnya
            $siswaData[$key]['qr_code_data'] = json_encode([
                'user_id' => $user->id,
                'nisn' => $siswa['nisn'],
                'nama' => $siswa['nama_lengkap'],
                'kelas_id' => $siswa['kelas_id'], // Masih null jika belum ada kelas yang terkait
            ]);
        }

        // Memasukkan data siswa yang sudah diperbarui ke tabel 'siswa'
        DB::table('siswa')->insert($siswaData);
    }
}