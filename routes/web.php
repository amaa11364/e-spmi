<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Vue SPA - All routes go to Vue
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

/*
|--------------------------------------------------------------------------
| API Routes (for Vue.js)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    
    // Public routes
    Route::get('/hero', [HeroController::class, 'index']);
    Route::get('/beritas/latest', [BeritaController::class, 'latest']);
    Route::get('/beritas', [BeritaController::class, 'index']);
    Route::get('/beritas/{id}', [BeritaController::class, 'show']);
    Route::get('/jadwals/upcoming', [JadwalController::class, 'upcoming']);
    Route::get('/jadwals', [JadwalController::class, 'index']);
    Route::get('/jadwals/{id}', [JadwalController::class, 'show']);
    Route::get('/jadwals/date-range', [JadwalController::class, 'getByDateRange']);
    
    // Auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        
        // Dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('/dashboard/activity', [DashboardController::class, 'recentActivity']);
        Route::get('/dashboard/chart', [DashboardController::class, 'chartData']);
        
        // Admin only routes
        Route::middleware('admin')->group(function () {
            // Hero
            Route::put('/hero', [HeroController::class, 'update']);
            Route::post('/hero/image', [HeroController::class, 'uploadImage']);
            Route::delete('/hero/image', [HeroController::class, 'deleteImage']);
            
            // Berita
            Route::post('/beritas', [BeritaController::class, 'store']);
            Route::put('/beritas/{id}', [BeritaController::class, 'update']);
            Route::delete('/beritas/{id}', [BeritaController::class, 'destroy']);
            Route::patch('/beritas/{id}/toggle-publish', [BeritaController::class, 'togglePublish']);
            
            // Jadwal
            Route::post('/jadwals', [JadwalController::class, 'store']);
            Route::put('/jadwals/{id}', [JadwalController::class, 'update']);
            Route::delete('/jadwals/{id}', [JadwalController::class, 'destroy']);
            
            // Users
            Route::get('/users', [AuthController::class, 'getUsers']);
        });
    });
});