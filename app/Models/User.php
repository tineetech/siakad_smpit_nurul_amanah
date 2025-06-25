<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_SISWA = 'siswa';
    const ROLE_GURU = 'guru';
    const ROLE_TATA_USAHA = 'tata_usaha';
    const ROLE_STAFF_PPDB = 'staff_ppdb';
    const ROLE_STAFF = 'staff';
    const ROLE_KEPSEK = 'kepala_sekolah';

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'profile_picture',
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

    public function isKepsek()
    {
        return $this->role === self::ROLE_KEPSEK;
    }


    /**
     * Get the URL for the user's Filament avatar.
     *
     * @return string|null
     */
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->profile_picture) {
            return Storage::url($this->profile_picture);
        }

        // Jika tidak ada profile_picture, kembalikan null atau URL default jika Anda punya
        // Filament akan menampilkan inisial atau placeholder default-nya jika null
        return null;
    }
}
