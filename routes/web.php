<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ParentProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'pageProps' => [
            'authenticated' => auth()->check(),
            'routes' => [
                'home' => url('/'),
                'login' => route('login'),
                'register' => route('register'),
                'dashboard' => route('dashboard'),
            ],
        ],
    ]);
});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::put('admin/users/{user}/access', [AdminController::class, 'updateUserAccess'])->name('admin.users.access');
    });

    Route::middleware('role:doctor')->group(function () {
        Route::get('doctor', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
        Route::patch('doctor/appointments/{appointment}/status', [DoctorController::class, 'updateAppointmentStatus'])
            ->name('doctor.appointments.status');
    });

    Route::middleware('role:parent')->group(function () {
        Route::get('parent-profile', [ParentProfileController::class, 'create'])->name('parent-profile.create');
        Route::post('parent-profile', [ParentProfileController::class, 'store'])->name('parent-profile.store');

        Route::middleware('parent.profile')->group(function () {
            Route::get('children', [ChildController::class, 'index'])->name('children.index');
            Route::get('children/create', [ChildController::class, 'create'])->name('children.create');
            Route::get('children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit');
            Route::post('children', [ChildController::class, 'store'])->name('children.store');
            Route::put('children/{child}', [ChildController::class, 'update'])->name('children.update');
            Route::delete('children/{child}', [ChildController::class, 'destroy'])->name('children.destroy');

            Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
            Route::get('appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
            Route::get('appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
            Route::post('appointments', [AppointmentController::class, 'store'])->name('appointments.store');
            Route::put('appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
            Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
        });
    });
});
