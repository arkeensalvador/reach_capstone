<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
});
Route::get('/records', function () {
    return view('admin.year_select');
});
