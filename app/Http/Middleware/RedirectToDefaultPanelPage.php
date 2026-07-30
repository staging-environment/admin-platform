<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToDefaultPanelPage
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user() ?: \Filament\Facades\Filament::auth()->user();

        if ($user && ($request->is('admin') || $request->is('admin/'))) {
            $isAdmin = $user->hasRole('Admin') || $user->hasRole('admin') || $user->can('ver_dashboard') || $user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1;
            
            if (!$isAdmin) {
                // Check if they already checked in today
                $empleado = \App\Models\Empleado::where('email', $user->email)->first();
                $hasCheckedInToday = false;

                if ($empleado) {
                    $hasCheckedInToday = \App\Models\EmpleadoFichaje::where('empleado_id', $empleado->id)
                        ->where('fecha', \Carbon\Carbon::today()->format('Y-m-d'))
                        ->exists();
                }

                // If they haven't checked in today, they MUST go to Fichaje first
                if (!$hasCheckedInToday) {
                    \Illuminate\Support\Facades\Log::info("Redirecting non-admin user to check-in screen (fichaje missing today)", [
                        'user_email' => $user->email
                    ]);
                    return redirect('/admin/ficha-empleado');
                }

                // If already checked in today, normal redirect logic
                if ($user->hasRole('Empleado') || $user->hasRole('empleado') || $user->can('ver_ficha_empleado')) {
                    \Illuminate\Support\Facades\Log::info("Redirecting employee to /admin/ficha-empleado");
                    return redirect('/admin/ficha-empleado');
                }
                
                if ($user->can('gestion_recursos_humanos')) {
                    \Illuminate\Support\Facades\Log::info("Redirecting gestor to /admin/recursos-humanos");
                    return redirect('/admin/recursos-humanos');
                }
            }
        }

        return $next($request);
    }
}
