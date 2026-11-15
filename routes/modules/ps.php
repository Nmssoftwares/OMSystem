<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ps\DashboardController as PsDashboardController;
use App\Http\Controllers\Ps\ProjectController;

Route::controller(PsDashboardController::class)->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
});

Route::prefix('projects')->name('projects.')->controller(ProjectController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('create', 'create')->name('create');
    Route::post('store', 'store')->name('store');
    Route::get('edit/{id}', 'edit')->name('edit');
    Route::put('update/{id}', 'update')->name('update');
    Route::delete('delete/{id}', 'destroy')->name('destroy');
    Route::post('update-status', 'updateStatus')->name('update-status');
    Route::get('datatable', 'datatable')->name('datatable');
    Route::get('export/pdf', 'exportPdf')->name('export.pdf');
});