<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Load routes cho các khu vực
require __DIR__.'/admin.php';
require __DIR__.'/accounting.php';
require __DIR__.'/teacher.php';
