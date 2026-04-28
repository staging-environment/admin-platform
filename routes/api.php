<?php

use App\Http\Controllers\Api\DataQueryController;
use App\Http\Controllers\Api\FilterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes for Data Queries
Route::middleware('auth:sanctum')->group(function () {
    // Database information
    Route::get('/databases/tables', [DataQueryController::class, 'getTables']);

    // Data queries
    Route::post('/data/query', [DataQueryController::class, 'query']);
    Route::post('/data/custom-query', [DataQueryController::class, 'customQuery']);
    Route::get('/data/schema', [DataQueryController::class, 'getSchema']);

    // Filters
    Route::apiResource('/filters', FilterController::class);
});

