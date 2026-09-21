<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isDefaultPassword = Hash::check('1234', $user->password);

        $rules = [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::min(8), 'confirmed'],
        ];

        $messages = [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'current_password.current_password' => 'La contraseña actual introducida no es correcta (introduce tu contraseña actual o 1234).',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ];

        // Si tiene la contraseña por defecto '1234' o se envían los checks normativos, exigir validación estricta
        if ($isDefaultPassword || $request->has('check_normativas_present')) {
            $rules['acepta_rgpd'] = ['accepted'];
            $rules['acepta_normativa'] = ['accepted'];
            $rules['acepta_prl'] = ['accepted'];

            $messages['acepta_rgpd.accepted'] = 'Debes marcar la casilla de aceptación del RGPD / Protección de Datos.';
            $messages['acepta_normativa.accepted'] = 'Debes marcar la casilla de aceptación de la Normativa Interna.';
            $messages['acepta_prl.accepted'] = 'Debes marcar la casilla de aceptación de Prevención de Riesgos Laborales (PRL).';
        }

        $validated = $request->validateWithBag('updatePassword', $rules, $messages);

        // Guardar la nueva contraseña con forceFill para evitar problemas de casting
        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        // Si es un empleado, registrar la fecha de aceptación de políticas
        $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower($user->email)])->first();
        if ($empleado) {
            $empleado->update([
                'politicas_aceptadas_at' => Carbon::now(),
            ]);
        }

        // Preservar estado de suplantación si existe
        $impersonatorId = $request->session()->get('impersonated_by');
        $impersonatorGuard = $request->session()->get('impersonator_guard');
        $impersonatorGuardUsing = $request->session()->get('impersonator_guard_using');
        $impersonateBackTo = $request->session()->get('impersonate.back_to');
        $impersonateGuard = $request->session()->get('impersonate.guard');

        // Mantener la sesión activa para evitar deslogueo por hash refresh
        \Illuminate\Support\Facades\Auth::guard('web')->login($user);
        $request->session()->put('password_hash_web', $user->getAuthPassword());
        $request->session()->put('password_hash_' . \Illuminate\Support\Facades\Auth::getDefaultDriver(), $user->getAuthPassword());

        if (class_exists(\Filament\Facades\Filament::class) && \Filament\Facades\Filament::auth()) {
            try {
                $guard = \Filament\Facades\Filament::getAuthGuard() ?: 'web';
                \Filament\Facades\Filament::auth()->login($user);
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        // Restaurar estado de suplantación si estaba presente
        if ($impersonatorId) {
            $request->session()->put('impersonated_by', $impersonatorId);
            $request->session()->put('impersonator_guard', $impersonatorGuard ?: 'web');
            $request->session()->put('impersonator_guard_using', $impersonatorGuardUsing ?: 'web');
            $request->session()->put('impersonate.back_to', $impersonateBackTo ?: '/admin/recursos-humanos');
            if ($impersonateGuard) {
                $request->session()->put('impersonate.guard', $impersonateGuard);
            }
        }

        session()->flash('status', 'password-updated');
        session()->flash('success', '¡Contraseña actualizada y normativas aceptadas correctamente!');

        if ($user->can('gestion_recursos_humanos') || $user->hasRole('Administrador') || $user->hasRole('Gestor')) {
            return redirect('/admin/recursos-humanos');
        }

        // Si es un empleado con onboarding pendiente, redirigir al proceso de onboarding
        if ($empleado && !$empleado->onboarding_completado) {
            session()->flash('info', 'Contraseña actualizada. Por favor, continúa con los pasos de tu incorporación.');
            return redirect()->route('empleado.onboarding');
        }

        return redirect('/admin/portal-empleado');
    }
}
