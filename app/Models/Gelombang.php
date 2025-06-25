<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gelombang extends Model
{
    use HasFactory;

    protected $table = 'gelombang';

    protected $fillable = [
        'nama',
        'kouta',
        'kouta_terisi',
        'tanggal_mulai',
        'tanggal_berakhir',
        'created_by',
    ];

    // Relasi ke CalonSiswa
    public function calonSiswas()
    {
        return $this->hasMany(CalonSiswa::class, 'gelombang_id');
    }
}
