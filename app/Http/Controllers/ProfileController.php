<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasRole(['Administrador', 'admin', 'Admin', 'Gestor', 'gestor', 'CEO'])
            || $user->can('gestion_recursos_humanos')
            || $user->id === 1
            || $user->email === 'jarodriguezbonilla@gmail.com';

        $validated = $request->validated();

        // El rol empleado no puede modificar su nombre ni DNI
        if (!$isAdmin) {
            unset($validated['name']);
        }

        $oldEmail = $user->getOriginal('email');

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Sincronizar datos con el modelo Empleado si existe (buscando por el email anterior o nuevo)
        $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower($oldEmail)])
            ->orWhereRaw('LOWER(email) = ?', [strtolower($user->email)])
            ->first();

        if ($empleado) {
            $empleadoData = [
                'email' => $user->email,
                'telefono_principal' => $request->input('telefono', $empleado->telefono_principal),
                'direccion' => $request->input('direccion', $empleado->direccion),
                'localidad' => $request->input('localidad', $empleado->localidad),
                'provincia' => $request->input('provincia', $empleado->provincia),
                'codigo_postal' => $request->input('codigo_postal', $empleado->codigo_postal),
                'contacto_emergencia_nombre' => $request->input('contacto_emergencia_nombre', $empleado->contacto_emergencia_nombre),
                'contacto_emergencia_telefono' => $request->input('contacto_emergencia_telefono', $empleado->contacto_emergencia_telefono),
            ];

            if ($request->filled('iban')) {
                $empleadoData['iban'] = strtoupper(str_replace(' ', '', $request->input('iban')));
            }

            // Solo administradores pueden cambiar nombre en empleado
            if ($isAdmin && isset($validated['name']) && !empty($validated['name'])) {
                $parts = explode(' ', trim($validated['name']));
                $empleadoData['nombre'] = $parts[0];
                if (count($parts) > 1) {
                    $empleadoData['apellidos'] = implode(' ', array_slice($parts, 1));
                }
            }

            $empleado->update($empleadoData);
        }

        // Si es un empleado y aún tiene el proceso de onboarding pendiente, redirigir al asistente
        if ($empleado && !$empleado->onboarding_completado) {
            session()->flash('info', 'Perfil actualizado. Por favor, continúa con tu proceso de incorporación (onboarding).');
            return redirect()->route('empleado.onboarding');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        if (!$user->can('gestion_eliminar_usuarios') && $user->id !== 1 && $user->email !== 'jarodriguezbonilla@gmail.com') {
            abort(403, 'Acción no permitida.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
