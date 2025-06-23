<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PenetapanSppSiswa;

class PengaturanSpp extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_spp';

    protected $fillable = [
        'nama',
        'jumlah',
        'tanggal_mulai',
        'tanggal_berakhir',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'jumlah' => 'decimal:2',
    ];

    // public function penetapanSppSiswa()
    // {
    //     return $this->hasMany(PenetapanSppSiswa::class, 'pengaturan_spp_id');
    // }
}