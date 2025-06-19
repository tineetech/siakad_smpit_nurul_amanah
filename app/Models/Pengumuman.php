<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'konten',
        'diposting_oleh_user_id',
        'target_peran',
        'tanggal_publikasi',
    ];

    protected $casts = [
        'tanggal_publikasi' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke model User (siapa yang memposting pengumuman)
    public function dipostingOleh()
    {
        return $this->belongsTo(User::class, 'diposting_oleh_user_id');
    }
}