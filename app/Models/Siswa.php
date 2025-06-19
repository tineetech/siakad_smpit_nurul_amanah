<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nisn',
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'nama_ayah',
        'nama_ibu',
        'nomor_telepon_orang_tua',
        'qr_code_data',
        'status',
        'kelas_id',
    ];
    
    protected $casts = [
        'tanggal_lahir' => 'date', // Ini yang penting untuk error Anda
        'created_at' => 'datetime', // Ini juga untuk created_at di export
    ];

    // Relasi ke model User (jika ada)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke model Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}