@php
    $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower($user->email)])->first();
    $isAdmin = auth()->user()->hasRole(['Administrador', 'admin', 'Admin', 'Gestor', 'gestor', 'CEO']) 
        || auth()->user()->can('gestion_recursos_humanos')
        || auth()->user()->id === 1
        || auth()->user()->email === 'jarodriguezbonilla@gmail.com';
@endphp

<section>
    <header class="mb-6">
        <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modificar Datos del Perfil
        </h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Actualiza tu e-mail, cuenta bancaria, teléfono, domicilio y contactos de emergencia. Por normativa legal, el Nombre y el DNI solo pueden ser modificados por Recursos Humanos.
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
            {{-- Nombre y Apellidos: Editable SOLO para Admin/Gestor --}}
            <div>
                <x-input-label for="name" :value="$isAdmin ? 'Nombre y Apellidos' : 'Nombre y Apellidos (No modificable)'" />
                @if($isAdmin)
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('name', $user->name)" required autocomplete="name" />
                    <x-input-error class="mt-1" :messages="$errors->get('name')" />
                @else
                    <x-text-input id="name_disabled" type="text" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-100 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400 cursor-not-allowed select-none" :value="$user->name" readonly disabled />
                    <p class="mt-1 text-[11px] text-gray-400">Dato oficial. Para corregir tu nombre contacta con RRHH.</p>
                @endif
            </div>

            {{-- DNI / NIE: No modificable por empleado --}}
            <div>
                <x-input-label for="dni" value="DNI / NIE (No modificable)" />
                <x-text-input id="dni" type="text" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-100 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400 font-mono cursor-not-allowed select-none" :value="$empleado->dni ?? 'No registrado'" readonly disabled />
                <p class="mt-1 text-[11px] text-gray-400">Identificador legal no modificable por el empleado.</p>
            </div>

            {{-- Correo Electrónico: Editable por el empleado --}}
            <div>
                <x-input-label for="email" value="Correo Electrónico (E-mail)" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('email', $user->email)" required autocomplete="email" />
                <x-input-error class="mt-1" :messages="$errors->get('email')" />
            </div>

            {{-- Teléfono Móvil --}}
            <div>
                <x-input-label for="telefono" value="Teléfono Móvil Principal" />
                <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('telefono', $user->telefono ?: ($empleado->telefono_principal ?? ''))" autocomplete="tel" placeholder="Ej: 600123456" />
                <x-input-error class="mt-1" :messages="$errors->get('telefono')" />
            </div>

            {{-- Cuenta Bancaria (IBAN) --}}
            <div>
                <x-input-label for="iban" value="Cuenta Bancaria (IBAN para Nómina)" />
                <x-text-input id="iban" name="iban" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 font-mono uppercase" :value="old('iban', $empleado->iban ?? '')" placeholder="ES00 0000 0000 0000 0000 0000" />
                <x-input-error class="mt-1" :messages="$errors->get('iban')" />
            </div>

            {{-- Fecha de Nacimiento: Informativa / No modificable --}}
            <div>
                <x-input-label for="fecha_nacimiento" value="Fecha de Nacimiento (No modificable)" />
                <x-text-input id="fecha_nacimiento" type="text" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-100 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400 cursor-not-allowed select-none" :value="$empleado && $empleado->fecha_nacimiento ? \Carbon\Carbon::parse($empleado->fecha_nacimiento)->format('d/m/Y') : 'No registrada'" readonly disabled />
            </div>

            {{-- Dirección --}}
            <div>
                <x-input-label for="direccion" value="Dirección / Domicilio" />
                <x-text-input id="direccion" name="direccion" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('direccion', $empleado->direccion ?? '')" placeholder="Calle, número, piso" />
            </div>

            {{-- Localidad --}}
            <div>
                <x-input-label for="localidad" value="Localidad" />
                <x-text-input id="localidad" name="localidad" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('localidad', $empleado->localidad ?? '')" placeholder="Ej: Utrera" />
            </div>

            {{-- Provincia --}}
            <div>
                <x-input-label for="provincia" value="Provincia" />
                <x-text-input id="provincia" name="provincia" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('provincia', $empleado->provincia ?? '')" placeholder="Ej: Sevilla" />
            </div>

            {{-- Código Postal --}}
            <div>
                <x-input-label for="codigo_postal" value="Código Postal" />
                <x-text-input id="codigo_postal" name="codigo_postal" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('codigo_postal', $empleado->codigo_postal ?? '')" placeholder="Ej: 41710" />
            </div>

            {{-- Contacto de Emergencia --}}
            <div>
                <x-input-label for="contacto_emergencia_nombre" value="Contacto de Emergencia (Nombre)" />
                <x-text-input id="contacto_emergencia_nombre" name="contacto_emergencia_nombre" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('contacto_emergencia_nombre', $empleado->contacto_emergencia_nombre ?? '')" placeholder="Familiar o persona de contacto" />
            </div>

            <div>
                <x-input-label for="contacto_emergencia_telefono" value="Contacto de Emergencia (Teléfono)" />
                <x-text-input id="contacto_emergencia_telefono" name="contacto_emergencia_telefono" type="text" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" :value="old('contacto_emergencia_telefono', $empleado->contacto_emergencia_telefono ?? '')" placeholder="Teléfono de emergencia" />
            </div>
        </div>

        {{-- Sección Informativa y Obligatoria: Discapacidad e Incapacidad --}}
        <div class="p-4 rounded-2xl bg-amber-500/5 border border-amber-500/20 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-base">♿</span>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                        Situación de Incapacidad y Discapacidad (Normativa Interna)
                    </h4>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $empleado && $empleado->tiene_discapacidad ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-400' }}">
                    {{ $empleado && $empleado->tiene_discapacidad ? 'Discapacidad Registrada' : 'Sin Discapacidad Declarada' }}
                </span>
            </div>
            <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-900 dark:text-red-300 text-[11px] leading-relaxed space-y-1">
                <p><strong>⚠️ Cláusula Obligatoria de la Normativa Interna y Código de Conducta:</strong></p>
                <p>Es preceptivo y obligatorio por parte del empleado notificar a la empresa de forma inmediata cualquier cambio referente a su incapacidad o discapacidad, pudiendo ser <u>motivo de despido disciplinario</u> el ocultamiento tanto de forma voluntaria como involuntaria. Asimismo, es preceptiva la comunicación inmediata de la <strong>retirada de la incapacidad</strong> por parte de la Administración Pública o la <strong>bajada del grado de discapacidad a un porcentaje inferior al 33%</strong>.</p>
            </div>
            <p class="text-[11px] text-gray-500 dark:text-gray-400">
                Si tu grado de discapacidad ha variado o has recibido una resolución de la Seguridad Social, debes remitir inmediatamente la documentación oficial a Recursos Humanos (<a href="mailto:utrecar@gmail.com" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">utrecar@gmail.com</a>).
            </p>
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
