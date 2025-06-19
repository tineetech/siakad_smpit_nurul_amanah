<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'nama',
        'kode',
        'jenis',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // Relasi ke model Guru (satu mata pelajaran bisa diajarkan banyak guru)
    public function gurus()
    {
        return $this->hasMany(Guru::class, 'mata_pelajaran_id');
    }
}