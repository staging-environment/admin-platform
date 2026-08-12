<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBaja
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower($user->email)])->first();

            if ($empleado) {
                if ($empleado->estado === 'Baja') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('filament.admin.auth.login')->withErrors([
                        'email' => 'Tu usuario se encuentra dado de baja en el sistema. El acceso ha sido bloqueado.',
                    ]);
                }

                if ($empleado->estaSuspendido()) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('filament.admin.auth.login')->withErrors([
                        'email' => 'Tu cuenta se encuentra en período de suspensión de empleo y sueldo. El acceso está temporalmente bloqueado.',
                    ]);
                }
            }
        }

        return $next($request);
    }
}
