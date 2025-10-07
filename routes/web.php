<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MomController;
use App\Models\User;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/**
 * USER AREA
 */
Route::middleware(['auth', 'role:user,admin'])->group(function () {
    Route::get('/user', fn () => view('user.dashboard'))->name('user.index');
    Route::get('/draft', fn () => view('user.draft'))->name('user.draft');
    Route::get('/reminder', fn () => view('user.reminder'))->name('user.reminder');
    Route::get('/calendar', fn () => view('user.calendar'))->name('user.calendar');
    Route::get('/notifications', fn () => view('user.notifikasi'))->name('user.notifications');
    Route::get('/show', fn () => view('user.show'))->name('user.show');
    Route::get('/export', fn () => view('user.export'))->name('user.export');

    Route::get('/admin', fn () => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/approvals', fn () => view('admin.approvals'))->name('admin.approvals');
    Route::get('/calendar', fn () => view('admin.calendar'))->name('admin.calendar');
    Route::get('/mom', fn () => view('admin.mom'))->name('admin.mom');
    Route::get('/users', fn () => view('admin.users'))->name('admin.users');

});
