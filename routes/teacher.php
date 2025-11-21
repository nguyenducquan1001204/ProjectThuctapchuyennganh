<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\TeacherController;

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
|
| Routes dành cho khu vực Giáo viên
| Prefix: /teacher
|
*/

Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher', 'prevent-back'])->group(function () {
    Route::get('/', [TeacherController::class, 'index'])->name('dashboard');
    // Thêm các routes teacher khác ở đây
});

