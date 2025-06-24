<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_SISWA = 'siswa';
    const ROLE_GURU = 'guru';
    const ROLE_TATA_USAHA = 'tata_usaha';
    const ROLE_STAFF_PPDB = 'staff_ppdb';
    const ROLE_STAFF = 'staff';

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'address',
        'role',
        'profile_picture'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isGuru()
    {
        return $this->role === self::ROLE_GURU;
    }

    public function isSiswa()
    {
        return $this->role === self::ROLE_SISWA;
    }

    public function isTataUsaha()
    {
        return $this->role === self::ROLE_TATA_USAHA;
    }

    public function isStaffPpdb()
    {
        return $this->role === self::ROLE_STAFF_PPDB;
    }   

    public function isStaff()
    {
        return $this->role === self::ROLE_STAFF;
    }
}
