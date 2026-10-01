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
            <div class="bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-white/5 rounded-3xl p-6 sm:p-8" x-data="{ editMode: false, modalNormativa: false }">
                
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
                                    <div class="flex items-center justify-between gap-2 mt-0.5">
                                        @if($empleado && $empleado->politicas_aceptadas_at)
                                            <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                                ✓ Formalizadas ({{ \Carbon\Carbon::parse($empleado->politicas_aceptadas_at)->format('d/m/Y') }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 font-bold">
                                                ⚠️ Pendiente de aceptación
                                            </span>
                                        @endif
                                        <button type="button" @click="modalNormativa = true" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                            <span>Ver Normativa</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- VISTA MODO EDICIÓN --}}
                <div x-show="editMode" class="mt-6" style="display: none;">
                    @include('profile.partials.update-profile-information-form')
                </div>

                {{-- Ventana Flotante de Normativa Interna de Conducta y Discapacidad --}}
                <template x-teleport="body">
                    <div x-show="modalNormativa" class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm" style="display: none;" @click="modalNormativa = false" @keydown.escape.window="modalNormativa = false">
                        <div x-show="modalNormativa" class="w-full max-w-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-3xl shadow-2xl space-y-4 text-left" @click.stop>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-white/10">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        <span>📋</span> Normativa Interna y Código de Conducta
                                    </h3>
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400">UTRECAR, S.L. · Normativa laboral y operativa</span>
                                </div>
                                <button type="button" @click="modalNormativa = false" class="text-gray-400 hover:text-gray-500 p-1">✕</button>
                            </div>
                            <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-3 text-xs text-gray-600 dark:text-gray-300 leading-relaxed border border-gray-100 dark:border-white/5 p-4 rounded-2xl bg-gray-50/50 dark:bg-gray-950/30">
                                <p><strong>1. Obligatoriedad del Registro de Jornada:</strong> Cada empleado es responsable único e intransferible de realizar el fichaje de entrada y de salida puntual en cada turno laboral a través de los canales autorizados (Portal Web corporativo o terminales en estación).</p>
                                <p><strong>2. Credenciales y Acceso:</strong> Las credenciales y contraseñas de acceso al sistema informático son de uso estrictamente personal. Queda terminantemente prohibido ceder o compartir las claves de usuario con otros compañeros o terceras personas.</p>
                                <p><strong>3. Uso de Instalaciones y Equipos:</strong> El trabajador se compromete a hacer un uso diligente, responsable y seguro de los surtidores, terminales TPV, sistemas de cobro y demás medios proporcionados por la empresa.</p>
                                <p><strong>4. Comunicación de Incidencias y Solicitudes:</strong> Cualquier baja médica, permiso retribuido o solicitud de vacaciones deberá tramitarse con la debida antelación a través del portal de Recursos Humanos, aportando los justificantes reglamentarios.</p>
                                <p><strong>5. Atención al Cliente e Imagen Corporativa:</strong> En los puestos de cara al público, se mantendrá un trato cordial, respetuoso y profesional, portando el uniforme reglamentario en perfectas condiciones de higiene y seguridad.</p>

                                {{-- Situación declarada del empleado en su expediente --}}
                                @php
                                    $empDiscapacidad = $empleado && $empleado->tiene_discapacidad;
                                @endphp
                                <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs uppercase tracking-wider">
                                            <span>♿</span> Tu Situación Declarada de Incapacidad / Discapacidad
                                        </span>
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $empDiscapacidad ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 border border-amber-300 dark:border-amber-700' : 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-400' }}">
                                            {{ $empDiscapacidad ? 'Discapacidad Registrada' : 'Sin Discapacidad Declarada' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed">
                                        <strong>Estado en tu expediente:</strong> {{ $empDiscapacidad ? 'Constas con discapacidad o incapacidad reconocida declarada en la empresa.' : 'No constas con discapacidad o incapacidad reconocida registrada en el sistema.' }}
                                    </p>
                                </div>

                                <p><strong>6. Notificación Obligatoria de Incapacidad y Discapacidad (Régimen Disciplinario):</strong> Es de obligado e inexcusable cumplimiento por parte del empleado comunicar inmediatamente a la empresa (departamento de Recursos Humanos) cualquier resolución, variación o circunstancia relativa a su incapacidad o discapacidad.</p>
                                <div class="p-3.5 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/40 rounded-xl text-red-900 dark:text-red-200 space-y-1.5 text-[11px] leading-relaxed">
                                    <p class="font-bold">⚠️ Causa Expresa de Despido Disciplinario:</p>
                                    <p>El ocultamiento o falta de notificación fehaciente a la empresa de dicha información, <strong>tanto de forma voluntaria como involuntaria</strong>, será considerado falta laboral muy grave que <strong>podrá ser motivo de despido disciplinario</strong> y rescisión del contrato de trabajo.</p>
                                    <p class="font-semibold pt-1">Constituye causa imperativa de notificación inmediata:</p>
                                    <ul class="list-disc pl-4 space-y-1">
                                        <li><strong>La retirada o extinción de la incapacidad</strong> (o incapacidad permanente) por parte de la Administración Pública / Seguridad Social.</li>
                                        <li><strong>La bajada o reducción del grado de discapacidad a un porcentaje inferior al 33%</strong> legalmente reconocido.</li>
                                        <li>Cualquier revisión, resolución o variación médica o administrativa que altere las limitaciones funcionales o bonificaciones asociadas al puesto.</li>
                                    </ul>
                                </div>
                                <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 rounded-xl text-amber-800 dark:text-amber-300 font-medium">
                                    📜 Fin de la Normativa Interna y Código de Conducta.
                                </div>
                            </div>
                            <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-white/10">
                                <button type="button" @click="modalNormativa = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl transition-all">
                                    Entendido / Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

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
