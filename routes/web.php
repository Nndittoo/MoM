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
    Route::get('/create', fn () => view('user.create'))->name('user.create');
    Route::get('/calendar', fn () => view('user.calendar'))->name('user.calendar');
    Route::get('/notifications', fn () => view('user.notifikasi'))->name('user.notifications');
    Route::get('/show', fn () => view('user.show'))->name('user.show');
    Route::get('/export', fn () => view('user.export'))->name('user.export');

    Route::get('/admin', fn () => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/approvals', fn () => view('admin.approvals'))->name('admin.approvals');
    Route::get('/calendars', fn () => view('admin.calendars'))->name('admin.calendars');
    Route::get('/mom', fn () => view('admin.mom'))->name('admin.mom');
    Route::get('/users', fn () => view('admin.users'))->name('admin.users');
    Route::get('/task', fn () => view('admin.task'))->name('admin.task');
    Route::get('/notification', fn () => view('admin.notification'))->name('admin.notification');
    Route::get('/details', fn () => view('admin.details'))->name('admin.details');
    Route::get('/shows', fn () => view('admin.shows'))->name('admin.shows');
});
