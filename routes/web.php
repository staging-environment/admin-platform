<?php

use Illuminate\Support\Facades\Route;
use App\Models\Gasolinera;
use App\Models\PreciosProducto;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\FtpUserController; // <-- Importamos tu nuevo controlador
use App\Http\Controllers\Api\DataQueryController;
use App\Http\Controllers\Api\FilterController;

// --- SECCIÓN PÚBLICA ---
Route::get('/', function () {
    $gasolineras = [];
    try {
        $gasolineras = Gasolinera::take(4)->get()->map(function ($estacion) {
            $estacion->diesel = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                ->where('CodigoProducto', '1')->value('PVP');
            $estacion->gasolina95 = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                ->where('CodigoProducto', '2')->value('PVP');
            return $estacion;
        });
    } catch (\Exception $e) { report($e); }
    return view('welcome', compact('gasolineras'));
});

Route::get('/estacion/{codigo}', function ($codigo) {
    try {
        $estacion = Gasolinera::where('Codigo', $codigo)->firstOrFail();
        $estacion->diesel = PreciosProducto::where('CodigoEstacion', $codigo)->where('CodigoProducto', '1')->value('PVP');
        $estacion->gasolina95 = PreciosProducto::where('CodigoEstacion', $codigo)->where('CodigoProducto', '2')->value('PVP');
        $extras = ['descripcion' => "Estación Utrecar en {$estacion->Poblacion}.", 'horario' => '24h', 'servicios' => ['Tienda', 'Lavado'], 'rating' => rand(40,50)/10];
        return view('estacion-detalle', compact('estacion', 'extras'));
    } catch (\Exception $e) { return redirect('/'); }
})->name('estacion.show');

// --- SECCIÓN PRIVADA (BACKEND) ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ESTA ES LA RUTA QUE FALLABA:
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // Gestión de usuarios de la plataforma y del repositorio FTP
    Route::middleware('permission:manage-users')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);

        // --- NUEVAS RUTAS PARA EL FTP ---
        Route::get('/ftp-users', [FtpUserController::class, 'index'])->name('ftp.index');
        Route::post('/ftp-users', [FtpUserController::class, 'store'])->name('ftp.store');
        Route::delete('/ftp-users/{id}', [FtpUserController::class, 'destroy'])->name('ftp.destroy');

        // Rutas de gestión de archivos dentro del panel
        Route::get('/ftp-users/{username}', [FtpUserController::class, 'show'])->name('ftp.show');
        Route::post('/ftp-users/{username}/upload', [FtpUserController::class, 'upload'])->name('ftp.upload');
        Route::get('/ftp-users/{username}/download/{filename}', [FtpUserController::class, 'download'])->name('ftp.download');
        Route::delete('/ftp-users/{username}/file/{filename}', [FtpUserController::class, 'deleteFile'])->name('ftp.deleteFile');
    });

    // Rutas de la API para el panel
    Route::prefix('api')->group(function () {
        Route::get('/databases/tables', [DataQueryController::class, 'getTables']);
        Route::post('/data/query', [DataQueryController::class, 'query']);
        Route::post('/data/custom-query', [DataQueryController::class, 'customQuery']);
        Route::get('/data/schema', [DataQueryController::class, 'getSchema']);
        Route::apiResource('/filters', FilterController::class);
    });
});

require __DIR__.'/auth.php';
