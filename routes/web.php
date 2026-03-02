<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/ip-monitoring', 'ip-monitoring');

Route::view('/ip-monitoring', 'ip-monitoring');
