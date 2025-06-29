<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'bukti_tf',
        'teller_user_id',
        'catatan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function penetapan()
    {
        return $this->belongsTo(PenetapanSpps::class, 'penetapan_spp_id');
    }

    public function teller()
    {
        return $this->belongsTo(User::class, 'teller_user_id');
    }
}
