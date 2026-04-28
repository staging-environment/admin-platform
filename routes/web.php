<?php

use App\Http\Controllers\Api\DataQueryController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('api')->group(function () {
        Route::get('/databases/tables', [DataQueryController::class, 'getTables']);

        Route::post('/data/query', [DataQueryController::class, 'query']);
        Route::post('/data/custom-query', [DataQueryController::class, 'customQuery']);
        Route::get('/data/schema', [DataQueryController::class, 'getSchema']);

        Route::apiResource('/filters', FilterController::class);
    });
});

require __DIR__.'/auth.php';
