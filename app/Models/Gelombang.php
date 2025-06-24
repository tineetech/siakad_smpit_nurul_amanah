<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gelombang extends Model
{
    protected $table = 'gelombang';
    protected $fillable = [
        'nama','kouta','kouta_terisi','tanggal_mulai','tanggal_berakhir','created_by',
    ];
}
