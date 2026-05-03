<?php

use Illuminate\Support\Facades\Route;
use App\Models\Gasolinera;
use App\Models\PreciosProducto;

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

        $extras = [
            'descripcion' => "Estación de servicio Utrecar en {$estacion->Poblacion}. Calidad garantizada por la red Virtusgesnet.",
            'horario' => 'Abierto 24 Horas',
            'servicios' => ['Tienda', 'Lavado', 'Café', 'Aire/Agua'],
            'rating' => rand(40, 50) / 10,
        ];
        return view('estacion-detalle', compact('estacion', 'extras'));
    } catch (\Exception $e) { return redirect('/'); }
})->name('estacion.show');

require __DIR__.'/auth.php';
