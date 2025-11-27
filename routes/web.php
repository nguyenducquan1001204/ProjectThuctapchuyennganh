<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ProfileController;

// Trang login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Quên mật khẩu
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password/send-code', [ForgotPasswordController::class, 'sendResetCode'])->name('password.sendCode');
Route::post('/forgot-password/verify-code', [ForgotPasswordController::class, 'verifyResetCode'])->name('password.verifyCode');
Route::post('/forgot-password/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

// Trang thông tin tài khoản (áp dụng cho cả Admin / Kế toán / Giáo viên)
Route::middleware(['auth', 'prevent-back'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'updateInfo'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});

// Load routes cho các khu vực
require __DIR__.'/admin.php';
require __DIR__.'/accounting.php';
require __DIR__.'/teacher.php';
