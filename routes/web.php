<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\LearningPlanController;
use App\Http\Controllers\Student\MaterialController as StudentMaterialController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Student\ScheduleController as StudentScheduleController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\MaterialController as TeacherMaterialController;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfileController;
use App\Http\Controllers\Teacher\ReportController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use App\Http\Controllers\Teacher\TeachingSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

    Route::get('/materials', [TeacherMaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/create', [TeacherMaterialController::class, 'create'])->name('materials.create');
    Route::post('/materials', [TeacherMaterialController::class, 'store'])->name('materials.store');
    Route::get('/materials/{material}/edit', [TeacherMaterialController::class, 'edit'])->name('materials.edit');
    Route::put('/materials/{material}', [TeacherMaterialController::class, 'update'])->name('materials.update');
    Route::delete('/materials/{material}', [TeacherMaterialController::class, 'destroy'])->name('materials.destroy');
    Route::get('/material-files/{file}', [TeacherMaterialController::class, 'download'])->name('materials.files.download');

    Route::get('/schedules', [TeachingSessionController::class, 'index'])->name('sessions.index');
    Route::get('/schedules/create', [TeachingSessionController::class, 'create'])->name('sessions.create');
    Route::post('/schedules', [TeachingSessionController::class, 'store'])->name('sessions.store');
    Route::get('/schedules/{session}/edit', [TeachingSessionController::class, 'edit'])->name('sessions.edit');
    Route::put('/schedules/{session}', [TeachingSessionController::class, 'update'])->name('sessions.update');
    Route::delete('/schedules/{session}', [TeachingSessionController::class, 'destroy'])->name('sessions.destroy');

    Route::get('/students', [TeacherStudentController::class, 'index'])->name('students.index');
    Route::post('/students/assign', [TeacherStudentController::class, 'assign'])->name('students.assign');
    Route::delete('/students/{student}/subjects/{subject}', [TeacherStudentController::class, 'detach'])->name('students.detach');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/profile', [TeacherProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [TeacherProfileController::class, 'update'])->name('profile.update');
});

Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/materials', [StudentMaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{material}', [StudentMaterialController::class, 'show'])->name('materials.show');
    Route::get('/material-files/{file}', [StudentMaterialController::class, 'download'])->name('materials.files.download');

    Route::get('/schedules', [StudentScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/plans', [LearningPlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [LearningPlanController::class, 'store'])->name('plans.store');
    Route::put('/plans/{plan}', [LearningPlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [LearningPlanController::class, 'destroy'])->name('plans.destroy');

    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::put('/progress/{material}', [ProgressController::class, 'update'])->name('progress.update');
    Route::get('/profile', [StudentProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [StudentProfileController::class, 'update'])->name('profile.update');
});
