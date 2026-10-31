<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMasterController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/barang/{item}', [ItemController::class, 'show'])
    ->whereNumber('item')->name('items.show');

// Hanya untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);

    Route::get('/lupa-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Hanya untuk yang sudah login
Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Form posting barang
    Route::get('/lapor/{jenis}', [ItemController::class, 'create'])
        ->whereIn('jenis', ['temuan', 'hilang'])->name('items.create');
    Route::post('/lapor/{jenis}', [ItemController::class, 'store'])
        ->whereIn('jenis', ['temuan', 'hilang'])->name('items.store');

    // Klaim / lapor temuan
    Route::get('/barang/{item}/klaim', [ClaimController::class, 'create'])
        ->whereNumber('item')->name('claims.create');
    Route::post('/barang/{item}/klaim', [ClaimController::class, 'store'])
        ->whereNumber('item')->name('claims.store');
    Route::get('/klaim/{claim}', [ClaimController::class, 'show'])
        ->whereNumber('claim')->name('claims.show');
    Route::get('/klaim-foto/{image}', [ClaimController::class, 'image'])
        ->whereNumber('image')->name('claims.image');

    // route untuk menyetujui atau menolak klaim, hanya bisa diakses oleh pembuat postingan atau admin
    Route::post('/klaim/{claim}/setujui', [ClaimController::class, 'approve'])
        ->whereNumber('claim')->name('claims.approve');
    Route::post('/klaim/{claim}/tolak', [ClaimController::class, 'reject'])
        ->whereNumber('claim')->name('claims.reject');

    // Serah terima
    Route::post('/klaim/{claim}/serah-terima', [HandoverController::class, 'store'])
        ->whereNumber('claim')->name('handover.store');
    Route::post('/klaim/{claim}/konfirmasi', [HandoverController::class, 'confirm'])
        ->whereNumber('claim')->name('handover.confirm');
    Route::post('/klaim/{claim}/belum-diterima', [HandoverController::class, 'dispute'])
        ->whereNumber('claim')->name('handover.dispute');
    Route::get('/serah-terima-foto/{image}', [HandoverController::class, 'image'])
        ->whereNumber('image')->name('handover.image');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifikasi/{id}/buka', [NotificationController::class, 'open'])->name('notifications.open');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'readAll'])->name('notifications.readAll');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'password'])->name('profile.password');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    Route::get('/postingan', [AdminController::class, 'items'])->name('items');
    Route::post('/postingan/{item}/hapus', [AdminController::class, 'destroyItem'])->whereNumber('item')->name('items.destroy');
    Route::post('/postingan/{item}/pulihkan', [AdminController::class, 'restoreItem'])->whereNumber('item')->name('items.restore');

    Route::get('/klaim', [AdminController::class, 'claims'])->name('claims');

    Route::get('/pengguna', [AdminController::class, 'users'])->name('users');
    Route::post('/pengguna/{user}/peran', [AdminController::class, 'toggleRole'])->whereNumber('user')->name('users.role');

    Route::post('/kedaluwarsa', [AdminController::class, 'expire'])->name('expire');

    Route::get('/master/{type}', [AdminMasterController::class, 'index'])->whereIn('type', ['kategori', 'lokasi'])->name('master');
    Route::post('/master/{type}', [AdminMasterController::class, 'store'])->whereIn('type', ['kategori', 'lokasi'])->name('master.store');
    Route::post('/master/{type}/{id}/ubah', [AdminMasterController::class, 'update'])->whereIn('type', ['kategori', 'lokasi'])->whereNumber('id')->name('master.update');
    Route::post('/master/{type}/{id}/hapus', [AdminMasterController::class, 'destroy'])->whereIn('type', ['kategori', 'lokasi'])->whereNumber('id')->name('master.destroy');
});
