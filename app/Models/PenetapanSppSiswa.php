<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenetapanSppSiswa extends Model
{
    use HasFactory;

    protected $table = 'penetapan_spp_siswa';

    protected $fillable = [
        'siswa_id',
        'pengaturan_spp_id',
        'status',
        'tanggal_jatuh_tempo',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pengaturanSpp()
    {
        return $this->belongsTo(PengaturanSpp::class);
    }
}