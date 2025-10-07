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
    Route::get('/show', fn () => view('user.show'))->name('user.show');
    Route::get('/export', fn () => view('user.export'))->name('user.export');

    Route::get('/admin', fn () => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/approvals', fn () => view('admin.approvals'))->name('admin.approvals');
    Route::get('/calendars', fn () => view('admin.calendars'))->name('admin.calendar');
    Route::get('/mom', fn () => view('admin.mom'))->name('admin.mom');
    Route::get('/users', fn () => view('admin.users'))->name('admin.users');
    Route::get('/details', fn () => view('admin.details'))->name('admin.details');
    Route::get('/task', fn () => view('admin.task'))->name('admin.task');
    Route::get('/creates', fn () => view('admin.create'))->name('admin.create');
    Route::get('/shows', fn () => view('admin.shows'))->name('admin.shows');
    Route::get('/notification', fn () => view('admin.notification'))->name('admin.notification');
});
