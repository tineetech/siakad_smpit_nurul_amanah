<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollment';

    protected $fillable = [
        'nama', // Opsional
        'kelas_id',
        'siswa_id',
        'guru_id',
        'semester_id',
        'kurikulum_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    // Relasi ke Semester
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    // Relasi ke Kurikulum
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }
}