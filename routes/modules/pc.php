<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pc\DashboardController;
use App\Http\Controllers\Pc\PettyCashController;

// 1. Dashboard Route
Route::controller(DashboardController::class)->group(function () {
   Route::get('/dashboard', 'index')->name('dashboard');
});

// 2. Petty Cash Transactions Routes
Route::prefix('transactions')->name('transactions.')->controller(PettyCashController::class)->group(function () {
   Route::get('/', 'index')->name('index');
   Route::get('/datatable', 'datatable')->name('datatable');
   Route::get('/create', 'create')->name('create');
   Route::post('/store', 'store')->name('store');
   Route::delete('/delete/{id}', 'destroy')->name('destroy');
   Route::get('/export/pdf', 'exportPdf')->name('export.pdf');
});
