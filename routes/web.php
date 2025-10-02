<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.sign-in');
});

Route::get('/user', function () {
    return view('user.dashboard');
})->name('user.index');

Route::get('/draft', function () {
    return view('user.draft');
})->name('user.draft');

Route::get('/create', function () {
    return view('user.create');
})->name('user.create');

Route::get('/reminder', function () {
    return view('user.reminder');
})->name('user.reminder');

Route::get('/calendar', function () {
    return view('user.calendar');
})->name('user.calendar');

Route::get('/notifications', function () {
    return view('user.notifikasi');
})->name('user.notifications');

Route::get('/detail', function () {
    return view('user.detail');
})->name('user.detail');

Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');
