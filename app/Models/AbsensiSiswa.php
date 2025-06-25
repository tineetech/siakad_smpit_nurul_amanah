<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AbsensiSiswa extends Model
{
    use HasFactory;

    protected $table = 'absensi_siswa';

    protected $fillable = [
        'siswa_id',
        'tanggal_absensi',
        'waktu_absensi',
        'status_kehadiran',
        'mode_absensi',
        'pencatat_user_id',
        'qr_code_terscan',
        'catatan',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->qr_code_terscan)) {
                $model->qr_code_terscan = (string) Str::uuid();
            }
        });
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'pencatat_user_id');
    }
}
