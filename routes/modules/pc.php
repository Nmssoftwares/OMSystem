<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pc\DashboardController;

Route::controller(DashboardController::class)->group(function () {
   Route::get('/dashboard', 'index')->name('dashboard');
});
