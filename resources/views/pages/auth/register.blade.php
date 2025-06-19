@extends('layouts.auth')

@section('title', 'Login Page')

@section('content')
<div class="card">
    <div class="card-body login-card-body">
        <p class="login-box-msg">Buat Akun Siswa sekarang</p>
        @if(session('message'))
            <div class="alert alert-success">
                {{session('message')}}
            </div>
        @endif
        <form action="{{ route('register.post') }}" method="post">
            @csrf
            <div class="input-group mb-3">
                <input type="text" name="username" required class="form-control" placeholder="Username">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="email" name="email" required class="form-control" placeholder="Email">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="password" name="password" required class="form-control" placeholder="Password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="number" name="phone_number" required class="form-control" placeholder="No. Telp">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-phone"></span>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="col-8">
                    {{-- <button type="submit" class="btn btn-success btn-block">Register</button> --}}
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-success btn-block">Register</button>
                </div>
                </div>
        </form>
    </div>
    </div>
@endsection