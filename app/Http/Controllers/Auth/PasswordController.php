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
            'current_password.current_password' => 'La contraseña actual introducida no es correcta.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];

        // Si tiene la contraseña por defecto '1234' o se envían los checks normativos, exigir validación estricta
        if ($isDefaultPassword || $request->has('check_normativas_present')) {
            $rules['acepta_rgpd'] = ['accepted'];
            $rules['acepta_normativa'] = ['accepted'];
            $rules['acepta_prl'] = ['accepted'];

            $messages['acepta_rgpd.accepted'] = 'Debes leer y aceptar el cumplimiento del RGPD / Protección de Datos.';
            $messages['acepta_normativa.accepted'] = 'Debes aceptar la Normativa Interna y Código de Conducta de la empresa.';
            $messages['acepta_prl.accepted'] = 'Debes aceptar las Normas de Prevención de Riesgos Laborales (PRL).';
        }

        $validated = $request->validateWithBag('updatePassword', $rules, $messages);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Si es un empleado, registrar la fecha de aceptación de políticas
        $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower($user->email)])->first();
        if ($empleado) {
            $empleado->update([
                'politicas_aceptadas_at' => Carbon::now(),
            ]);
        }

        // Mantener la sesión activa para evitar deslogueo por hash refresh
        $request->session()->put('password_hash_web', $user->getAuthPassword());
        $request->session()->put('password_hash_' . \Illuminate\Support\Facades\Auth::getDefaultDriver(), $user->getAuthPassword());

        foreach (array_keys(config('auth.guards')) as $guard) {
            if (\Illuminate\Support\Facades\Auth::guard($guard)->check()) {
                $request->session()->put('password_hash_' . $guard, $user->getAuthPassword());
            }
        }

        session()->flash('status', 'password-updated');

        if ($user->can('gestion_recursos_humanos')) {
            return redirect('/admin/recursos-humanos');
        }

        if ($user->can('acceder_portal_fichajes')) {
            return redirect('/admin/portal-empleado');
        }

        return redirect()->route('profile.edit');
    }
}
