<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user() ?: (\class_exists(\Filament\Facades\Filament::class) ? \Filament\Facades\Filament::auth()->user() : null);

        if ($user && ($user->hasRole('Empleado') || $user->hasRole('empleado'))) {
            if (Hash::check('1234', $user->password)) {
                // Permite acceder a las rutas de perfil y cierre de sesión
                if (!$request->is('profile*') && !$request->is('logout') && !$request->is('admin/logout')) {
                    session()->flash('warning', 'Por motivos de seguridad, debes cambiar tu contraseña por defecto (1234) antes de acceder a la plataforma.');
                    return redirect()->route('profile.edit');
                }
            }
        }

        return $next($request);
    }
}
