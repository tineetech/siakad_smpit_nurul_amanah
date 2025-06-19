@extends('layouts.auth')

@section('title', 'Login Page')

@section('content')
<div class="card">
    <div class="card-body login-card-body">
        <p class="login-box-msg">Login sekarang</p>

        @if(session('message'))
            <div class="alert alert-success">
                {{session('message')}}
            </div>
        @endif
        <form action="{{ route('login.post') }}" method="post">
            @csrf
            <div class="input-group mb-3">
                <input type="email" required name="email" class="form-control" placeholder="Email">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="password" required name="password" class="form-control" placeholder="Password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-8">
                    <div class="icheck-primary">
                        <input type="checkbox" id="remember">
                        <label for="remember">
                            Remember Me
                        </label>
                    </div>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-success btn-block">Login</button>
                </div>
                </div>
        </form>
    </div>
    </div>
@endsection