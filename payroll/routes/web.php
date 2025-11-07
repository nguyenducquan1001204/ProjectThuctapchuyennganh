<?php

use App\Http\Controllers\Admin\JobTitleController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'template.index')->name('dashboard');
Route::get('/quanlychucdanh', [JobTitleController::class, 'index'])->name('admin.roles.index');
Route::post('/quanlychucdanh', [JobTitleController::class, 'store'])->name('admin.roles.store');
Route::put('/quanlychucdanh/{jobtitle}', [JobTitleController::class, 'update'])->name('admin.roles.update');
Route::delete('/quanlychucdanh/{jobtitle}', [JobTitleController::class, 'destroy'])->name('admin.roles.destroy');
