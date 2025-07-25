<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $fillable = [
        'user_id',
        'niy',
        'nama_lengkap',
        'jenjang_pendidikan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'qr_code_data',
        'status',
        'mata_pelajaran_id',
        'kelas_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke Enrollment (guru dapat memiliki banyak enrollment)
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
