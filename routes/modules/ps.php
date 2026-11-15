<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ps\DashboardController as PsDashboardController;
use App\Http\Controllers\Ps\ProjectController;

// 1. PS Dashboard Route (/ps/dashboard)
Route::controller(PsDashboardController::class)->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
});

Route::get('projects/export/pdf', [ProjectController::class, 'exportPdf'])->name('projects.export.pdf');

// 2. Projects Management Routes (/ps/projects, /ps/projects/datatable)
Route::prefix('projects')->name('projects.')->controller(ProjectController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/datatable', 'datatable')->name('datatable');
});
