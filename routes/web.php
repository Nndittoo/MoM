<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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
    Route::get('/create', fn () => view('user.create'))->name('user.create');
    Route::get('/reminder', fn () => view('user.reminder'))->name('user.reminder');
    Route::get('/calendar', fn () => view('user.calendar'))->name('user.calendar');
    Route::get('/notifications', fn () => view('user.notifikasi'))->name('user.notifications');
    Route::get('/detail', fn () => view('user.detail'))->name('user.detail');
});

/**
 * ADMIN AREA
 */
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('admin.dashboard');

        // kalau kamu punya page lain, definisikan di sini:
        Route::get('/approvals', fn () => view('admin.approvals'))->name('admin.approvals');
        Route::get('/mom', fn () => view('admin.mom'))->name('admin.mom');
        Route::get('/calendar', fn () => view('admin.calendar'))->name('admin.calendar');
        Route::get('/users', fn () => view('admin.users'))->name('admin.users');
    });

/**
 * SIGN UP (kalau ada)
 */
Route::get('/sign-up', [AuthController::class, 'showRegister'])->name('register');
Route::post('/sign-up', [AuthController::class, 'register'])->name('register.post');

Route::prefix('admin')->middleware(['auth','role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/users/{id}/update-role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});

