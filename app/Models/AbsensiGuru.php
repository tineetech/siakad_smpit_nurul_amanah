<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiGuru extends Model
{
    use HasFactory;

    protected $table = 'absensi_guru';

    protected $fillable = [
        'guru_id',
        'tanggal_absensi',
        'waktu_absensi',
        'status_kehadiran',
        'mode_absensi',
        'pencatat_user_id',
        'qr_code_terscan',
        'catatan',
    ];

    // Tambahkan konstanta untuk status kehadiran
    const STATUS_HADIR = 'hadir';
    const STATUS_TERLAMBAT = 'terlambat';
    const STATUS_IZIN = 'izin';
    const STATUS_SAKIT = 'sakit';
    const STATUS_ALPA = 'alpha';

    // Tambahkan konstanta untuk mode absensi
    const MODE_QR = 'scan_qr';
    const MODE_MANUAL = 'manual';

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'pencatat_user_id');
    }
}