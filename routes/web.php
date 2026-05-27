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
use App\Models\ContactoMensaje;
use Illuminate\Http\Request;

// --- SECCIÓN PÚBLICA ---
Route::get('/', function () {
    $gasolineras = [];
    $homeConfig = null;
    try {
        $homeConfig = \App\Models\HomeConfig::find(1);
        $gasolineras = Gasolinera::with('contenido')->get()->map(function ($estacion) {
            $estacion->diesel = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                ->where('CodigoProducto', '1')->value('PVP');
            $estacion->gasolina95 = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                ->where('CodigoProducto', '2')->value('PVP');
            return $estacion;
        });
    } catch (\Exception $e) { report($e); }
    return view('welcome', compact('gasolineras', 'homeConfig'));
});

Route::get('/estacion/{slug}', function ($slug) {
    try {
        $estacion = Gasolinera::with('contenido')->get()->first(function ($e) use ($slug) {
            return \Illuminate\Support\Str::slug($e->Nombre) === $slug || $e->Codigo == $slug;
        });
        
        if (!$estacion) {
            return redirect('/');
        }
        
        $codigo = $estacion->Codigo;
        $estacion->diesel = PreciosProducto::where('CodigoEstacion', $codigo)->where('CodigoProducto', '1')->value('PVP');
        $estacion->gasolina95 = PreciosProducto::where('CodigoEstacion', $codigo)->where('CodigoProducto', '2')->value('PVP');
        $homeConfig = \App\Models\HomeConfig::find(1);
        return view('estacion-detalle', compact('estacion', 'homeConfig'));
    } catch (\Exception $e) { return redirect('/'); }
})->name('estacion.show');

Route::post('/estacion/{slug}/contacto', function (Request $request, $slug) {
    $estacion = Gasolinera::get()->first(function ($e) use ($slug) {
        return \Illuminate\Support\Str::slug($e->Nombre) === $slug || $e->Codigo == $slug;
    });
    
    if (!$estacion) abort(404);
    
    $codigo = $estacion->Codigo;
    $request->validate([
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'mensaje' => 'required|string',
    ]);

    ContactoMensaje::create([
        'gasolinera_codigo' => $codigo,
        'nombre' => $request->nombre,
        'email' => $request->email,
        'mensaje' => $request->mensaje,
    ]);

    return redirect()->back()->with('success', 'Tu mensaje ha sido enviado correctamente.');
})->name('estacion.contacto');

Route::post('/contacto', function (Request $request) {
    $request->validate([
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'mensaje' => 'required|string',
    ]);

    ContactoMensaje::create([
        'gasolinera_codigo' => null,
        'nombre' => $request->nombre,
        'email' => $request->email,
        'mensaje' => $request->mensaje,
    ]);

    return redirect()->back()->with('success', 'Tu mensaje ha sido enviado correctamente.');
})->name('home.contacto');

// --- SECCIÓN PRIVADA (BACKEND) ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/dashboard', '/admin/dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // Gestión de usuarios de la plataforma y del repositorio FTP
    Route::middleware('permission:manage-users')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);


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
