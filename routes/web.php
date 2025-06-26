<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\PengumumanHomeController;
use App\Http\Controllers\PpdbController;
use App\Http\Controllers\SiswaController;
use App\Models\Gelombang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.beranda');
})->name('home');

Route::prefix('ppdb')->group(function() {
    Route::get('/', [PpdbController::class, 'index'])->name('ppdb.index');
    Route::post('/', [PpdbController::class, 'store'])->name('ppdb.store');

    Route::get('/success/{nomor_pendaftaran}', [PpdbController::class, 'indexSuccess'])->name('ppdb.success');
});


Route::prefix('api')->group(function() {
    Route::get('/gelombang', function () {
        return Gelombang::select('id', 'nama')->orderBy('tanggal_mulai', 'desc')->get();
    })->name('api.gelombang');

    // pengumuman route
    Route::get('/announcements', [PengumumanHomeController::class, 'index'])->name('api.announcements.index');
    Route::get('/announcements/{id}', [PengumumanHomeController::class, 'show'])->name('api.announcements.show');
});


Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

