<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MomController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\ActionItemController;
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
    Route::get('/calendars', fn () => view('admin.calendars'))->name('admin.calendars');
    Route::get('/mom', fn () => view('admin.mom'))->name('admin.mom');
    Route::get('/users', fn () => view('admin.users'))->name('admin.users');
    Route::get('/task', fn () => view('admin.task'))->name('admin.task');
    Route::get('/notification', fn () => view('admin.notification'))->name('admin.notification');
    Route::get('/details', fn () => view('admin.details'))->name('admin.details');
    Route::get('/shows', fn () => view('admin.shows'))->name('admin.shows');

    Route::get('/create', function () {
        $users = App\Models\User::all(['id', 'name']); 
        return view('user.create', compact('users')); 
    })->name('user.create'); 

    // POST route tetap sama, tetapi namanya lebih jelas
    Route::post('/moms', [MomController::class, 'store'])->name('moms.store'); 
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

Route::get('/draft', [DraftController::class, 'index'])->name('draft.index')->middleware('auth');
// Route for viewing details (moms.detail)
Route::get('/moms/{mom}', [MomController::class, 'show'])->name('moms.detail'); 
// Route for editing/revising (moms.edit)
Route::get('/moms/{mom}/edit', [MomController::class, 'edit'])->name('moms.edit'); 
Route::post('/action-items', [ActionItemController::class, 'store'])->name('action_items.store');