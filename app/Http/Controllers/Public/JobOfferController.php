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

        // Honeypot check: if filled, silently succeed without saving to DB
        if ($request->filled('website_url_check')) {
            return redirect()->route('offers.show', $offer->id)
                ->with('success', '¡Tu candidatura se ha enviado correctamente! Muchas gracias por tu interés.');
        }

        // Invisible antispam check (Honeypot + JS execution check)
        if (!$request->has('security_check')) {
            return redirect()->route('offers.show', $offer->id)
                ->with('success', '¡Tu candidatura se ha enviado correctamente! Muchas gracias por tu interés.');
        }

        try {
            $decrypted = decrypt($request->security_check);
            $todayKey = date("Y-m-d") . "_utrecar_human_key";
            $yesterdayKey = date("Y-m-d", strtotime("-1 day")) . "_utrecar_human_key";
            
            if ($decrypted !== $todayKey && $decrypted !== $yesterdayKey) {
                return redirect()->route('offers.show', $offer->id)
                    ->with('success', '¡Tu candidatura se ha enviado correctamente! Muchas gracias por tu interés.');
            }
        } catch (\Exception $e) {
            return redirect()->route('offers.show', $offer->id)
                ->with('success', '¡Tu candidatura se ha enviado correctamente! Muchas gracias por tu interés.');
        }

        $request->validate([
            'first_name'          => 'required|string|max:255',
            'last_name'           => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'phone'               => 'required|string|max:50',
            'profile_description' => 'nullable|string',
            'years_of_experience' => 'nullable|string|max:255',
            'incorporation_time'  => 'nullable|string|max:255',
            'travel_possibility'  => 'nullable|boolean',
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
            'years_of_experience' => $request->input('years_of_experience'),
            'incorporation_time'  => $request->input('incorporation_time'),
            'travel_possibility'  => $request->has('travel_possibility'),
            'cv_path'             => $cvPath,
        ]);

        return redirect()->route('offers.show', $offer->id)
            ->with('success', '¡Tu candidatura se ha enviado correctamente! Muchas gracias por tu interés.');
    }
}