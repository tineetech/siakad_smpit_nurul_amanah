<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalonSiswa extends Model
{
    protected $table = 'calon_siswa';

    protected $fillable = [
        'gelombang_id','nomor_pendaftaran','nisn','nis','nama_lengkap',
        'profile_picture','surat_kelulusan','akta_kelahiran','kartu_keluarga',
        'jenis_kelamin','tempat_lahir','tanggal_lahir','alamat',
        'nama_orang_tua','nomor_telepon_orang_tua','email',
        'tanggal_pendaftaran','status','disetujui_oleh_user_id','tanggal_persetujuan',
    ];
    
    public function gelombang() { 
        return $this->belongsTo(Gelombang::class); 
    }
    
    public function disetujuiOleh() { 
        return $this->belongsTo(User::class, 'disetujui_oleh_user_id');
    }
}
