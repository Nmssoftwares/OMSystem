<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Um\DashboardController;
use App\Http\Controllers\Um\UserController;

// 1. Dashboard
Route::controller(DashboardController::class)->group(function () {
   Route::get('/dashboard', 'index')->name('dashboard');
});

// 2. Admin Listing Route (Role = 2)
Route::get('admins', [UserController::class, 'admin'])->name('admins.index');

// 3. Users Management Routes (Old structure preserved & extended)
Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
   Route::get('/', 'index')->name('index');
   Route::get('create', 'create')->name('create');
   Route::post('store', 'store')->name('store');
   Route::get('edit/{id}', 'edit')->name('edit');
   Route::put('update/{id}', 'update')->name('update');
   Route::get('datatable', 'datatable')->name('datatable');
   Route::post('update-status', 'updateStatus')->name('update-status'); // Status Toggle
   Route::post('password-update', 'updatePassword')->name('password.update');
   Route::get('modules-update/get', 'getUserModules')->name('modules.get');
   Route::post('modules-update', 'updateUserModules')->name('modules.update');
});
