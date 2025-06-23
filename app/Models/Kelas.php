<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'tingkat',
        'kapasitas',
        'guru_id', // Tambahkan ini untuk wali kelas
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Siswa (yang terdaftar langsung di kelas ini - jika masih dipakai)
    // Jika siswa hanya di-enroll via tabel enrollment, relasi ini bisa dipertimbangkan ulang
    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    // Relasi ke Guru sebagai wali kelas
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    // Relasi ke Enrollment (kelas memiliki banyak enrollment)
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}