<?php

use App\Http\Controllers\Web\Admin\BannerController;
use App\Http\Controllers\Web\Admin\ClcController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\EnrollmentController;
use App\Http\Controllers\Web\Admin\ModuleController as AdminModuleController;
use App\Http\Controllers\Web\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Web\SuperAdmin\TeacherManagementController;
use App\Http\Controllers\ChatController;
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

// ALS AI Virtual Assistant (Gemini)
Route::post('/chat', [ChatController::class, 'sendMessage'])->name('chat.message');

// Authentication & Registration
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Super Administrator Protected Portal (Teacher & Staff Account Management)
Route::middleware(['auth', 'role:super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/teachers', [TeacherManagementController::class, 'index'])->name('teachers.index');
    Route::post('/teachers', [TeacherManagementController::class, 'store'])->name('teachers.store');
    Route::put('/teachers/{id}', [TeacherManagementController::class, 'update'])->name('teachers.update');
    Route::patch('/teachers/{id}/toggle-status', [TeacherManagementController::class, 'toggleStatus'])->name('teachers.toggleStatus');
    Route::post('/teachers/{id}/reset-password', [TeacherManagementController::class, 'resetPassword'])->name('teachers.resetPassword');
    Route::delete('/teachers/{id}', [TeacherManagementController::class, 'destroy'])->name('teachers.destroy');
});

// Teacher & Administrator Protected Portal
Route::middleware(['auth', 'role:super_admin,admin,mobile_teacher'])->prefix('admin')->name('admin.')->group(function () {
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

    // Homepage Hero Banner Carousel Manager
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::put('/banners/{id}', [BannerController::class, 'update'])->name('banners.update');
    Route::patch('/banners/{id}/toggle', [BannerController::class, 'toggleActive'])->name('banners.toggle');
    Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // DepEd Report Generators
    Route::get('/reports/form-1', [AdminReportController::class, 'form1'])->name('reports.form1');
    Route::get('/reports/form-1/pdf', [AdminReportController::class, 'exportForm1Pdf'])->name('reports.form1.pdf');
    Route::get('/reports/form-2', [AdminReportController::class, 'form2'])->name('reports.form2');
    Route::get('/reports/form-2/{learner_id}/pdf', [AdminReportController::class, 'exportForm2Pdf'])->name('reports.form2.pdf');
});

// Learner Protected Portal
Route::middleware(['auth', 'role:learner,super_admin,admin,mobile_teacher'])->prefix('learner')->name('learner.')->group(function () {
    Route::get('/dashboard', [LearnerPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/enrollment', [LearnerPortalController::class, 'enrollment'])->name('enrollment');
    Route::get('/profile', [LearnerPortalController::class, 'profile'])->name('profile');
    Route::get('/documents', [LearnerPortalController::class, 'documents'])->name('documents');
    Route::post('/documents', [LearnerPortalController::class, 'uploadDocument'])->name('documents.upload');
    Route::get('/documents/{id}/file', [LearnerPortalController::class, 'viewDocument'])->name('documents.file');
    Route::get('/modules', [LearnerPortalController::class, 'modules'])->name('modules');
    Route::get('/modules/{id}/read', [LearnerPortalController::class, 'readModule'])->name('modules.read');
    Route::post('/modules/{id}/progress', [LearnerPortalController::class, 'updateProgress'])->name('modules.progress');
});
