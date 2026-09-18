@php
    $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower($user->email)])->first();
@endphp

<section>
    <header class="mb-6">
        <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modificar Datos del Perfil
        </h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Actualiza tu información personal, teléfono y contacto de emergencia.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        @if (session('status') === 'profile-updated')
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 rounded-2xl text-xs font-bold flex items-center gap-2">
                <span>✅</span> La información de tu perfil se ha guardado correctamente.
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" value="Nombre y Apellidos" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('name', $user->name)" required autocomplete="name" />
                <x-input-error class="mt-1" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" value="Correo Electrónico" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 bg-gray-50 dark:bg-gray-800/60" :value="old('email', $user->email)" required readonly />
                <x-input-error class="mt-1" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="telefono" value="Teléfono Móvil Principal" />
                <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('telefono', $user->telefono ?: ($empleado->telefono_principal ?? ''))" autocomplete="tel" placeholder="Ej: 600123456" />
                <x-input-error class="mt-1" :messages="$errors->get('telefono')" />
            </div>

            <div>
                <x-input-label for="direccion" value="Dirección" />
                <x-text-input id="direccion" name="direccion" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('direccion', $empleado->direccion ?? '')" placeholder="Calle, número, piso" />
            </div>

            <div>
                <x-input-label for="localidad" value="Localidad" />
                <x-text-input id="localidad" name="localidad" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('localidad', $empleado->localidad ?? '')" placeholder="Ej: Utrera" />
            </div>

            <div>
                <x-input-label for="provincia" value="Provincia" />
                <x-text-input id="provincia" name="provincia" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('provincia', $empleado->provincia ?? '')" placeholder="Ej: Sevilla" />
            </div>

            <div>
                <x-input-label for="contacto_emergencia_nombre" value="Contacto de Emergencia (Nombre)" />
                <x-text-input id="contacto_emergencia_nombre" name="contacto_emergencia_nombre" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('contacto_emergencia_nombre', $empleado->contacto_emergencia_nombre ?? '')" placeholder="Familiar o persona de contacto" />
            </div>

            <div>
                <x-input-label for="contacto_emergencia_telefono" value="Contacto de Emergencia (Teléfono)" />
                <x-text-input id="contacto_emergencia_telefono" name="contacto_emergencia_telefono" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('contacto_emergencia_telefono', $empleado->contacto_emergencia_telefono ?? '')" placeholder="Teléfono de emergencia" />
            </div>
        </div>

        {{-- BOTÓN DE GUARDAR ABAJO DEL TODO --}}
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-white/10">
            <button type="button" @click="editMode = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl transition-all">
                Cancelar
            </button>
            <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-600/20 transition-all focus:outline-none">
                Guardar Cambios
            </button>
        </div>
    </form>
</section>
