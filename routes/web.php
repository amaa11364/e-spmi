<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\LandingPageController;

/*
|--------------------------------------------------------------------------
| API Routes (for Vue.js)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    
    // Public routes
    Route::get('/landing-content', [LandingPageController::class, 'index']);
    Route::get('/hero', [HeroController::class, 'index']);
    Route::get('/beritas/latest', [BeritaController::class, 'latest']);
    Route::get('/beritas', [BeritaController::class, 'index']);
    Route::get('/beritas/{id}', [BeritaController::class, 'show']);
    Route::get('/jadwals/upcoming', [JadwalController::class, 'upcoming']);
    Route::get('/jadwals/date-range', [JadwalController::class, 'getByDateRange']);
    Route::get('/jadwals', [JadwalController::class, 'index']);
    Route::get('/jadwals/{id}', [JadwalController::class, 'show']);
    
    // Dokumen publik routes
    Route::get('/dokumen/public', [DokumenController::class, 'publicFolders']);
    Route::get('/dokumen/files/{id}/download', [DokumenController::class, 'downloadFile']);
    
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
            
            // Landing Page Content (Team & Documentation)
            Route::get('/landing/team', [LandingPageController::class, 'getTeam']);
            Route::post('/landing/team', [LandingPageController::class, 'storeTeamMember']);
            Route::put('/landing/team/{id}', [LandingPageController::class, 'updateTeamMember']);
            Route::delete('/landing/team/{id}', [LandingPageController::class, 'destroyTeamMember']);
            Route::post('/landing/image', [LandingPageController::class, 'uploadImage']);
            Route::get('/landing/docs', [LandingPageController::class, 'getDocumentations']);
            Route::post('/landing/docs', [LandingPageController::class, 'storeDocumentation']);
            Route::put('/landing/docs/{id}', [LandingPageController::class, 'updateDocumentation']);
            Route::delete('/landing/docs/{id}', [LandingPageController::class, 'destroyDocumentation']);
            
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
            
            // Dokumen Management
            Route::get('/dokumen/folders', [DokumenController::class, 'index']);
            Route::post('/dokumen/folders', [DokumenController::class, 'storeFolder']);
            Route::put('/dokumen/folders/{id}', [DokumenController::class, 'updateFolder']);
            Route::delete('/dokumen/folders/{id}', [DokumenController::class, 'destroyFolder']);
            Route::patch('/dokumen/folders/{id}/toggle-public', [DokumenController::class, 'toggleFolderPublic']);
            Route::post('/dokumen/folders/{folderId}/files', [DokumenController::class, 'storeFile']);
            Route::put('/dokumen/files/{id}', [DokumenController::class, 'updateFile']);
            Route::delete('/dokumen/files/{id}', [DokumenController::class, 'destroyFile']);
            Route::patch('/dokumen/files/{id}/toggle-public', [DokumenController::class, 'toggleFilePublic']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Vue SPA Catch-All Route
|--------------------------------------------------------------------------
| This MUST be the last route to avoid blocking API routes.
*/

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');