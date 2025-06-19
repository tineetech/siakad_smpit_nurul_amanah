<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru'; // Pastikan nama tabelnya benar jika tidak standar (Laravel akan mencari 'gurus')

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'qr_code_data',
        'status',
        'mata_pelajaran_id',
        'kelas_id', // Untuk wali kelas
    ];

    // Mengatur kolom tanggal_lahir dan created_at agar di-cast ke tipe date/datetime
    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke model MataPelajaran (mata pelajaran yang diajarkan)
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    // Relasi ke model Kelas (jika dia seorang wali kelas)
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}