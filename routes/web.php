<?php

use Illuminate\Support\Facades\Route;
use App\Models\Gasolinera;
use App\Models\PreciosProducto;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Api\DataQueryController;
use App\Http\Controllers\Api\FilterController;

Route::get('/', function () {
    $gasolineras = [];
    try {
        // Obtenemos las estaciones y mapeamos sus precios específicos
        $gasolineras = Gasolinera::take(4)->get()->map(function ($estacion) {

            // Buscamos el PVP para GASOLEO A (Código 1)
            $estacion->diesel = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                ->where('CodigoProducto', '1')
                ->value('PVP');

            // Buscamos el PVP para SIN PLOMO 95 (Código 2)
            $estacion->gasolina95 = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                ->where('CodigoProducto', '2')
                ->value('PVP');

            return $estacion;
        });
    } catch (\Exception $e) {
        // Si la conexión falla, se registra el error sin tumbar la web
        report($e);
    }
    return view('welcome', compact('gasolineras'));
});

// Rutas de administración y autenticación (se mantienen igual)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:Admin')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::middleware('permission:manage-users')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
    });

    Route::prefix('api')->group(function () {
        Route::get('/databases/tables', [DataQueryController::class, 'getTables']);
        Route::post('/data/query', [DataQueryController::class, 'query']);
        Route::post('/data/custom-query', [DataQueryController::class, 'customQuery']);
        Route::get('/data/schema', [DataQueryController::class, 'getSchema']);
        Route::apiResource('/filters', FilterController::class);
    });
});

require __DIR__.'/auth.php';
