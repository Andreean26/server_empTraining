<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\TrainingController;
use App\Http\Controllers\Api\TrainingEnrollmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    
    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/recent-trainings', [DashboardController::class, 'recentTrainings']);
        Route::get('/upcoming-trainings', [DashboardController::class, 'upcomingTrainings']);
        Route::get('/trainings-by-department', [DashboardController::class, 'trainingsByDepartment']);
        Route::get('/monthly-completions', [DashboardController::class, 'monthlyCompletions']);
        Route::get('/enrollment-status', [DashboardController::class, 'enrollmentStatus']);
        Route::get('/top-performers', [DashboardController::class, 'topPerformers']);
        Route::get('/completion-rates', [DashboardController::class, 'completionRates']);
    });
    
    // Employee routes
    Route::apiResource('employees', EmployeeController::class);
    Route::get('/employees-departments', [EmployeeController::class, 'departments']);
    Route::get('/employees-positions', [EmployeeController::class, 'positions']);
    
    // Training routes
    Route::apiResource('trainings', TrainingController::class);
    Route::get('/trainings/{id}/download-pdf', [TrainingController::class, 'downloadPdf']);
    
    // Training Enrollment routes
    Route::apiResource('enrollments', TrainingEnrollmentController::class);
    Route::get('/trainings/{training_id}/enrollments', [TrainingEnrollmentController::class, 'byTraining']);
    Route::get('/employees/{employee_id}/enrollments', [TrainingEnrollmentController::class, 'byEmployee']);
    
    // Role routes
    Route::apiResource('roles', \App\Http\Controllers\Api\RoleController::class);
    Route::get('/roles/{id}/permissions', [\App\Http\Controllers\Api\RoleController::class, 'permissions']);
    Route::put('/roles/{id}/permissions', [\App\Http\Controllers\Api\RoleController::class, 'updatePermissions']);
    
    // User routes
    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
    Route::post('/users/{id}/toggle-status', [\App\Http\Controllers\Api\UserController::class, 'toggleStatus']);
    Route::post('/users/{id}/reset-password', [\App\Http\Controllers\Api\UserController::class, 'resetPassword']);
    Route::get('/users/by-role/{roleId}', [\App\Http\Controllers\Api\UserController::class, 'byRole']);
    Route::get('/users-statistics', [\App\Http\Controllers\Api\UserController::class, 'statistics']);
    Route::get('/users/{id}/employee', [\App\Http\Controllers\Api\UserController::class, 'getEmployee']);
    Route::get('/users-with-employees', [\App\Http\Controllers\Api\UserController::class, 'usersWithEmployees']);
    
});
