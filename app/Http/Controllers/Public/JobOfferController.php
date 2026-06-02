<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use App\Models\Gasolinera;
use App\Models\PreciosProducto;
use App\Models\HomeConfig;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    /**
     * Muestra el listado público de ofertas dentro del layout de la Home.
     */
    public function index()
    {
        $offers = JobOffer::where('active', true)->orderBy('created_at', 'desc')->get();
        $gasolineras = Gasolinera::with('contenido')
            ->get()
            ->map(function ($estacion) {
                $estacion->diesel = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                    ->where('CodigoProducto', '1')
                    ->value('PVP');
                $estacion->gasolina95 = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                    ->where('CodigoProducto', '2')
                    ->value('PVP');
                return $estacion;
            });
        $homeConfig = HomeConfig::find(1);
        return view('offers.index', [
            'offers'      => $offers,
            'gasolineras' => $gasolineras,
            'homeConfig'  => $homeConfig,
        ]);
    }

    /**
     * Muestra el detalle de una oferta específica dentro del layout de la Home.
     */
    public function show($id)
    {
        $offer = JobOffer::where('active', true)->findOrFail($id);
        
        $gasolineras = Gasolinera::with('contenido')
            ->get()
            ->map(function ($estacion) {
                $estacion->diesel = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                    ->where('CodigoProducto', '1')
                    ->value('PVP');
                $estacion->gasolina95 = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                    ->where('CodigoProducto', '2')
                    ->value('PVP');
                return $estacion;
            });
        $homeConfig = HomeConfig::find(1);
        
        return view('offers.show', [
            'offer'       => $offer,
            'gasolineras' => $gasolineras,
            'homeConfig'  => $homeConfig,
        ]);
    }

    /**
     * Procesa la inscripción de un candidato y almacena su currículum.
     */
    public function apply(Request $request, $id)
    {
        $offer = JobOffer::where('active', true)->findOrFail($id);

        $request->validate([
            'first_name'          => 'required|string|max:255',
            'last_name'           => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'phone'               => 'required|string|max:50',
            'profile_description' => 'nullable|string',
            'cv'                  => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Guardar el CV en el disco privado configurado para candidaturas
        $cvPath = $request->file('cv')->store('cvs', 'private_cvs');

        JobApplication::create([
            'job_offer_id'        => $offer->id,
            'first_name'          => $request->input('first_name'),
            'last_name'           => $request->input('last_name'),
            'email'               => $request->input('email'),
            'phone'               => $request->input('phone'),
            'profile_description' => $request->input('profile_description'),
            'cv_path'             => $cvPath,
        ]);

        return redirect()->route('offers.show', $offer->id)
            ->with('success', '¡Tu candidatura se ha enviado correctamente! Muchas gracias por tu interés.');
    }
}