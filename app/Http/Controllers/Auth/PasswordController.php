<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);

        $user = $request->user();
        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        // Actualizar el hash de contraseña en la sesión para evitar que AuthenticateSession desloguee al usuario
        \Illuminate\Support\Facades\Auth::guard('web')->login($user);
        $request->session()->put('password_hash_web', $user->getAuthPassword());
        $request->session()->put('password_hash_' . \Illuminate\Support\Facades\Auth::getDefaultDriver(), $user->getAuthPassword());

        if (class_exists(\Filament\Facades\Filament::class) && \Filament\Facades\Filament::auth()) {
            try {
                $guard = \Filament\Facades\Filament::getAuthGuard() ?: 'web';
                \Filament\Facades\Filament::auth()->login($user);
                $request->session()->put('password_hash_' . $guard, $user->getAuthPassword());
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        if (class_exists(\Filament\Notifications\Notification::class)) {
            \Filament\Notifications\Notification::make()
                ->title('Contraseña actualizada correctamente')
                ->body('Tu contraseña ha sido cambiada con éxito. Ya puedes navegar libremente por la plataforma.')
                ->success()
                ->send();
        }

        session()->flash('status', 'password-updated');
        session()->flash('success', 'Tu contraseña ha sido cambiada con éxito.');

        if ($user->hasRole('Empleado') || $user->hasRole('empleado') || $user->can('ver_ficha_empleado')) {
            return redirect('/admin/ficha-empleado');
        }

        if ($user->can('gestion_recursos_humanos')) {
            return redirect('/admin/recursos-humanos');
        }

        return redirect('/admin');
    }
}
