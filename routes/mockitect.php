<?php

use App\Http\Controllers\Mockitect\DashboardController;
use App\Http\Controllers\Mockitect\MockController;
use App\Http\Controllers\Mockitect\RequestLogController;
use Illuminate\Support\Facades\Route;

// These routes take precedence over catch-all
Route::prefix('__mockitect')->name('mockitect.')->group(function() {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('mocks', MockController::class);
    Route::resource('logs', RequestLogController::class)->only(['index', 'show']);
});
