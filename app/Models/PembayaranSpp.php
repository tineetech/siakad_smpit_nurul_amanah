<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranSpp extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_spp';

    protected $fillable = [
        'siswa_id',
        'penetapan_spp_id',
        'jumlah_dibayar',
        'tanggal_pembayaran',
        'metode_pembayaran',
        'teller_user_id',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'datetime',
        'jumlah_dibayar' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function penetapanSpp()
    {
        return $this->belongsTo(PenetapanSppSiswa::class, 'penetapan_spp_id');
    }

    public function teller()
    {
        return $this->belongsTo(User::class, 'teller_user_id');
    }
}