<?php

namespace App\Filament\Resources\CalonSiswaResource\Pages;

use App\Filament\Resources\CalonSiswaResource;
use App\Models\Gelombang;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Users;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class EditCalonSiswa extends EditRecord
{
    protected static string $resource = CalonSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $token = "sdXpWQ9yXK62kdwKGrgv";

        $calonSiswa = $this->record;
        $existSiswa = Siswa::where('nisn', $calonSiswa->nisn)->first();

        if ($calonSiswa->status === "disetujui" && !$existSiswa) {
            // Kirim notif calon siswa diterima via wa
            $target = $calonSiswa->nomor_hp_siswa ?? $calonSiswa->nomor_hp_ibu;
            $findGelombang = Gelombang::where('id', $calonSiswa->gelombang_id)->first();
            $tgl = date('Y-m-d H:i:s');
            $waktuDiterima = $calonSiswa->tanggal_persetujuan ?? $tgl;
            $message = "
📢 *PENGUMUMAN PENERIMAAN SISWA BARU*

Assalamu'alaikum, Halo *{$calonSiswa->nama_lengkap}* 👋

🎉 *SELAMAT!* Kamu telah *DITERIMA* sebagai siswa di:
🏫 *SMP IT Nurul Amanah*

Berikut detail informasi kamu:
────────────────────────
👤 Nama Lengkap : *{$calonSiswa->nama_lengkap}*
🆔 NISN         : *{$calonSiswa->nisn}*
📅 Gelombang    : *{$findGelombang->nama}*
⏰ Diterima     : *{$waktuDiterima}*
────────────────────────

Sehubungan dengan telah diterimanya putra/putri Bapak/Ibu dalam proses pendaftaran, kami mengundang Bapak/Ibu untuk hadir dalam kegiatan wawancara orang tua yang merupakan bagian dari tahapan seleksi berikutnya pada hari senin minggu ini/depan.

📞 Hubungi admin jika ada pertanyaan.
Terima kasih 🙏

Wassalamu'alaikum warahmatullahi wabarakatuh
";

            $this->kirimFonnte($token, [
                'target' => $target,
                'message' => $message,
            ]);

            $namaDepan = explode(' ', $calonSiswa->nama_lengkap)[0];
            $namaLengkapBersih = strtolower($namaDepan);
            $email = $namaLengkapBersih . '@gmail.com';
            // $password = $siswa->nisn;
            $password = "siswa123";
            
            $createLoginSiswa = Users::create([
                'name' => $calonSiswa->nama_lengkap,
                'email' => $email,
                'password' => Hash::make($password),
                'phone_number' => $calonSiswa->nomor_hp_siswa,
                'address' => $calonSiswa->alamat,
                'role' => User::ROLE_SISWA,
                'profile_picture' => $calonSiswa->profile_picture ?? null,
            ]);
            
            $qrData = json_encode([
                'user_id' => $createLoginSiswa->id,
                'nisn' => $calonSiswa->nisn,
                'nama' => $calonSiswa->nama_lengkap,
                'kelas_id' => null,
            ]);

            Siswa::create([
                'user_id' => $createLoginSiswa->id,
                'nisn' => $calonSiswa->nisn,
                'nis' => $calonSiswa->nis,
                'nama_lengkap' => $calonSiswa->nama_lengkap,
                'jenis_kelamin' => $calonSiswa->jenis_kelamin,
                'tempat_lahir' => $calonSiswa->tempat_lahir,
                'tanggal_lahir' => $calonSiswa->tanggal_lahir,
                'agama' => "Islam",
                'nama_ayah' => $calonSiswa->nama_ayah,
                'nama_ibu' => $calonSiswa->nama_ibu,
                'nomor_telepon_orang_tua' => $calonSiswa->nomor_hp_ayah ?? $calonSiswa->nomor_hp_ibu,
                'qr_code_data' => $qrData ?? null,
                'status' => 'aktif',
                'kelas_id' => null, 
            ]);

            
        } else if ($calonSiswa->status === "ditolak" && !$existSiswa) {
            // Kirim notif calon siswa ditolak via wa
            $target = $calonSiswa->nomor_hp_siswa ?? $calonSiswa->nomor_hp_ibu;
            $findGelombang = Gelombang::where('id', $calonSiswa->gelombang_id)->first();
            $tgl = date('Y-m-d H:i:s');
            $waktuDiterima = $calonSiswa->tanggal_persetujuan ?? $tgl;
            $message = "
📢 *PENGUMUMAN PENERIMAAN SISWA BARU*

Assalamu'alaikum, Halo *{$calonSiswa->nama_lengkap}* 👋

🔔 *MAAF!* Kamu telah *DITOLAK* sebagai siswa di:
🏫 *SMP IT Nurul Amanah*

Berikut detail informasi kamu:
────────────────────────
👤 Nama Lengkap : *{$calonSiswa->nama_lengkap}*
🆔 NISN         : *{$calonSiswa->nisn}*
📅 Gelombang    : *{$findGelombang->nama}*
⏰ Diterima     : *{$waktuDiterima}*
────────────────────────

Jangan berkecil hati silakan coba lagi lain waktu.

📞 Hubungi admin jika ada pertanyaan.
Terima kasih 🙏

Wassalamu'alaikum warahmatullahi wabarakatuh
";
            $this->kirimFonnte($token, [
                'target' => $target,
                'message' => $message,
            ]);

        }
    }

    protected function kirimFonnte(string $token, array $data): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $data['target'],
                'message' => $data['message'],
            ]);

            // Log responsenya
            \Log::info('WA sent via Fonnte', [
                'to' => $data['target'],
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $th) {
            \Log::error('Failed sending WA via Fonnte', [
                'error' => $th->getMessage(),
            ]);
        }
    }
}
