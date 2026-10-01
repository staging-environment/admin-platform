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
        // Permitir siempre salir de la suplantación y logout
        if ($request->is('filament-impersonate*') || $request->is('*impersonate*') || $request->is('logout') || $request->is('admin/logout')) {
            return $next($request);
        }

        $user = auth()->user() ?: (\class_exists(\Filament\Facades\Filament::class) ? \Filament\Facades\Filament::auth()->user() : null);

        if ($user) {
            $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower($user->email)])->first();

            $isEmpleado = $user->hasRole('Empleado') || ($empleado && !$user->hasRole(['Admin', 'admin', 'Administrador', 'CEO', 'Gestor']));
            $isDefaultPassword = Hash::check('1234', $user->password);

            // Si es rol empleado y tiene contraseña por defecto o tiene onboarding incompleto -> Redirigir a onboarding
            if ($isEmpleado) {
                if ($isDefaultPassword || ($empleado && !$empleado->onboarding_completado)) {
                    if (!$request->is('portal/onboarding*') && !$request->is('livewire*') && !$request->is('logout') && !$request->is('admin/logout') && !$request->is('filament-impersonate*') && !$request->is('*impersonate*')) {
                        return redirect()->route('empleado.onboarding');
                    }
                }
            } elseif ($isDefaultPassword) {
                // Solo administradores u otros roles: exigir cambio en perfil
                if ($request->is('admin*') || $request->is('dashboard*')) {
                    if (!$request->is('profile*') && !$request->is('password*') && !$request->is('logout') && !$request->is('admin/logout') && !$request->is('portal/onboarding*') && !$request->is('livewire*') && !$request->is('filament-impersonate*') && !$request->is('*impersonate*')) {
                        session()->flash('warning', 'Por motivos de seguridad, debes cambiar tu contraseña por defecto (1234) antes de acceder a las secciones de administración.');
                        return redirect()->route('profile.edit');
                    }
                }
            }
        }

        return $next($request);
    }
}
