<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalonSiswa extends Model
{
    protected $table = 'calon_siswa';

    protected $fillable = [
        'gelombang_id',
        'nomor_pendaftaran',
        'nik',
        'nisn',
        'nama_lengkap',
        'profile_picture',
        'kartu_keluarga', 
        'akta_kelahiran', 
        'surat_kelulusan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nomor_hp_siswa',
        'asal_sekolah',
        'anak_ke',
        'jumlah_saudara',
        'cita_cita',
        'hobi',
        'berat_badan',
        'tinggi_badan',
        'riwayat_penyakit',
        'nama_ayah',
        'status_ayah',
        'tempat_lahir_ayah',
        'tanggal_lahir_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'nomor_hp_ayah',
        'nama_ibu',
        'status_ibu',
        'tempat_lahir_ibu',
        'tanggal_lahir_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'nomor_hp_ibu',
        'tanggal_pendaftaran',
        'status',
        'disetujui_oleh_user_id',
        'tanggal_persetujuan',
    ];
    
    public function gelombang() { 
        return $this->belongsTo(Gelombang::class); 
    }
    
    public function disetujuiOleh() { 
        return $this->belongsTo(User::class, 'disetujui_oleh_user_id');
    }
}
