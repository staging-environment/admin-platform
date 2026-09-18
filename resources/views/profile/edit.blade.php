<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight flex items-center gap-2">
                <x-heroicon-o-user-circle class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                <span>Perfil de Usuario y Datos de Expediente</span>
            </h2>
            <a href="/admin" class="text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 flex items-center gap-1">
                ← Volver al Panel
            </a>
        </div>
    </x-slot>

    @php
        $user = auth()->user();
        $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower($user->email)])->first();
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alertas de Estado --}}
            @if (session('status') === 'password-updated')
                <div class="p-4 sm:p-5 bg-emerald-500/10 border-2 border-emerald-500/40 rounded-2xl text-emerald-900 dark:text-emerald-200 flex items-start gap-4 shadow-sm">
                    <div class="p-2 bg-emerald-500/20 rounded-xl text-emerald-600 dark:text-emerald-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-emerald-800 dark:text-emerald-300">¡Contraseña actualizada correctamente!</h3>
                        <p class="text-xs mt-0.5 text-emerald-700 dark:text-emerald-300/90">Tu contraseña ha sido guardada. Ya puedes continuar navegando con total normalidad.</p>
                    </div>
                </div>
            @endif

            @if (session('warning') || (auth()->check() && \Illuminate\Support\Facades\Hash::check('1234', $user->password)))
                <div class="p-4 sm:p-5 bg-amber-500/10 border-2 border-amber-500/40 rounded-2xl text-amber-900 dark:text-amber-200 flex items-start gap-4 shadow-sm">
                    <div class="p-2 bg-amber-500/20 rounded-xl text-amber-600 dark:text-amber-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-amber-800 dark:text-amber-300">⚠️ Cambio de contraseña obligatorio</h3>
                        <p class="text-xs mt-0.5 text-amber-800/90 dark:text-amber-200/90">Tu cuenta tiene actualmente la contraseña provisional por defecto (1234). Por motivos de seguridad, debes actualizarla a continuación.</p>
                    </div>
                </div>
            @endif

            {{-- FICHA DE DATOS DEL EMPLEADO / USUARIO (Apariencia como vista de Administración) --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-white/5 rounded-3xl p-6 sm:p-8" x-data="{ editMode: false }">
                
                {{-- Cabecera con Avatar, Datos Principales y Botón Modificar --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-gray-100 dark:border-white/5">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-600 text-white font-extrabold text-xl flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0">
                            {{ strtoupper(substr($user->name ?: 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50">
                                    {{ $empleado->estado ?? 'Activo' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $user->email }} &bull; {{ $empleado->puesto ?? ($user->getRoleNames()->first() ?? 'Empleado') }}
                            </p>
                        </div>
                    </div>

                    <button type="button" 
                            @click="editMode = !editMode" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all focus:outline-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span x-text="editMode ? 'Ver Ficha de Datos' : 'Modificar Datos'"></span>
                    </button>
                </div>

                {{-- VISTA MODO LECTURA (TIPO INFOLIST ADMIN) --}}
                <div x-show="!editMode" class="mt-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        {{-- Tarjeta 1: Datos Personales --}}
                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-white/5 space-y-3">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Datos Personales
                            </h3>
                            <div class="space-y-2 text-xs">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">Nombre Completo:</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $empleado ? "{$empleado->nombre} {$empleado->apellidos}" : $user->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">DNI / NIE:</span>
                                    <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $empleado->dni ?? 'No especificado' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">Fecha de Nacimiento:</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $empleado && $empleado->fecha_nacimiento ? \Carbon\Carbon::parse($empleado->fecha_nacimiento)->format('d/m/Y') : 'No especificada' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Tarjeta 2: Contacto y Domicilio --}}
                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-white/5 space-y-3">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Contacto y Domicilio
                            </h3>
                            <div class="space-y-2 text-xs">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">Teléfono Principal:</span>
                                    <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $user->telefono ?: ($empleado->telefono_principal ?? 'No especificado') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">Dirección:</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $empleado->direccion ?? 'No especificada' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">Localidad y Provincia:</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $empleado ? "{$empleado->localidad} ({$empleado->provincia})" : 'No especificada' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Tarjeta 3: Contacto de Emergencia y Normativas --}}
                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-white/5 space-y-3">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Emergencia y Políticas
                            </h3>
                            <div class="space-y-2 text-xs">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">Contacto de Emergencia:</span>
                                    <span class="font-bold text-gray-900 dark:text-white">
                                        {{ $empleado && $empleado->contacto_emergencia_nombre ? "{$empleado->contacto_emergencia_nombre} ({$empleado->contacto_emergencia_telefono})" : 'No configurado' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">Cuenta Bancaria (IBAN):</span>
                                    <span class="font-mono font-bold text-gray-900 dark:text-white">
                                        {{ $empleado && $empleado->iban ? substr($empleado->iban, 0, 4) . ' •••• •••• •••• ' . substr($empleado->iban, -4) : 'Registrado' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block font-medium">Normativas y RGPD:</span>
                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                        ✓ Formalizadas y Aceptadas
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- VISTA MODO EDICIÓN --}}
                <div x-show="editMode" class="mt-6" style="display: none;">
                    @include('profile.partials.update-profile-information-form')
                </div>

            </div>

            {{-- TARJETA DE ACTUALIZACIÓN DE CONTRASEÑA --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-white/5 rounded-3xl p-6 sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if(auth()->user()->can('gestion_eliminar_usuarios') || auth()->user()->id === 1 || auth()->user()->email === 'jarodriguezbonilla@gmail.com')
            <div class="bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-white/5 rounded-3xl p-6 sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
