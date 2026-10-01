<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user) {
            $empleado = \App\Models\Empleado::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($user->email))])->first();
            $isEmpleado = $user->hasRole('Empleado') || ($empleado && !$user->hasRole(['Admin', 'admin', 'Administrador', 'CEO', 'Gestor']));

            if ($isEmpleado && (\Illuminate\Support\Facades\Hash::check('1234', $user->password) || ($empleado && !$empleado->onboarding_completado))) {
                return redirect()->route('empleado.onboarding');
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
