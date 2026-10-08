<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LearnerController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - ALS SDO San Juan City
|--------------------------------------------------------------------------
| Prefix: /api/v1
*/

Route::prefix('v1')->group(function () {
    // Authentication Endpoints
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // ALS AI Assistant
    Route::post('/chat', [ChatController::class, 'sendMessage']);

    // Learning Strands & Published Modules (Publicly accessible for preview/offline app cache)
    Route::get('/strands', [ModuleController::class, 'getStrands']);
    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/modules/{id}', [ModuleController::class, 'show']);
    Route::get('/modules/{id}/download', [ModuleController::class, 'download']);

    // Protected Routes (Requires Bearer Token)
    Route::middleware('auth:sanctum')->group(function () {
        // Authenticated Session
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Learner Specific Operations
        Route::middleware('role:learner,admin,mobile_teacher')->group(function () {
            Route::post('/learner/documents/upload', [LearnerController::class, 'uploadDocument']);
            Route::get('/learner/enrollment-status', [LearnerController::class, 'getEnrollmentStatus']);
            Route::post('/learner/sync-progress', [LearnerController::class, 'syncProgress']);
        });

        // Administrator & Mobile Teacher Reports & Exports
        Route::middleware('role:admin,mobile_teacher')->group(function () {
            Route::get('/admin/reports/form-1', [ReportController::class, 'exportForm1']);
            Route::get('/admin/reports/form-2/{learner_id}', [ReportController::class, 'exportForm2']);
        });
    });
});
