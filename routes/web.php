<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainingEnrollmentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard - accessible by all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Role Management - accessible by all authenticated users (as per requirement)
    Route::resource('roles', RoleController::class);
    
    // User Management - only Administrator
    Route::middleware(['role:Administrator'])->group(function () {
        Route::resource('users', UserController::class);
    });
    
    // Employee Management - HR Manager and Administrator
    Route::middleware(['role:Administrator,HR Manager'])->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::get('/employees/{employee}/export', [EmployeeController::class, 'export'])->name('employees.export');
        Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    });
    
    // Training Management - HR Manager and Administrator
    Route::middleware(['role:Administrator,HR Manager'])->group(function () {
        Route::resource('trainings', TrainingController::class);
        Route::get('/trainings/{training}/export', [TrainingController::class, 'export'])->name('trainings.export');
        Route::post('/trainings/import', [TrainingController::class, 'import'])->name('trainings.import');
        Route::post('/trainings/{training}/upload-pdf', [TrainingController::class, 'uploadPdf'])->name('trainings.upload-pdf');
    });
    
    // Training Enrollment Management
    Route::resource('training-enrollments', TrainingEnrollmentController::class);
    Route::post('/trainings/{training}/enroll', [TrainingEnrollmentController::class, 'enroll'])->name('training.enroll');
    Route::post('/enrollments/{enrollment}/attend', [TrainingEnrollmentController::class, 'markAttended'])->name('enrollment.attend');
    Route::post('/enrollments/{enrollment}/complete', [TrainingEnrollmentController::class, 'markCompleted'])->name('enrollment.complete');
    
    // API Routes for Select2
    Route::get('/api/employees/search', [EmployeeController::class, 'search'])->name('api.employees.search');
    Route::get('/api/trainings/search', [TrainingController::class, 'search'])->name('api.trainings.search');
    
    // Export/Import Routes
    Route::get('/export/trainings', [TrainingController::class, 'exportAll'])->name('export.trainings');
    Route::get('/export/employees', [EmployeeController::class, 'exportAll'])->name('export.employees');
    Route::get('/export/enrollments', [TrainingEnrollmentController::class, 'exportAll'])->name('export.enrollments');
});
