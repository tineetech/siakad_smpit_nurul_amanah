<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\PpdbController;
use App\Http\Controllers\SiswaController;
use App\Models\Gelombang;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.beranda');
})->name('home');

Route::prefix('ppdb')->group(function() {
    Route::get('/', [PpdbController::class, 'index'])->name('ppdb.index');
    Route::post('/', [PpdbController::class, 'store'])->name('ppdb.store');

    Route::get('/success', [PpdbController::class, 'indexSuccess'])->name('ppdb.success');
});

Route::get('/api/gelombang', function () {
    return Gelombang::select('id', 'nama')->orderBy('tanggal_mulai', 'desc')->get();
})->name('api.gelombang');


Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

