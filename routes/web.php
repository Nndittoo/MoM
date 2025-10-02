<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/index', function () {
    return view('index');
});

Route::get('/draft', function () {
    return view('draft');
});

Route::get('/create', function () {
    return view('create');
});

Route::get('/reminder', function () {
    return view('reminder');
});

Route::get('/detail', function () {
    return view('detail');
});

Route::get('/sign-in', function () {
    return view('sign-in');
});

Route::get('/sign-up', function () {
    return view('sign-up');
});

Route::get('/reminder1', function () {
    return view('reminder1');
});

Route::get('/calendar', function () {
    return view('calendar');
});

Route::get('/notifikasi', function () {
    return view('notifikasi');
});
