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

        if ($user) {
            $isEmpleado = $user->hasRole('Empleado') || $user->hasRole('empleado');
            if ($isEmpleado && \Illuminate\Support\Facades\Hash::check('1234', $user->password)) {
                if (!$request->is('profile*') && !$request->is('password*') && !$request->is('logout') && !$request->is('admin/logout')) {
                    \Illuminate\Support\Facades\Log::info("Redirecting employee with default password '1234' to profile page", [
                        'user_email' => $user->email
                    ]);
                    session()->flash('warning', 'Por motivos de seguridad, debes cambiar tu contraseña por defecto (1234) antes de continuar.');
                    return redirect()->route('profile.edit');
                }
            }
        }

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
