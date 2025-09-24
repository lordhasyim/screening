<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PublicStatsController;

/*
|--------------------------------------------------------------------------
| Quiz Routes - Main Application
|--------------------------------------------------------------------------
*/

// Welcome/Landing Page
Route::get('/', [QuizController::class, 'index'])->name('quiz.index');
Route::get('/statistics', [PublicStatsController::class, 'index'])->name('public.stats');
// Quiz Flow Routes
Route::prefix('quiz')->name('quiz.')->group(function () {
    // Identity Form
    Route::get('/identity', [QuizController::class, 'identity'])->name('identity');
    Route::post('/identity', [QuizController::class, 'submitIdentity']);
    
    // PHQ-9 Questions
    Route::get('/phq9', [QuizController::class, 'phq9'])->name('phq9');
    Route::post('/phq9', [QuizController::class, 'submitPhq9']);
    
    // DASS-21 Questions (conditional)
    Route::get('/dass21', [QuizController::class, 'dass21'])->name('dass21');
    Route::post('/dass21', [QuizController::class, 'submitDass21']);
    
    // Results Page
    Route::get('/result/{quizResponse}', [QuizController::class, 'result'])->name('result');
});

// AJAX Routes
Route::get('/departments/{faculty}', [QuizController::class, 'getDepartments'])->name('departments.by-faculty');

/*
|--------------------------------------------------------------------------
| Admin Routes - Dashboard & Management
|--------------------------------------------------------------------------
*/

// Route::prefix('admin')->name('admin.')->group(function () {
//     // Authentication Routes
//     Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
//     // Protected Admin Routes
//     Route::middleware('auth:admin')->group(function () {
//         // Dashboard
//         Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
//         // Quiz Responses Management
//         Route::get('/responses', [DashboardController::class, 'responses'])->name('responses');
//         Route::get('/responses/{quizResponse}', [DashboardController::class, 'showResponse'])->name('responses.show');
//         Route::delete('/responses/{quizResponse}', [DashboardController::class, 'deleteResponse'])->name('responses.delete');
        
//         // Analytics & Reports
//         Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
//         Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
//         Route::get('/export', [DashboardController::class, 'export'])->name('export');
        
//         // User Management (Admin & Super Admin only)
//         Route::middleware('can:manage-users')->group(function () {
//             Route::get('/users', [DashboardController::class, 'users'])->name('users');
//             Route::post('/users', [DashboardController::class, 'createUser'])->name('users.create');
//             Route::put('/users/{adminUser}', [DashboardController::class, 'updateUser'])->name('users.update');
//             Route::delete('/users/{adminUser}', [DashboardController::class, 'deleteUser'])->name('users.delete');
//         });
        
//         // Settings
//         Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
//         Route::put('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
//     });
// });

/*
|--------------------------------------------------------------------------
| API Routes (Optional - for AJAX requests)
|--------------------------------------------------------------------------
*/

// Route::prefix('api')->name('api.')->group(function () {
//     // Public endpoints
//     Route::get('/faculties', function () {
//         return \App\Models\Faculty::with('departments')->get();
//     });
    
//     // Admin endpoints (protected)
//     Route::middleware('auth:admin')->group(function () {
//         Route::get('/stats', [DashboardController::class, 'getStats']);
//         Route::get('/chart-data', [DashboardController::class, 'getChartData']);
//         Route::post('/analytics', [DashboardController::class, 'trackAnalytics']);
//     });
// });