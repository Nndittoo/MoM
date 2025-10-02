<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Login (public)
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Sign Up (public)
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.post');

// Logout & halaman aplikasi (protected)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/user', fn () => view('user.dashboard'))->name('user.index');

    Route::get('/draft', fn () => view('user.draft'))->name('user.draft');
    Route::get('/create', fn () => view('user.create'))->name('user.create');
    Route::get('/reminder', fn () => view('user.reminder'))->name('user.reminder');
    Route::get('/calendar', fn () => view('user.calendar'))->name('user.calendar');
    Route::get('/notifications', fn () => view('user.notifikasi'))->name('user.notifications');
    Route::get('/detail', fn () => view('user.detail'))->name('user.detail');
    Route::get('/admin', fn () => view('admin.dashboard'))->name('admin.dashboard');
});
