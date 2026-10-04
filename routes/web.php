<?php

use App\Http\Controllers\Web\Admin\ClcController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\EnrollmentController;
use App\Http\Controllers\Web\Admin\ModuleController as AdminModuleController;
use App\Http\Controllers\Web\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\Learner\LearnerPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - ALS SDO San Juan City
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-als', [HomeController::class, 'showPage'])->defaults('pageKey', 'about')->name('about');
Route::get('/programs', [HomeController::class, 'showPage'])->defaults('pageKey', 'programs')->name('programs');
Route::get('/requirements', [HomeController::class, 'showPage'])->defaults('pageKey', 'requirements')->name('requirements');
Route::get('/learning-centers', [HomeController::class, 'showPage'])->defaults('pageKey', 'centers')->name('centers');
Route::get('/contact', [HomeController::class, 'showPage'])->defaults('pageKey', 'contact')->name('contact');

// Authentication & Registration
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Teacher & Administrator Protected Portal
Route::middleware(['auth', 'role:admin,mobile_teacher'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Enrollment Intake & Verification
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/enrollments/{id}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    Route::post('/enrollments/{id}/status', [EnrollmentController::class, 'updateStatus'])->name('enrollments.updateStatus');
    Route::post('/enrollments/walk-in', [EnrollmentController::class, 'walkIn'])->name('enrollments.walkIn');
    Route::post('/documents/{id}/status', [EnrollmentController::class, 'updateDocumentStatus'])->name('documents.updateStatus');

    // CLC & Allocation Manager
    Route::get('/clcs', [ClcController::class, 'index'])->name('clcs.index');
    Route::post('/clcs', [ClcController::class, 'store'])->name('clcs.store');
    Route::put('/clcs/{id}', [ClcController::class, 'update'])->name('clcs.update');
    Route::post('/clcs/reassign', [ClcController::class, 'reassignLearners'])->name('clcs.reassign');

    // Self-Learning Module Repository
    Route::get('/modules', [AdminModuleController::class, 'index'])->name('modules.index');
    Route::post('/modules', [AdminModuleController::class, 'store'])->name('modules.store');
    Route::put('/modules/{id}', [AdminModuleController::class, 'update'])->name('modules.update');
    Route::patch('/modules/{id}/toggle', [AdminModuleController::class, 'togglePublish'])->name('modules.toggle');
    Route::delete('/modules/{id}', [AdminModuleController::class, 'destroy'])->name('modules.destroy');

    // DepEd Report Generators
    Route::get('/reports/form-1', [AdminReportController::class, 'form1'])->name('reports.form1');
    Route::get('/reports/form-2', [AdminReportController::class, 'form2'])->name('reports.form2');
});

// Learner Protected Portal
Route::middleware(['auth', 'role:learner,admin,mobile_teacher'])->prefix('learner')->name('learner.')->group(function () {
    Route::get('/dashboard', [LearnerPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/documents', [LearnerPortalController::class, 'documents'])->name('documents');
    Route::post('/documents', [LearnerPortalController::class, 'uploadDocument'])->name('documents.upload');
    Route::get('/modules', [LearnerPortalController::class, 'modules'])->name('modules');
});
