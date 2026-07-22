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
        $user = auth()->user();

        if ($user) {
            \Illuminate\Support\Facades\Log::info("RedirectToDefaultPanelPage middleware running", [
                'path' => $request->path(),
                'email' => $user->email,
                'roles' => $user->getRoleNames()->toArray(),
                'can_acceder_ficha' => $user->can('acceder_ficha_empleado'),
                'is_admin' => $user->hasRole('Admin') || $user->hasRole('admin'),
            ]);
        }

        if ($user && ($request->is('admin') || $request->is('admin/'))) {
            $hasAdminAccess = $user->hasRole('Admin') || $user->hasRole('admin') || $user->can('ver_dashboard') || $user->email === 'jarodriguezbonilla@gmail.com';
            
            if (!$hasAdminAccess) {
                // If it is an Employee with Ficha access, redirect them to their portal
                if ($user->hasRole('Empleado') && $user->can('acceder_ficha_empleado')) {
                    \Illuminate\Support\Facades\Log::info("Redirecting employee to /admin/ficha-empleado");
                    return redirect('/admin/ficha-empleado');
                }
                
                // If it is a Gestor with RRHH access, redirect them to Recursos Humanos
                if ($user->can('gestion_recursos_humanos')) {
                    \Illuminate\Support\Facades\Log::info("Redirecting gestor to /admin/recursos-humanos");
                    return redirect('/admin/recursos-humanos');
                }
            }
        }

        return $next($request);
    }
}
