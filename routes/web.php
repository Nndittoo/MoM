<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MomController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\ActionItemController;
use App\Http\Controllers\ApprovalController;
use App\Models\User;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminCalendarController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminTaskController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/**
 * USER AREA
 */
Route::middleware(['auth', 'role:user,admin'])->group(function () {
    Route::get('/api/search-moms', [DashboardController::class, 'searchMoms'])->name('api.search.moms');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/search', [DashboardController::class, 'searchMoms'])->name('dashboard.search');

        Route::get('/user', function() {
        return redirect()->route('dashboard');
    })->name('user.index');
    Route::get('/draft', fn () => view('user.draft'))->name('user.draft');
    Route::get('/reminder', [ReminderController::class, 'index'])->name('user.reminder');
    Route::post('/reminder/{id}/complete', [ReminderController::class, 'complete'])->name('reminder.complete');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('user.calendar');
    Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::get('/show', fn () => view('user.show'))->name('user.show');
    Route::get('/export', fn () => view('user.export'))->name('user.export');


    Route::get('/create', function () {
        $users = App\Models\User::all(['id', 'name']);
        return view('user.create', compact('users'));
    })->name('user.create');

    Route::post('/moms', [MomController::class, 'store'])->name('moms.store');
});

/**
 * SIGN UP
 */
Route::get('/sign-up', [AuthController::class, 'showRegister'])->name('register');
Route::post('/sign-up', [AuthController::class, 'register'])->name('register.post');

Route::prefix('admin')->middleware(['auth','role:admin'])->group(function () {

    // Admin Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    // Admin Calendar
    Route::get('/calendars', [AdminCalendarController::class, 'index'])->name('admin.calendars');
    Route::get('/calendars/events', [AdminCalendarController::class, 'getEvents'])->name('admin.calendars.events');

    // Admin Notification
    Route::get('/notification', [AdminNotificationController::class, 'index'])->name('admin.notification');
    Route::get('/notification/{id}/read', [AdminNotificationController::class, 'read'])->name('admin.notification.read');
    Route::get('/admin/notifications/recent', [AdminNotificationController::class, 'getRecent'])->name('admin.notifications.recent');

        Route::get('/mom/export', fn () => view('admin.export'))->name('admin.export');

    Route::get('/mom', fn () => view('admin.mom'))->name('admin.mom');
    Route::get('/users', fn () => view('admin.users'))->name('admin.users');

    // Admin Task Management
    Route::get('/task', [AdminTaskController::class, 'index'])->name('admin.task');
    Route::post('/task/{action_id}/update-status', [AdminTaskController::class, 'updateStatus'])->name('admin.task.update-status');
    Route::get('/task/search', [AdminTaskController::class, 'search'])->name('admin.task.search');

    Route::get('/details/{mom}', [MomController::class, 'show_admin'])->name('admin.details');
    Route::get('/shows', fn () => view('admin.shows'))->name('admin.shows');
    Route::get('/creates', fn () => view('admin.create'))->name('admin.creates');
    Route::get('/mom', [MomController::class, 'repository'])->name('admin.repository');
    Route::get('/admin/moms/{mom}/edit', [MomController::class, 'editAdmin'])->name('admin.moms.edit');

    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/users/{id}/update-role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('admin.approvals.index');
    Route::post('/approvals/approve/{mom}', [ApprovalController::class, 'approve'])->name('admin.approvals.approve');
    Route::post('/approvals/reject/{mom}', [ApprovalController::class, 'reject'])->name('admin.approvals.reject');
    Route::get('/moms/create', [MomController::class, 'create'])->name('admin.moms.create');
    Route::get('/moms/{mom}', [MomController::class, 'show_admin'])->name('admin.moms.show');

});

Route::get('/draft', [DraftController::class, 'index'])->name('draft.index')->middleware('auth');
Route::get('/moms/{mom}', [MomController::class, 'show'])->name('moms.detail');
//Route::get('/moms/{mom}/edit', [MomController::class, 'edit'])->name('user.edit');
Route::get('/export/{mom}', [MomController::class, 'export'])->name('moms.export');

// Action Items routes
Route::prefix('action-items')->group(function () {
    Route::post('/', [ActionItemController::class, 'store'])->name('action_items.store');
    Route::delete('/{actionItem}', [ActionItemController::class, 'destroy'])->name('action_items.destroy');


});

Route::middleware(['auth'])->name('moms.')->prefix('moms')->group(function () {
    // Route untuk menampilkan form edit
    Route::get('/{mom}/edit', [MomController::class, 'edit'])->name('edit');

    // Route untuk memproses update data (AJAX Spoofing PATCH)
    Route::patch('/{mom}', [MomController::class, 'update'])->name('update');
});

Route::delete('/moms/{mom}', [MomController::class, 'destroy'])->name('moms.destroy');

