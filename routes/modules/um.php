<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Um\DashboardController;
use App\Http\Controllers\Um\UserController;
use App\Http\Controllers\Um\UserBankController; // Added UserBankController

// 1. Dashboard
Route::controller(DashboardController::class)->group(function () {
   Route::get('/dashboard', 'index')->name('dashboard');
});

// 2. Admin Listing Route (Role = 2)
Route::get('admins', [UserController::class, 'admin'])->name('admins.index');

// 4. Users Management Routes
Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
   Route::get('/', 'index')->name('index');
   Route::get('create', 'create')->name('create');
   Route::post('store', 'store')->name('store');
   Route::get('edit/{id}', 'edit')->name('edit');
   Route::put('update/{id}', 'update')->name('update');
   Route::get('datatable', 'datatable')->name('datatable');
   Route::post('update-status', 'updateStatus')->name('update-status');
   Route::post('password-update', 'updatePassword')->name('password.update');
   Route::get('modules-update/get', 'getUserModules')->name('modules.get');
   Route::post('modules-update', 'updateUserModules')->name('modules.update');
   Route::get('export/pdf', 'exportPdf')->name('export.pdf');
});

// 5. User Bank & UPI Details Routes (Added)
Route::prefix('user-banks')->name('user-banks.')->controller(UserBankController::class)->group(function () {
   Route::get('/', 'index')->name('index');
   Route::get('datatable', 'datatable')->name('datatable');
   Route::post('store', 'store')->name('store');
   Route::put('update/{id}', 'update')->name('update');
   Route::post('update-status', 'updateStatus')->name('update-status');
   Route::delete('delete/{id}', 'destroy')->name('destroy');
   Route::get('export/pdf', 'exportPdf')->name('export.pdf');
});
