<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
// use Session;

class AuthController extends Controller
{
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
