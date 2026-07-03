<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController;

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
    
    // Quản lý nhân sự
    Route::resource('staff', App\Http\Controllers\Admin\StaffController::class)->except(['show', 'destroy']);
    Route::patch('staff/{staff}/toggle-active', [App\Http\Controllers\Admin\StaffController::class, 'toggleActive'])->name('staff.toggle-active');
    Route::post('staff/{staff}/reset-password', [App\Http\Controllers\Admin\StaffController::class, 'resetPassword'])->name('staff.reset-password');

    // Quản lý chuyên khoa
    Route::resource('specialties', App\Http\Controllers\Admin\SpecialtyController::class)->except(['create', 'show', 'edit']);

    Route::resource('users', UserController::class);
    Route::patch(
            'users/{user}/reset-password',
            [UserController::class, 'resetPassword']
        )->name('users.reset-password');
    Route::patch(
            'users/{user}/toggle-status',
            [UserController::class, 'toggleStatus']
        )->name('users.toggle-status');
});

Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/', [App\Http\Controllers\Doctor\DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/appointments/{id}/start', [App\Http\Controllers\Doctor\DashboardController::class, 'startExamine'])->name('appointments.start');
    Route::patch('/appointments/{id}/push-back', [App\Http\Controllers\Doctor\DashboardController::class, 'pushBack'])->name('appointments.push-back');
    Route::get('/appointments/{id}/examine', [App\Http\Controllers\Doctor\AppointmentController::class, 'examine'])->name('appointments.examine');
    Route::post('/appointments/{id}/examine', [App\Http\Controllers\Doctor\AppointmentController::class, 'storeExamine'])->name('appointments.storeExamine');
});

Route::middleware(['auth', 'role:receptionist'])->prefix('receptionist')->name('receptionist.')->group(function () {
    Route::get('/', [App\Http\Controllers\Receptionist\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [App\Http\Controllers\Receptionist\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [App\Http\Controllers\Receptionist\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [App\Http\Controllers\Receptionist\AppointmentController::class, 'store'])->name('appointments.store');
    Route::put('/appointments/{id}/reschedule', [App\Http\Controllers\Receptionist\AppointmentController::class, 'update'])->name('appointments.update');
    Route::patch('/appointments/{id}/checkin', [App\Http\Controllers\Receptionist\AppointmentController::class, 'checkIn'])->name('appointments.checkin');
    Route::patch('/appointments/{id}/assign', [App\Http\Controllers\Receptionist\AppointmentController::class, 'assignDoctor'])->name('appointments.assign');
    
    Route::get('/appointments/{id}/payment', [App\Http\Controllers\Receptionist\PaymentController::class, 'show'])->name('payment.show');
    Route::post('/appointments/{id}/payment/cash', [App\Http\Controllers\Receptionist\PaymentController::class, 'processCash'])->name('payment.cash');
    Route::post('/appointments/{id}/payment/vnpay', [App\Http\Controllers\Receptionist\PaymentController::class, 'createVnPayPayment'])->name('payment.vnpay');
    Route::get('/payment/vnpay-return', [App\Http\Controllers\Receptionist\PaymentController::class, 'vnpayReturn'])->name('payment.vnpayReturn');
    Route::get('/appointments/{id}/invoice/print', [App\Http\Controllers\Receptionist\PaymentController::class, 'printInvoice'])->name('invoice.print');

    // API Routes for AJAX
    Route::get('/api/search-patient', [App\Http\Controllers\Receptionist\AppointmentController::class, 'searchPatient'])->name('api.search-patient');
    Route::get('/api/available-doctors', [App\Http\Controllers\Receptionist\AppointmentController::class, 'getAvailableDoctors'])->name('api.available-doctors');
});

Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/', [App\Http\Controllers\Patient\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\Patient\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Patient\ProfileController::class, 'update'])->name('profile.update');

    // Quản lý đặt lịch khám
    Route::get('/appointments', [App\Http\Controllers\Patient\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [App\Http\Controllers\Patient\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [App\Http\Controllers\Patient\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/shifts', [App\Http\Controllers\Patient\AppointmentController::class, 'getAvailableShifts'])->name('appointments.shifts');
    Route::get('/appointments/history', [App\Http\Controllers\Patient\AppointmentController::class, 'history'])->name('appointments.history');
});


