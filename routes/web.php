<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// 1. Chuyển hướng trang chủ thẳng vào màn hình Đăng nhập
Route::redirect('/', '/login');

// 2. Nơi chứa toàn bộ các Route Đăng nhập, Đăng ký, Quên mật khẩu
Auth::routes();

// 3. Route trung chuyển khi user đã đăng nhập (Fix lỗi Too many redirects)
Route::get('/home', function () {
    if (!Auth::check()) return redirect('/login');
    return match (Auth::user()->role) {
        'admin' => redirect('/admin'),
        'receptionist' => redirect('/receptionist'),
        'doctor' => redirect('/doctor'),
        'patient' => redirect('/patient'),
        default => abort(403, 'Role không hợp lệ hoặc chưa được gán quyền.'),
    };
})->name('home');

// 3. Các Route dành riêng cho từng Role đã phân quyền
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/', [App\Http\Controllers\Doctor\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:receptionist'])->prefix('receptionist')->name('receptionist.')->group(function () {
    Route::get('/', [App\Http\Controllers\Receptionist\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/', [App\Http\Controllers\Patient\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\Patient\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Patient\ProfileController::class, 'update'])->name('profile.update');
});