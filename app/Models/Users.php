<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users'; 
    protected $fillable = [
        'username',
        'password',
        'email',
        'role',
        'phone_number',
        'address',
    ];
}
