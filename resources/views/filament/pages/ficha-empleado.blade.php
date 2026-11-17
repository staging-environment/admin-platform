<x-filament-panels::page>
    @php $activeTab = $activeTab ?? 'fichajes'; @endphp
    <div class="space-y-6">
        @if (session('status') === 'password-updated' || session('success'))
            <div class="p-4 sm:p-6 bg-emerald-500/10 border-2 border-emerald-500/40 rounded-2xl text-emerald-900 dark:text-emerald-200 flex items-start gap-4 shadow-sm">
                <div class="p-2 bg-emerald-500/20 rounded-xl text-emerald-600 dark:text-emerald-400 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-base text-emerald-800 dark:text-emerald-300">
                        ¡Contraseña actualizada correctamente!
                    </h3>
                    <p class="text-sm mt-1 text-emerald-800/90 dark:text-emerald-200/90 leading-relaxed">
                        {{ session('success') ?? 'Tu contraseña ha sido cambiada con éxito. Ya puedes navegar libremente por la plataforma.' }}
                    </p>
                </div>
            </div>
        @endif

        @if(!$empleado)
            <div class="p-6 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900 rounded-2xl flex items-start gap-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-900 dark:text-red-200">Asociación de empleado no encontrada</h3>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">Tu usuario actual ({{ auth()->user()->email }}) no está asociado a ningún registro de empleado en la base de datos de Recursos Humanos. Contacta con tu administrador para que vincule tu correo electrónico.</p>
                </div>
            </div>
        @else
            @if(request()->query('empleado_id'))
                <!-- Info Banner for Admin viewing specific employee -->
                <div class="p-4 bg-indigo-50 dark:bg-indigo-950/20 border border-indigo-200 dark:border-indigo-900/40 rounded-2xl flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="p-2 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </span>
                        <div>
                            <span class="text-sm font-semibold text-indigo-900 dark:text-indigo-200">Modo de Visualización de Administrador</span>
                            <p class="text-xs text-indigo-700 dark:text-indigo-400">Estás viendo y gestionando el portal del empleado <strong>{{ $empleado->nombre }} {{ $empleado->apellidos }}</strong>.</p>
                        </div>
                    </div>
                    <a href="/admin/fichajes" style="background-color: #4f46e5; color: #ffffff;" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm transition-all hover:bg-indigo-700">
                        Volver al Listado General
                    </a>
                </div>
            @endif


            @php
                $activeBaja = collect($ausencias)->first(fn($a) => $a->tipo === 'Bajas médicas' && empty($a->fecha_fin));
            @endphp

            @if($activeBaja)
                <!-- Banner Prioritario: Empleado en situación de Baja Médica -->
                <div class="p-6 bg-gradient-to-r from-rose-500/15 via-amber-500/10 to-emerald-500/15 dark:from-rose-950/40 dark:via-amber-950/20 dark:to-emerald-950/20 border-2 border-rose-500/50 dark:border-rose-500/30 rounded-3xl shadow-md animate-fadeIn">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex items-start gap-4">
                            <div class="p-3.5 bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-2xl shrink-0 mt-0.5 animate-pulse shadow-sm">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 bg-rose-600 text-white font-black text-xs rounded-full uppercase tracking-wider shadow-sm">
                                        ● Situación Actual: Baja Médica (Incapacidad Temporal)
                                    </span>
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 bg-white/70 dark:bg-white/10 px-2.5 py-0.5 rounded-full">
                                        Fecha de Inicio: {{ \Carbon\Carbon::parse($activeBaja->fecha_inicio)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white">
                                    Actualmente te encuentras en situación de baja laboral
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed max-w-3xl">
                                    Tus fichajes ordinarios de entrada y salida permanecen pausados mientras continúe este periodo. En cuanto te reincorpores al trabajo, debes pulsar en el botón de la derecha para registrar tu fecha de finalización de baja y adjuntar el parte médico oficial de alta.
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center">
                            <button type="button" wire:click="abrirRegistrarAlta({{ $activeBaja->id }})" style="background-color: #10b981; color: #ffffff;" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 hover:bg-emerald-700 text-white font-black text-sm rounded-2xl shadow-lg shadow-emerald-600/30 hover:shadow-xl transition-all cursor-pointer">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Registrar Alta Médica (Reincorporarme)</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if($fichajePendienteAnterior)
                <!-- Alert Banner: Shift from previous day is unclosed -->
                <div class="p-6 bg-gradient-to-r from-red-500/10 via-amber-500/10 to-orange-500/10 dark:from-red-950/30 dark:via-amber-950/20 dark:to-orange-950/20 border-2 border-red-500/40 dark:border-red-500/30 rounded-3xl shadow-sm">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-red-500/20 text-red-600 dark:text-red-400 rounded-2xl shrink-0 mt-0.5 animate-pulse">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 font-bold text-xs rounded-full uppercase tracking-wider">
                                        Atención: Turno Pendiente de Cierre
                                    </span>
                                </div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white">
                                    Tienes un turno anterior sin cerrar ({{ \Carbon\Carbon::parse($fichajePendienteAnterior->fecha)->translatedFormat('l, d \d\e F \d\e Y') }})
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Registraste entrada a las <strong class="font-bold text-gray-900 dark:text-white font-mono">{{ \Carbon\Carbon::parse($fichajePendienteAnterior->hora_entrada)->format('H:i') }}</strong> pero no se registró la salida. 
                                    <span class="text-red-600 dark:text-red-400 font-bold">Debes cerrar el turno anterior indicando la hora de salida para poder realizar una nueva entrada hoy.</span>
                                </p>
                            </div>
                        </div>

                        <!-- Action form to close pending shift -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-red-200/80 dark:border-red-900/50 shadow-sm shrink-0" x-data="{
                            loadingCierre: false,
                            doCerrarTurno() {
                                this.loadingCierre = true;
                                getFichajeGeolocation((lat, lng) => {
                                    $wire.cerrarTurnoAnterior(lat, lng)
                                        .then(() => { this.loadingCierre = false; })
                                        .catch(() => { this.loadingCierre = false; });
                                });
                            }
                        }">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Hora de Salida ({{ \Carbon\Carbon::parse($fichajePendienteAnterior->fecha)->format('d/m/Y') }})</label>
                                <input type="time" wire:model="hora_salida_pendiente" class="w-full sm:w-36 rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-base font-bold focus:border-red-500 focus:ring-red-500 shadow-sm" />
                            </div>
                            <button @click="doCerrarTurno" x-bind:disabled="loadingCierre" style="background-color: #dc2626; color: #ffffff;" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-600/20 hover:shadow-red-700/30 transition-all flex items-center justify-center gap-2 text-sm disabled:opacity-50 whitespace-nowrap">
                                <span x-show="loadingCierre" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full" style="display: none;"></span>
                                <span x-show="!loadingCierre">Cerrar Turno Anterior</span>
                                <span x-show="loadingCierre" style="display: none;">Guardando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif





            @if($activeTab === 'fichajes')
                <div class="space-y-6">

                    {{-- 1. Alertas de Cambios de Turno Recibidos (Pendientes de Aceptar / Denegar por este empleado) --}}
                    @foreach($this->solicitudesCambioPendientesRecibidas as $solRecibida)
                        <div class="p-5 bg-gradient-to-r from-amber-500/15 via-indigo-500/10 to-blue-500/15 dark:from-amber-950/40 dark:via-indigo-950/20 dark:to-blue-950/20 border-2 border-amber-500/60 dark:border-amber-500/40 rounded-3xl shadow-md animate-fadeIn">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="flex items-start gap-3.5">
                                    <div class="p-3 bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-2xl shrink-0 mt-0.5 shadow-xs">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 bg-amber-600 text-white font-black text-xs rounded-full uppercase tracking-wider shadow-xs">
                                                @if($solRecibida->tipo_cambio === 'devolucion_debito')
                                                    ● Oferta de Devolución de Turno
                                                @elseif($solRecibida->tipo_cambio === 'cobro_credito')
                                                    ● Solicitud de Cobro de Turno a Crédito
                                                @elseif(empty($solRecibida->fecha_devolucion))
                                                    ● Solicitud de Cobertura (Turno a Débito/Crédito)
                                                @else
                                                    ● Solicitud de Cambio de Turno Recibida
                                                @endif
                                            </span>
                                        </div>
                                        <h3 class="text-base font-black text-gray-900 dark:text-white">
                                            @if($solRecibida->tipo_cambio === 'devolucion_debito')
                                                {{ $solRecibida->empleado->nombre }} {{ $solRecibida->empleado->apellidos }} te ofrece cubrir tu turno para saldar 1 turno que te debe
                                            @elseif($solRecibida->tipo_cambio === 'cobro_credito')
                                                {{ $solRecibida->empleado->nombre }} {{ $solRecibida->empleado->apellidos }} te solicita cubrir su turno para saldar 1 turno que le debes
                                            @elseif(empty($solRecibida->fecha_devolucion))
                                                {{ $solRecibida->empleado->nombre }} {{ $solRecibida->empleado->apellidos }} te pide cubrir su turno (sin fecha de devolución)
                                            @else
                                                {{ $solRecibida->empleado->nombre }} {{ $solRecibida->empleado->apellidos }} te ha solicitado un cambio de turno
                                            @endif
                                        </h3>
                                        <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                            <p>
                                                🗓️ <strong>Día a cubrir:</strong> <span class="text-indigo-600 dark:text-indigo-400 font-black">{{ \Carbon\Carbon::parse($solRecibida->fecha_inicio)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                                            </p>
                                            @if($solRecibida->fecha_devolucion)
                                                <p>
                                                    🔄 <strong>Día de devolución:</strong> <span class="text-emerald-600 dark:text-emerald-400 font-black">{{ \Carbon\Carbon::parse($solRecibida->fecha_devolucion)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                                                </p>
                                            @elseif($solRecibida->tipo_cambio === 'cobro_credito')
                                                <p class="text-amber-700 dark:text-amber-400 font-semibold">
                                                    ℹ️ Si aceptas, saldarás 1 de los turnos que le debes a tu compañero.
                                                </p>
                                            @elseif($solRecibida->tipo_cambio === 'devolucion_debito')
                                                <p class="text-emerald-700 dark:text-emerald-400 font-semibold">
                                                    ℹ️ Si aceptas, tu compañero te cubrirá este día y saldará 1 turno que te debe.
                                                </p>
                                            @else
                                                <p class="text-sky-700 dark:text-sky-400 font-semibold">
                                                    ℹ️ Al no tener fecha de devolución, se te sumará 1 turno a crédito y a tu compañero 1 turno a débito al aceptar.
                                                </p>
                                            @endif
                                            @if($solRecibida->comentario_empleado)
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 italic">
                                                    💬 "{{ $solRecibida->comentario_empleado }}"
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2.5 shrink-0 self-end lg:self-center">
                                    <button
                                        type="button"
                                        wire:click="responderCambioTurno({{ $solRecibida->id }}, true)"
                                        style="background-color: #10b981; color: #ffffff;"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md transition-all cursor-pointer"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Aceptar Cambio</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="responderCambioTurno({{ $solRecibida->id }}, false)"
                                        style="background-color: #ef4444; color: #ffffff;"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl shadow-md transition-all cursor-pointer"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span>Denegar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- 2. Alertas para el Empleado Solicitante (con la respuesta de su compañero) --}}
                    @foreach($this->alertasCambioTurnoParaSolicitante as $alertaSol)
                        @php
                            $esAceptada = in_array($alertaSol->estado, ['Aceptada', 'Aprobada']);
                            $nombreResp = $alertaSol->empleadoSustituto ? ($alertaSol->empleadoSustituto->nombre . ' ' . $alertaSol->empleadoSustituto->apellidos) : 'Tu compañero';
                        @endphp
                        <div class="p-5 {{ $esAceptada ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-950 dark:text-emerald-100' : 'bg-rose-500/10 border-rose-500/40 text-rose-950 dark:text-rose-100' }} border-2 rounded-3xl shadow-sm animate-fadeIn">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="p-2.5 {{ $esAceptada ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/20 text-rose-600 dark:text-rose-400' }} rounded-xl shrink-0 mt-0.5">
                                        @if($esAceptada)
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        @endif
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="font-black text-sm {{ $esAceptada ? 'text-emerald-800 dark:text-emerald-300' : 'text-rose-800 dark:text-rose-300' }}">
                                            {{ $esAceptada ? '¡Solicitud Aceptada!' : 'Solicitud Denegada' }}
                                        </h4>
                                        <p class="text-xs leading-relaxed">
                                            @if($esAceptada)
                                                @if($alertaSol->tipo_cambio === 'cobro_credito')
                                                    <strong>{{ $nombreResp }}</strong> ha <strong>ACEPTADO</strong> cubrir tu turno el <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_inicio)->format('d/m/Y') }}</strong> para saldar el turno que te debía. Tu crédito ha quedado saldado y el Planing de turnos se ha actualizado.
                                                @elseif($alertaSol->tipo_cambio === 'devolucion_debito')
                                                    <strong>{{ $nombreResp }}</strong> ha <strong>ACEPTADO</strong> tu oferta de cubrir su turno el <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_inicio)->format('d/m/Y') }}</strong>. Tu turno a débito ha quedado saldado y el Planing de turnos se ha actualizado.
                                                @elseif(empty($alertaSol->fecha_devolucion))
                                                    <strong>{{ $nombreResp }}</strong> ha <strong>ACEPTADO</strong> cubrir tu turno el <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_inicio)->format('d/m/Y') }}</strong>. Se ha añadido 1 turno a débito con este compañero y el Planing de turnos se ha actualizado.
                                                @else
                                                    <strong>{{ $nombreResp }}</strong> ha <strong>ACEPTADO</strong> el cambio de turno. Cubrirá tu turno el <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_inicio)->format('d/m/Y') }}</strong> y tú le devolverás el turno el <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_devolucion)->format('d/m/Y') }}</strong>. El Planing de turnos ha sido actualizado.
                                                @endif
                                            @else
                                                <strong>{{ $nombreResp }}</strong> ha <strong>DENEGADO</strong> tu solicitud para el día <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_inicio)->format('d/m/Y') }}</strong>.
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    wire:click="marcarAlertaCambioVista({{ $alertaSol->id }})"
                                    class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl text-xs font-bold shadow-xs transition-all cursor-pointer whitespace-nowrap {{ $esAceptada ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-rose-600 hover:bg-rose-700 text-white' }}"
                                >
                                    <span>Entendido / Cerrar</span>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Check-in Card -->
                <div class="p-4 sm:p-5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="p-2 bg-green-500/10 text-green-600 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                </span>
                                Entrada / Check-In
                            </h3>
                            <span class="text-xs text-gray-400">Registro de entrada</span>
                        </div>

                        <div class="py-3 flex flex-col items-center justify-center min-h-[90px]">
                            @if($activeBaja)
                                <div class="text-center space-y-3 max-w-sm text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/20 p-5 rounded-2xl border border-rose-200 dark:border-rose-900/40">
                                    <svg class="w-8 h-8 mx-auto stroke-current text-rose-500" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <div>
                                        <h4 class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">Fichajes Inactivos por Baja Médica</h4>
                                        <p class="text-[11px] text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">No puedes registrar entradas mientras dure tu incapacidad temporal. Registra tu alta médica para reactivarlos.</p>
                                    </div>
                                    <button type="button" wire:click="abrirRegistrarAlta({{ $activeBaja->id }})" style="background-color: #10b981; color: #ffffff;" class="inline-flex items-center gap-1.5 px-4 py-2 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Registrar Alta Médica
                                    </button>
                                </div>
                            @elseif($fichajePendienteAnterior)
                                <div class="text-center space-y-3 max-w-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/20 p-5 rounded-2xl border border-amber-200 dark:border-amber-900/40">
                                    <svg class="w-10 h-10 mx-auto stroke-current text-amber-500" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <div>
                                        <h4 class="font-bold text-sm text-gray-900 dark:text-white">Entrada Bloqueada</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Debes cerrar primero el turno anterior del <strong>{{ \Carbon\Carbon::parse($fichajePendienteAnterior->fecha)->format('d/m/Y') }}</strong> para poder iniciar tu jornada de hoy.</p>
                                    </div>
                                </div>
                            @elseif($fichajeActivo && $fichajeActivo->hora_entrada)
                                <div class="text-center space-y-2">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-300 font-bold rounded-full text-sm border border-green-200 dark:border-green-900">
                                        <span class="h-2.5 w-2.5 rounded-full bg-green-500 animate-pulse"></span>
                                        Jornada en Curso
                                    </div>
                                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($fichajeActivo->hora_entrada)->format('H:i') }}</h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Hora real de registro: {{ $fichajeActivo->server_checkin_at ? $fichajeActivo->server_checkin_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') : \Carbon\Carbon::parse($fichajeActivo->fecha . ' ' . $fichajeActivo->hora_entrada)->format('d/m/Y H:i:s') }}</p>
                                    <div>
                                        <button type="button" wire:click="editFichaje({{ $fichajeActivo->id }})" class="text-xs font-semibold text-amber-600 dark:text-amber-400 hover:underline inline-flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            Modificar hora de entrada
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="w-full max-w-xs space-y-3 text-center">
                                    @if($fichajesDelDia && $fichajesDelDia->count() > 0)
                                        <div>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 font-bold rounded-full text-xs border border-blue-200 dark:border-blue-900">
                                                Turno {{ $fichajesDelDia->count() + 1 }} de hoy
                                            </span>
                                        </div>
                                    @endif
                                    <button type="button" 
                                            wire:click="abrirModalCheckIn" 
                                            style="background-color: #16a34a; color: #ffffff;" 
                                            class="w-full py-3.5 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-600/10 hover:shadow-green-700/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>{{ ($fichajesDelDia && $fichajesDelDia->count() > 0) ? 'Registrar Nueva Entrada' : 'Registrar Entrada' }}</span>
                                    </button>
                                    <p class="text-[11px] text-gray-400">Pulsa para seleccionar tu hora de entrada en la ventana emergente.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                        <!-- Check-out Card -->
                <div class="p-4 sm:p-5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="p-2 bg-orange-500/10 text-orange-600 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </span>
                                Salida / Check-Out
                            </h3>
                            <span class="text-xs text-gray-400">Registro de salida</span>
                        </div>

                        <div class="py-3 flex flex-col items-center justify-center min-h-[90px]">
                            @if($activeBaja)
                                <div class="text-center space-y-2 max-w-xs text-gray-400 py-3">
                                    <svg class="w-8 h-8 mx-auto stroke-current text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <p class="text-xs font-medium">No hay jornada activa que cerrar durante la baja médica.</p>
                                </div>
                            @elseif($fichajePendienteAnterior)
                                <div class="w-full max-w-xs space-y-4" x-data="{
                                    loadingCierre: false,
                                    doCerrarTurno() {
                                        this.loadingCierre = true;
                                        getFichajeGeolocation((lat, lng) => {
                                            $wire.cerrarTurnoAnterior(lat, lng)
                                                .then(() => { this.loadingCierre = false; })
                                                .catch(() => { this.loadingCierre = false; });
                                        });
                                    }
                                }">
                                    <div class="text-center pb-1">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300 font-bold rounded-full text-xs border border-red-200 dark:border-red-900">
                                            Turno del {{ \Carbon\Carbon::parse($fichajePendienteAnterior->fecha)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Hora Salida Turno Anterior</label>
                                        <input type="time" wire:model="hora_salida_pendiente" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-red-500 focus:ring-red-500 shadow-sm" />
                                    </div>
                                    <button @click="doCerrarTurno" x-bind:disabled="loadingCierre" style="background-color: #dc2626; color: #ffffff;" class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-600/10 hover:shadow-red-700/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                                        <span x-show="loadingCierre" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full" style="display: none;"></span>
                                        <span x-show="!loadingCierre">Cerrar Turno Anterior</span>
                                        <span x-show="loadingCierre" style="display: none;">Obteniendo ubicación...</span>
                                    </button>
                                </div>
                            @elseif(!$fichajeActivo)
                                @if($ultimoFichajeCerradoDelDia)
                                    <div class="text-center space-y-2">
                                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300 font-bold rounded-full text-sm border border-amber-200 dark:border-amber-900">
                                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                                            Última Salida: {{ \Carbon\Carbon::parse($ultimoFichajeCerradoDelDia->hora_salida)->format('H:i') }}
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Hora real de registro: {{ $ultimoFichajeCerradoDelDia->server_checkout_at ? $ultimoFichajeCerradoDelDia->server_checkout_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') : \Carbon\Carbon::parse($ultimoFichajeCerradoDelDia->fecha . ' ' . $ultimoFichajeCerradoDelDia->hora_salida)->format('d/m/Y H:i:s') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Jornada previa completada. Si inicias otro turno hoy, registra tu entrada primero.</p>
                                    </div>
                                @else
                                    <div class="text-center space-y-2 max-w-xs text-gray-400">
                                        <svg class="w-12 h-12 mx-auto stroke-current" fill="none" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        <p class="text-sm font-medium">Debes registrar tu entrada primero para poder registrar la salida.</p>
                                    </div>
                                @endif
                            @else
                                <div class="w-full max-w-xs space-y-3 text-center">
                                    <button type="button" 
                                            wire:click="abrirModalCheckOut" 
                                            style="background-color: #ea580c; color: #ffffff;" 
                                            class="w-full py-3.5 px-4 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg shadow-orange-600/10 hover:shadow-orange-700/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Registrar Salida</span>
                                    </button>
                                    <p class="text-[11px] text-gray-400">Pulsa para seleccionar tu hora de salida en la ventana emergente.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        <!-- Fichajes History -->
            <div class="p-4 sm:p-5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-2 bg-amber-500/10 text-amber-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        Historial de Fichajes (Últimos 30 días)
                    </h3>
                    <button type="button" wire:click="abrirFichajeRetroactivoNuevaFecha" style="background-color: #d97706; color: #ffffff;" class="inline-flex items-center gap-1.5 px-3 py-1.5 hover:bg-amber-750 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Fichaje Retroactivo
                    </button>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-white/5">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/5">
                                <th class="py-2 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                                <th class="py-2 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Hora Entrada</th>
                                <th class="py-2 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Hora Salida</th>
                                <th class="py-2 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Horas Trabajadas</th>
                                <th class="py-2 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @forelse($recentFichajes as $fichaje)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                    <td class="py-2 px-4 text-sm font-bold text-gray-900 dark:text-white">
                                        <div class="flex flex-col">
                                            <span>{{ \Carbon\Carbon::parse($fichaje->fecha)->format('d/m/Y') }}</span>
                                            <div class="flex flex-wrap gap-1 mt-1 font-normal text-[10px]">
                                                @if($fichaje->is_retroactive)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                        Retroactivo
                                                    </span>
                                                @endif
                                                @if($fichaje->is_edited)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded font-semibold bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400" title="Modificado por {{ $fichaje->edited_by_email }}. Entrada original: {{ $fichaje->original_hora_entrada ? \Carbon\Carbon::parse($fichaje->original_hora_entrada)->format('H:i') : '-' }}, Salida original: {{ $fichaje->original_hora_salida ? \Carbon\Carbon::parse($fichaje->original_hora_salida)->format('H:i') : '-' }}">
                                                        Modificado
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4 text-sm text-gray-700 dark:text-gray-300">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900 dark:text-white text-base">
                                                    {{ $fichaje->hora_entrada ? \Carbon\Carbon::parse($fichaje->hora_entrada)->format('H:i') : '-' }}
                                                </span>
                                                @if($isAdmin && $fichaje->checkin_latitude && $fichaje->checkin_longitude)
                                                    <a href="https://www.google.com/maps?q={{ $fichaje->checkin_latitude }},{{ $fichaje->checkin_longitude }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 font-semibold text-[11px] transition-colors" title="Ver ubicación en Google Maps (Lat: {{ $fichaje->checkin_latitude }}, Lng: {{ $fichaje->checkin_longitude }})">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                        Mapa
                                                    </a>
                                                @endif
                                            </div>
                                            @if($isAdmin && $fichaje->server_checkin_at)
                                                <span class="inline-flex items-center gap-1 text-[10.5px] bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded font-mono w-fit whitespace-nowrap" title="Hora real registrada por el sistema: {{ $fichaje->server_checkin_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}">
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Real: {{ $fichaje->server_checkin_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2 px-4 text-sm text-gray-700 dark:text-gray-300">
                                        @if($fichaje->hora_salida)
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-gray-900 dark:text-white text-base">
                                                        {{ \Carbon\Carbon::parse($fichaje->hora_salida)->format('H:i') }}
                                                    </span>
                                                    @if($isAdmin && $fichaje->checkout_latitude && $fichaje->checkout_longitude)
                                                        <a href="https://www.google.com/maps?q={{ $fichaje->checkout_latitude }},{{ $fichaje->checkout_longitude }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 font-semibold text-[11px] transition-colors" title="Ver ubicación en Google Maps (Lat: {{ $fichaje->checkout_latitude }}, Lng: {{ $fichaje->checkout_longitude }})">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                            Mapa
                                                        </a>
                                                    @endif
                                                </div>
                                                @if($isAdmin && $fichaje->server_checkout_at)
                                                    <span class="inline-flex items-center gap-1 text-[10.5px] bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded font-mono w-fit whitespace-nowrap" title="Hora real registrada por el sistema: {{ $fichaje->server_checkout_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}">
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Real: {{ $fichaje->server_checkout_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 font-bold text-xs">
                                                En curso
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4 text-sm font-semibold text-amber-600 dark:text-amber-400">
                                        {{ $fichaje->horas_trabajadas_formateadas }}
                                    </td>
                                    <td class="py-2 px-4 text-sm text-right space-x-2">
                                        <button wire:click="editFichaje({{ $fichaje->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            Editar
                                        </button>
                                         <button type="button" @click="$wire.set('deletingFichajeId', {{ $fichaje->id }}); $dispatch('open-modal', { id: 'delete-fichaje-modal' })" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                                             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                             </svg>
                                             Eliminar
                                         </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Aún no has registrado ningún fichaje en los últimos 30 días.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
                </div>
            @endif

                        @if($activeTab === 'vacaciones')
                <div class="space-y-6">

                    {{-- Alertas de solicitudes de cambio recibidas y respuestas para el solicitante --}}
                    @if($this->solicitudesCambioPendientesRecibidas->isNotEmpty())
                        @foreach($this->solicitudesCambioPendientesRecibidas as $solRecibida)
                            <div class="p-5 bg-gradient-to-r from-amber-500/15 via-indigo-500/10 to-blue-500/15 dark:from-amber-950/40 dark:via-indigo-950/20 dark:to-blue-950/20 border-2 border-amber-500/60 dark:border-amber-500/40 rounded-3xl shadow-md animate-fadeIn">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                    <div class="flex items-start gap-3.5">
                                        <div class="p-3 bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-2xl shrink-0 mt-0.5 shadow-xs">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2.5 py-0.5 bg-amber-600 text-white font-black text-xs rounded-full uppercase tracking-wider shadow-xs">
                                                    @if($solRecibida->tipo_cambio === 'devolucion_debito')
                                                        ● Oferta de Devolución de Turno
                                                    @elseif($solRecibida->tipo_cambio === 'cobro_credito')
                                                        ● Solicitud de Cobro de Turno a Crédito
                                                    @elseif(empty($solRecibida->fecha_devolucion))
                                                        ● Solicitud de Cobertura de Turno (A Débito/Crédito)
                                                    @else
                                                        ● Solicitud de Cambio de Turno Recibida
                                                    @endif
                                                </span>
                                            </div>
                                            <h3 class="text-base font-black text-gray-900 dark:text-white">
                                                @if($solRecibida->tipo_cambio === 'devolucion_debito')
                                                    {{ $solRecibida->empleado->nombre }} {{ $solRecibida->empleado->apellidos }} te ofrece cubrir tu turno para saldar 1 turno que te debe
                                                @elseif($solRecibida->tipo_cambio === 'cobro_credito')
                                                    {{ $solRecibida->empleado->nombre }} {{ $solRecibida->empleado->apellidos }} te solicita cubrir su turno para saldar 1 turno que le debes
                                                @elseif(empty($solRecibida->fecha_devolucion))
                                                    {{ $solRecibida->empleado->nombre }} {{ $solRecibida->empleado->apellidos }} te pide cubrir su turno (sin fecha de devolución)
                                                @else
                                                    {{ $solRecibida->empleado->nombre }} {{ $solRecibida->empleado->apellidos }} te ha solicitado un cambio de turno
                                                @endif
                                            </h3>
                                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                                <p>
                                                    🗓️ <strong>Día a cubrir:</strong> <span class="text-indigo-600 dark:text-indigo-400 font-black">{{ \Carbon\Carbon::parse($solRecibida->fecha_inicio)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                                                </p>
                                                @if($solRecibida->fecha_devolucion)
                                                    <p>
                                                        🔄 <strong>Día de devolución:</strong> <span class="text-emerald-600 dark:text-emerald-400 font-black">{{ \Carbon\Carbon::parse($solRecibida->fecha_devolucion)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                                                    </p>
                                                @elseif($solRecibida->tipo_cambio === 'cobro_credito')
                                                    <p class="text-amber-700 dark:text-amber-400 font-semibold">
                                                        ℹ️ Si aceptas, saldarás 1 de los turnos que le debes a tu compañero.
                                                    </p>
                                                @elseif($solRecibida->tipo_cambio === 'devolucion_debito')
                                                    <p class="text-emerald-700 dark:text-emerald-400 font-semibold">
                                                        ℹ️ Si aceptas, tu compañero saldará 1 turno que te debe (te cubrirá este día).
                                                    </p>
                                                @else
                                                    <p class="text-sky-700 dark:text-sky-400 font-semibold">
                                                        ℹ️ Al no tener fecha de devolución, se te anotará 1 turno a crédito y a tu compañero 1 turno a débito al aceptar.
                                                    </p>
                                                @endif
                                                @if($solRecibida->comentario_empleado)
                                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 italic">
                                                        💬 "{{ $solRecibida->comentario_empleado }}"
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2.5 shrink-0 self-end lg:self-center">
                                        <button
                                            type="button"
                                            wire:click="responderCambioTurno({{ $solRecibida->id }}, true)"
                                            style="background-color: #10b981; color: #ffffff;"
                                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md transition-all cursor-pointer"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Aceptar</span>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="responderCambioTurno({{ $solRecibida->id }}, false)"
                                            style="background-color: #ef4444; color: #ffffff;"
                                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl shadow-md transition-all cursor-pointer"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>Denegar</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($this->alertasCambioTurnoParaSolicitante->isNotEmpty())
                        @foreach($this->alertasCambioTurnoParaSolicitante as $alertaSol)
                            @php
                                $esAceptada = in_array($alertaSol->estado, ['Aceptada', 'Aprobada']);
                                $nombreResp = $alertaSol->empleadoSustituto ? ($alertaSol->empleadoSustituto->nombre . ' ' . $alertaSol->empleadoSustituto->apellidos) : 'Tu compañero';
                            @endphp
                            <div class="p-5 {{ $esAceptada ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-950 dark:text-emerald-100' : 'bg-rose-500/10 border-rose-500/40 text-rose-950 dark:text-rose-100' }} border-2 rounded-3xl shadow-sm animate-fadeIn">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2.5 {{ $esAceptada ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/20 text-rose-600 dark:text-rose-400' }} rounded-xl shrink-0 mt-0.5">
                                            @if($esAceptada)
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <h4 class="font-black text-sm {{ $esAceptada ? 'text-emerald-800 dark:text-emerald-300' : 'text-rose-800 dark:text-rose-300' }}">
                                                {{ $esAceptada ? '¡Solicitud Aceptada!' : 'Solicitud Denegada' }}
                                            </h4>
                                            <p class="text-xs leading-relaxed">
                                                @if($esAceptada)
                                                    @if($alertaSol->tipo_cambio === 'cobro_credito')
                                                        <strong>{{ $nombreResp }}</strong> ha <strong>ACEPTADO</strong> cubrir tu turno el <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_inicio)->format('d/m/Y') }}</strong> para saldar el turno que te debía. Tu crédito ha quedado saldado y el Planing de turnos se ha actualizado.
                                                    @elseif($alertaSol->tipo_cambio === 'devolucion_debito')
                                                        <strong>{{ $nombreResp }}</strong> ha <strong>ACEPTADO</strong> tu oferta de cubrir su turno el <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_inicio)->format('d/m/Y') }}</strong>. Tu turno a débito ha quedado saldado y el Planing de turnos se ha actualizado.
                                                    @elseif(empty($alertaSol->fecha_devolucion))
                                                        <strong>{{ $nombreResp }}</strong> ha <strong>ACEPTADO</strong> cubrir tu turno el <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_inicio)->format('d/m/Y') }}</strong>. Se ha añadido 1 turno a débito con este compañero y el Planing de turnos se ha actualizado.
                                                    @else
                                                        <strong>{{ $nombreResp }}</strong> ha <strong>ACEPTADO</strong> el cambio de turno. Cubrirá tu turno el <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_inicio)->format('d/m/Y') }}</strong> y tú le devolverás el turno el <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_devolucion)->format('d/m/Y') }}</strong>. El Planing de turnos ha sido actualizado.
                                                    @endif
                                                @else
                                                    <strong>{{ $nombreResp }}</strong> ha <strong>DENEGADO</strong> tu solicitud para el día <strong>{{ \Carbon\Carbon::parse($alertaSol->fecha_inicio)->format('d/m/Y') }}</strong>.
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        wire:click="marcarAlertaCambioVista({{ $alertaSol->id }})"
                                        class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl text-xs font-bold shadow-xs transition-all cursor-pointer whitespace-nowrap {{ $esAceptada ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-rose-600 hover:bg-rose-700 text-white' }}"
                                    >
                                        <span>Entendido / Cerrar</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    {{-- 5 Bloques Clicables de Solicitud y Gestión --}}
                    @if(auth()->user()->can('solicitar_ver_vacaciones'))
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                            {{-- Bloque 1: Vacaciones --}}
                            <button
                                type="button"
                                wire:click="seleccionarBloquePermiso('vacaciones')"
                                class="relative flex flex-col p-4 rounded-3xl text-left border-2 transition-all cursor-pointer shadow-sm
                                    {{ $bloquePermisosActivo === 'vacaciones'
                                        ? 'bg-sky-50 dark:bg-sky-950/40 border-sky-500 shadow-sky-500/10 ring-2 ring-sky-500/30'
                                        : 'bg-white dark:bg-gray-900 border-gray-100 dark:border-white/5 hover:border-sky-300 dark:hover:border-sky-800' }}"
                            >
                                <div class="flex items-center justify-between w-full mb-2.5">
                                    <div class="p-2.5 rounded-2xl {{ $bloquePermisosActivo === 'vacaciones' ? 'bg-sky-500 text-white' : 'bg-sky-500/10 text-sky-600 dark:text-sky-400' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    @if($bloquePermisosActivo === 'vacaciones')
                                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500 ring-4 ring-sky-500/20"></span>
                                    @endif
                                </div>
                                <h4 class="text-sm font-black text-gray-900 dark:text-white">Vacaciones</h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Por quincenas</p>
                            </button>

                            {{-- Bloque 2: Permiso Retribuido --}}
                            <button
                                type="button"
                                wire:click="seleccionarBloquePermiso('permisos')"
                                class="relative flex flex-col p-4 rounded-3xl text-left border-2 transition-all cursor-pointer shadow-sm
                                    {{ $bloquePermisosActivo === 'permisos'
                                        ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 shadow-emerald-500/10 ring-2 ring-emerald-500/30'
                                        : 'bg-white dark:bg-gray-900 border-gray-100 dark:border-white/5 hover:border-emerald-300 dark:hover:border-emerald-800' }}"
                            >
                                <div class="flex items-center justify-between w-full mb-2.5">
                                    <div class="p-2.5 rounded-2xl {{ $bloquePermisosActivo === 'permisos' ? 'bg-emerald-500 text-white' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    @if($bloquePermisosActivo === 'permisos')
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20"></span>
                                    @endif
                                </div>
                                <h4 class="text-sm font-black text-gray-900 dark:text-white">Permiso Retribuido</h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Con justificante</p>
                            </button>

                            {{-- Bloque 3: Cambio de Turno --}}
                            <button
                                type="button"
                                wire:click="seleccionarBloquePermiso('cambio_turno')"
                                class="relative flex flex-col p-4 rounded-3xl text-left border-2 transition-all cursor-pointer shadow-sm
                                    {{ $bloquePermisosActivo === 'cambio_turno'
                                        ? 'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-500 shadow-indigo-500/10 ring-2 ring-indigo-500/30'
                                        : 'bg-white dark:bg-gray-900 border-gray-100 dark:border-white/5 hover:border-indigo-300 dark:hover:border-indigo-800' }}"
                            >
                                <div class="flex items-center justify-between w-full mb-2.5">
                                    <div class="p-2.5 rounded-2xl {{ $bloquePermisosActivo === 'cambio_turno' ? 'bg-indigo-500 text-white' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </div>
                                    @if($bloquePermisosActivo === 'cambio_turno')
                                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 ring-4 ring-indigo-500/20"></span>
                                    @endif
                                </div>
                                <h4 class="text-sm font-black text-gray-900 dark:text-white">Cambio de Turno</h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Con compañero</p>
                            </button>

                            {{-- Bloque 4: Turnos a Débito --}}
                            <button
                                type="button"
                                wire:click="seleccionarBloquePermiso('debito')"
                                class="relative flex flex-col p-4 rounded-3xl text-left border-2 transition-all cursor-pointer shadow-sm
                                    {{ $bloquePermisosActivo === 'debito'
                                        ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-500 shadow-amber-500/10 ring-2 ring-amber-500/30'
                                        : 'bg-white dark:bg-gray-900 border-gray-100 dark:border-white/5 hover:border-amber-300 dark:hover:border-amber-800' }}"
                            >
                                <div class="flex items-center justify-between w-full mb-2.5">
                                    <div class="p-2.5 rounded-2xl {{ $bloquePermisosActivo === 'debito' ? 'bg-amber-500 text-white' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </div>
                                    <span class="px-2 py-0.5 text-xs font-black rounded-full {{ $this->totalTurnosDebito > 0 ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}">
                                        {{ $this->totalTurnosDebito }}
                                    </span>
                                </div>
                                <h4 class="text-sm font-black text-gray-900 dark:text-white">Turnos a Débito</h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Debo a compañeros</p>
                            </button>

                            {{-- Bloque 5: Turnos a Crédito --}}
                            <button
                                type="button"
                                wire:click="seleccionarBloquePermiso('credito')"
                                class="relative flex flex-col p-4 rounded-3xl text-left border-2 transition-all cursor-pointer shadow-sm
                                    {{ $bloquePermisosActivo === 'credito'
                                        ? 'bg-cyan-50 dark:bg-cyan-950/40 border-cyan-500 shadow-cyan-500/10 ring-2 ring-cyan-500/30'
                                        : 'bg-white dark:bg-gray-900 border-gray-100 dark:border-white/5 hover:border-cyan-300 dark:hover:border-cyan-800' }}"
                            >
                                <div class="flex items-center justify-between w-full mb-2.5">
                                    <div class="p-2.5 rounded-2xl {{ $bloquePermisosActivo === 'credito' ? 'bg-cyan-500 text-white' : 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                        </svg>
                                    </div>
                                    <span class="px-2 py-0.5 text-xs font-black rounded-full {{ $this->totalTurnosCredito > 0 ? 'bg-cyan-100 dark:bg-cyan-900/60 text-cyan-800 dark:text-cyan-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}">
                                        {{ $this->totalTurnosCredito }}
                                    </span>
                                </div>
                                <h4 class="text-sm font-black text-gray-900 dark:text-white">Turnos a Crédito</h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Me deben a favor</p>
                            </button>
                        </div>

                        {{-- Panel Dinámico según el Bloque Seleccionado --}}
                        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm transition-all">

                            {{-- 1. Formulario de Vacaciones --}}
                            @if($bloquePermisosActivo === 'vacaciones')
                                <div>
                                    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-gray-100 dark:border-white/5">
                                        <div class="p-2 bg-sky-500/10 text-sky-600 rounded-xl">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-black text-gray-900 dark:text-white">Solicitud de Vacaciones</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Selecciona el mes, año y la quincena correspondiente a tus días de descanso.</p>
                                        </div>
                                    </div>

                                    <form wire:submit.prevent="solicitarVacacion" class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Mes</label>
                                                <select wire:model="vacacion_mes" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm font-semibold">
                                                    <option value="">-- Selecciona el mes --</option>
                                                    <option value="1">Enero</option>
                                                    <option value="2">Febrero</option>
                                                    <option value="3">Marzo</option>
                                                    <option value="4">Abril</option>
                                                    <option value="5">Mayo</option>
                                                    <option value="6">Junio</option>
                                                    <option value="7">Julio</option>
                                                    <option value="8">Agosto</option>
                                                    <option value="9">Septiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                                @error('vacacion_mes') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Año</label>
                                                <select wire:model="vacacion_ano" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm font-semibold">
                                                    @php $currentYear = (int) \Carbon\Carbon::now()->year; @endphp
                                                    <option value="{{ $currentYear }}">{{ $currentYear }}</option>
                                                    <option value="{{ $currentYear + 1 }}">{{ $currentYear + 1 }}</option>
                                                    <option value="{{ $currentYear + 2 }}">{{ $currentYear + 2 }}</option>
                                                </select>
                                                @error('vacacion_ano') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Quincena</label>
                                                <select wire:model="vacacion_quincena" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm font-semibold">
                                                    <option value="1">1ª Quincena (del 1 al 15)</option>
                                                    <option value="2">2ª Quincena (del 16 al fin de mes)</option>
                                                </select>
                                                @error('vacacion_quincena') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div class="flex justify-end pt-2">
                                            <button
                                                type="submit"
                                                style="background-color: #0284c7; color: #ffffff;"
                                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl text-xs shadow-md transition-all cursor-pointer"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                                <span>Enviar Solicitud de Vacaciones</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            {{-- 2. Formulario de Permiso Retribuido --}}
                            @if($bloquePermisosActivo === 'permisos')
                                <div>
                                    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-gray-100 dark:border-white/5">
                                        <div class="p-2 bg-emerald-500/10 text-emerald-600 rounded-xl">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-black text-gray-900 dark:text-white">Solicitud de Permiso Retribuido</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Adjunta el justificante y las fechas requeridas para tu permiso retribuido.</p>
                                        </div>
                                    </div>

                                    <form wire:submit.prevent="solicitarVacacion" class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Fecha de Inicio</label>
                                                <input type="date" wire:model="vacacion_fecha_inicio" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm font-semibold" />
                                                @error('vacacion_fecha_inicio') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Fecha de Fin</label>
                                                <input type="date" wire:model="vacacion_fecha_fin" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm font-semibold" />
                                                @error('vacacion_fecha_fin') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Motivo del Permiso Retribuido</label>
                                            <input type="text" wire:model="permiso_motivo" placeholder="Ej. Consulta médica especialista, deber inexcusable..." class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm font-semibold" />
                                            @error('permiso_motivo') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Justificante del Permiso (PDF, Imagen)</label>
                                            <input type="file" wire:model="permiso_justificante" class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950/40 dark:file:text-emerald-300" />
                                            @error('permiso_justificante') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="flex justify-end pt-2">
                                            <button
                                                type="submit"
                                                style="background-color: #059669; color: #ffffff;"
                                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md transition-all cursor-pointer"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                                <span>Enviar Solicitud de Permiso</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            {{-- 3. Formulario de Cambio de Turno con Compañero --}}
                            @if($bloquePermisosActivo === 'cambio_turno')
                                <div>
                                    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-gray-100 dark:border-white/5">
                                        <div class="p-2 bg-indigo-500/10 text-indigo-600 rounded-xl">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-black text-gray-900 dark:text-white">Cambio de Turno con Compañero</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Solicita a un compañero que cubra tu turno. Tu compañero recibirá una alerta para responder.</p>
                                        </div>
                                    </div>

                                    <div class="p-3.5 mb-4 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200/80 dark:border-indigo-900/40 rounded-2xl text-xs text-indigo-900 dark:text-indigo-200 space-y-1">
                                        <p class="font-bold flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Información sobre el funcionamiento del cambio:
                                        </p>
                                        <p class="leading-relaxed">
                                            &bull; <strong>Si indicas fecha de devolución:</strong> Se realizará un intercambio directo de un turno por otro en el Planing. No afectará a turnos a débito ni a crédito.
                                        </p>
                                        <p class="leading-relaxed">
                                            &bull; <strong>Si dejas la fecha de devolución vacía:</strong> Solicitarás que tu compañero cubra tu turno. Al aceptar, se sumará <strong>1 turno a débito</strong> al que solicita y <strong>1 turno a crédito</strong> al compañero que cubre.
                                        </p>
                                    </div>

                                    <form wire:submit.prevent="solicitarVacacion" class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">
                                                    Día que solicitas <span class="text-red-500">*</span>
                                                    <span class="normal-case text-[11px] font-normal text-gray-500 dark:text-gray-400 block">(Día que no puedes ir a trabajar y necesitas que te cubran)</span>
                                                </label>
                                                <input type="date" wire:model.live="cambio_fecha_solicitada" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm font-bold focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />
                                                @error('cambio_fecha_solicitada') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">
                                                    Compañero que te va a sustituir <span class="text-red-500">*</span>
                                                </label>
                                                <select wire:model.live="cambio_empleado_sustituto_id" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-semibold">
                                                    <option value="">-- Selecciona a un compañero --</option>
                                                    @foreach($this->companerosDisponibles as $comp)
                                                        <option value="{{ $comp->id }}">
                                                            {{ $comp->apellidos }}, {{ $comp->nombre }} ({{ $comp->puesto ?? 'Expendedor' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('cambio_empleado_sustituto_id') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">
                                                    Cuándo le devuelves el día
                                                    <span class="normal-case text-[11px] font-normal text-indigo-600 dark:text-indigo-400 block font-semibold">(Opcional - Si lo dejas vacío, genera Turno a Débito/Crédito)</span>
                                                </label>
                                                <input type="date" wire:model.live="cambio_fecha_devolucion" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm font-bold focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />
                                                @error('cambio_fecha_devolucion') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">
                                                    Motivo o comentario para el compañero
                                                    <span class="normal-case text-[11px] font-normal text-gray-500 dark:text-gray-400 block">(Opcional)</span>
                                                </label>
                                                <input type="text" wire:model="cambio_motivo" placeholder="Ej. Cita médica, gracias compañero..." class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-semibold" />
                                                @error('cambio_motivo') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div class="flex justify-end pt-2">
                                            <button
                                                type="submit"
                                                style="background-color: #4f46e5; color: #ffffff;"
                                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-md transition-all cursor-pointer"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                                <span>Enviar Solicitud de Cambio</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            {{-- 4. Sección de Turnos a Débito --}}
                            @if($bloquePermisosActivo === 'debito')
                                <div class="space-y-6">
                                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-white/5">
                                        <div class="p-2 bg-amber-500/10 text-amber-600 rounded-xl">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-black text-gray-900 dark:text-white">Turnos a Débito (Turnos que debo a compañeros)</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Consulta los compañeros a los que les debes turnos y propón fechas para saldar tu débito.</p>
                                        </div>
                                    </div>

                                    {{-- Resumen de saldo por compañero --}}
                                    <div>
                                        <h4 class="text-xs font-black uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-3">Compañeros a los que debo turnos:</h4>
                                        @if($this->saldoDebitos->isNotEmpty())
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                @foreach($this->saldoDebitos as $deb)
                                                    @php $empAcreedor = $deb->empleadoAcreedor; @endphp
                                                    <div class="flex items-center justify-between p-3.5 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 rounded-2xl shadow-2xs">
                                                        <div>
                                                            <div class="text-xs font-black text-gray-900 dark:text-white">
                                                                {{ $empAcreedor ? ($empAcreedor->apellidos . ', ' . $empAcreedor->nombre) : 'Compañero' }}
                                                            </div>
                                                            <span class="inline-block mt-1 px-2.5 py-0.5 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[11px] font-bold rounded-full">
                                                                {{ $deb->turnos_debidos }} {{ $deb->turnos_debidos == 1 ? 'turno debido' : 'turnos debidos' }}
                                                            </span>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            wire:click="abrirModalDevolverDebito({{ $deb->empleado_acreedor_id }})"
                                                            style="background-color: #d97706; color: #ffffff;"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer whitespace-nowrap"
                                                        >
                                                            <span>Devolver Turno</span>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-dashed border-gray-200 dark:border-white/10 text-xs text-gray-500 dark:text-gray-400 text-center">
                                                ✨ ¡Estás al día! No debes ningún turno a ningún compañero actualmente.
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Tabla Paginada de Turnos a Débito --}}
                                    <div class="pt-2">
                                        <h4 class="text-xs font-black uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-3">Histórico de Turnos a Débito:</h4>
                                        <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-white/10 shadow-2xs">
                                            <table class="min-w-full divide-y divide-gray-100 dark:divide-white/10 text-xs">
                                                <thead class="bg-gray-50 dark:bg-gray-800/60 font-bold text-gray-700 dark:text-gray-300">
                                                    <tr>
                                                        <th scope="col" class="px-3.5 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" wire:click="sortByDebito('fecha_inicio')">
                                                            <div class="flex items-center gap-1">
                                                                <span>Fecha</span>
                                                                @if($debitoSortField === 'fecha_inicio')
                                                                    <span>{{ $debitoSortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                                @else
                                                                    <span class="text-gray-400">↕</span>
                                                                @endif
                                                            </div>
                                                        </th>
                                                        <th scope="col" class="px-3.5 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" wire:click="sortByDebito('empleado')">
                                                            <div class="flex items-center gap-1">
                                                                <span>Empleado</span>
                                                                @if($debitoSortField === 'empleado')
                                                                    <span>{{ $debitoSortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                                @else
                                                                    <span class="text-gray-400">↕</span>
                                                                @endif
                                                            </div>
                                                        </th>
                                                        <th scope="col" class="px-3.5 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" wire:click="sortByDebito('tipo')">
                                                            <div class="flex items-center gap-1">
                                                                <span>Concepto / Tipo</span>
                                                                @if($debitoSortField === 'tipo')
                                                                    <span>{{ $debitoSortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                                @else
                                                                    <span class="text-gray-400">↕</span>
                                                                @endif
                                                            </div>
                                                        </th>
                                                        <th scope="col" class="px-3.5 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" wire:click="sortByDebito('estado')">
                                                            <div class="flex items-center gap-1">
                                                                <span>Estado</span>
                                                                @if($debitoSortField === 'estado')
                                                                    <span>{{ $debitoSortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                                @else
                                                                    <span class="text-gray-400">↕</span>
                                                                @endif
                                                            </div>
                                                        </th>
                                                        <th scope="col" class="px-3.5 py-3 text-right">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                                                    @forelse($this->turnosDebitoList as $rDeb)
                                                        @php
                                                            $compRow = $this->getCompaneroDeSolicitud($rDeb);
                                                        @endphp
                                                        <tr class="hover:bg-gray-50/70 dark:hover:bg-white/5 transition-colors">
                                                            <td class="px-3.5 py-3 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                                                {{ \Carbon\Carbon::parse($rDeb->fecha_inicio)->format('d/m/Y') }}
                                                            </td>
                                                            <td class="px-3.5 py-3 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                                                {{ $compRow ? ($compRow->apellidos . ', ' . $compRow->nombre) : '-' }}
                                                            </td>
                                                            <td class="px-3.5 py-3 text-gray-600 dark:text-gray-300">
                                                                @if($rDeb->tipo_cambio === 'devolucion_debito')
                                                                    <span class="inline-flex items-center gap-1 font-medium text-amber-700 dark:text-amber-400">
                                                                        🔄 Propuesta de Devolución
                                                                    </span>
                                                                @elseif($rDeb->tipo_cambio === 'cobro_credito')
                                                                    <span class="inline-flex items-center gap-1 font-medium text-indigo-700 dark:text-indigo-400">
                                                                        📥 Cobro reclamado por compañero
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center gap-1 font-medium text-gray-700 dark:text-gray-300">
                                                                        📤 Turno solicitado a cubrir
                                                                    </span>
                                                                @endif
                                                                @if($rDeb->comentario_empleado)
                                                                    <span class="text-[11px] text-gray-400 block italic">{{ $rDeb->comentario_empleado }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-3.5 py-3 whitespace-nowrap">
                                                                @if($rDeb->estado === 'Pendiente')
                                                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50">
                                                                        Pendiente de Aceptación
                                                                    </span>
                                                                @elseif(in_array($rDeb->estado, ['Aceptada', 'Aprobada']))
                                                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50">
                                                                        {{ $rDeb->deuda_saldada ? 'Validado (Saldado)' : 'Validado' }}
                                                                    </span>
                                                                @else
                                                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-900/50">
                                                                        Denegado
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="px-3.5 py-3 text-right whitespace-nowrap">
                                                                @if($rDeb->estado === 'Pendiente' && (int)$rDeb->empleado_id === (int)$this->empleado->id)
                                                                    <button type="button" wire:click="deleteVacacion({{ $rDeb->id }})" wire:confirm="¿Deseas cancelar esta solicitud?" class="text-red-500 hover:text-red-700 text-xs font-semibold p-1" title="Cancelar Solicitud">
                                                                        Cancelar
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="px-4 py-8 text-center text-xs text-gray-400">
                                                                No hay registros de turnos a débito.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3">
                                            {{ $this->turnosDebitoList->links() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- 5. Sección de Turnos a Crédito --}}
                            @if($bloquePermisosActivo === 'credito')
                                <div class="space-y-6">
                                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-white/5">
                                        <div class="p-2 bg-cyan-500/10 text-cyan-600 rounded-xl">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-black text-gray-900 dark:text-white">Turnos a Crédito (Turnos que me deben compañeros)</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Consulta los compañeros que te deben turnos y solicítales una fecha para cobrar alguno de los turnos a tu favor.</p>
                                        </div>
                                    </div>

                                    {{-- Resumen de saldo por compañero --}}
                                    <div>
                                        <h4 class="text-xs font-black uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-3">Compañeros que me deben turnos:</h4>
                                        @if($this->saldoCreditos->isNotEmpty())
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                @foreach($this->saldoCreditos as $cred)
                                                    @php $empDeudor = $cred->empleadoDeudor; @endphp
                                                    <div class="flex items-center justify-between p-3.5 bg-cyan-50/50 dark:bg-cyan-950/20 border border-cyan-200 dark:border-cyan-900/40 rounded-2xl shadow-2xs">
                                                        <div>
                                                            <div class="text-xs font-black text-gray-900 dark:text-white">
                                                                {{ $empDeudor ? ($empDeudor->apellidos . ', ' . $empDeudor->nombre) : 'Compañero' }}
                                                            </div>
                                                            <span class="inline-block mt-1 px-2.5 py-0.5 bg-cyan-100 dark:bg-cyan-900/60 text-cyan-800 dark:text-cyan-200 text-[11px] font-bold rounded-full">
                                                                {{ $cred->turnos_a_favor }} {{ $cred->turnos_a_favor == 1 ? 'turno a favor' : 'turnos a favor' }}
                                                            </span>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            wire:click="abrirModalCobrarCredito({{ $cred->empleado_deudor_id }})"
                                                            style="background-color: #0891b2; color: #ffffff;"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer whitespace-nowrap"
                                                        >
                                                            <span>Cobrar Turno</span>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-dashed border-gray-200 dark:border-white/10 text-xs text-gray-500 dark:text-gray-400 text-center">
                                                No tienes ningún turno a crédito acumulado a tu favor actualmente.
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Tabla Paginada de Turnos a Crédito --}}
                                    <div class="pt-2">
                                        <h4 class="text-xs font-black uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-3">Histórico de Turnos a Crédito:</h4>
                                        <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-white/10 shadow-2xs">
                                            <table class="min-w-full divide-y divide-gray-100 dark:divide-white/10 text-xs">
                                                <thead class="bg-gray-50 dark:bg-gray-800/60 font-bold text-gray-700 dark:text-gray-300">
                                                    <tr>
                                                        <th scope="col" class="px-3.5 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" wire:click="sortByCredito('fecha_inicio')">
                                                            <div class="flex items-center gap-1">
                                                                <span>Fecha</span>
                                                                @if($creditoSortField === 'fecha_inicio')
                                                                    <span>{{ $creditoSortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                                @else
                                                                    <span class="text-gray-400">↕</span>
                                                                @endif
                                                            </div>
                                                        </th>
                                                        <th scope="col" class="px-3.5 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" wire:click="sortByCredito('empleado')">
                                                            <div class="flex items-center gap-1">
                                                                <span>Empleado</span>
                                                                @if($creditoSortField === 'empleado')
                                                                    <span>{{ $creditoSortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                                @else
                                                                    <span class="text-gray-400">↕</span>
                                                                @endif
                                                            </div>
                                                        </th>
                                                        <th scope="col" class="px-3.5 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" wire:click="sortByCredito('tipo')">
                                                            <div class="flex items-center gap-1">
                                                                <span>Concepto / Tipo</span>
                                                                @if($creditoSortField === 'tipo')
                                                                    <span>{{ $creditoSortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                                @else
                                                                    <span class="text-gray-400">↕</span>
                                                                @endif
                                                            </div>
                                                        </th>
                                                        <th scope="col" class="px-3.5 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" wire:click="sortByCredito('estado')">
                                                            <div class="flex items-center gap-1">
                                                                <span>Estado</span>
                                                                @if($creditoSortField === 'estado')
                                                                    <span>{{ $creditoSortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                                @else
                                                                    <span class="text-gray-400">↕</span>
                                                                @endif
                                                            </div>
                                                        </th>
                                                        <th scope="col" class="px-3.5 py-3 text-right">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                                                    @forelse($this->turnosCreditoList as $rCred)
                                                        @php
                                                            $compRow = $this->getCompaneroDeSolicitud($rCred);
                                                        @endphp
                                                        <tr class="hover:bg-gray-50/70 dark:hover:bg-white/5 transition-colors">
                                                            <td class="px-3.5 py-3 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                                                {{ \Carbon\Carbon::parse($rCred->fecha_inicio)->format('d/m/Y') }}
                                                            </td>
                                                            <td class="px-3.5 py-3 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                                                {{ $compRow ? ($compRow->apellidos . ', ' . $compRow->nombre) : '-' }}
                                                            </td>
                                                            <td class="px-3.5 py-3 text-gray-600 dark:text-gray-300">
                                                                @if($rCred->tipo_cambio === 'cobro_credito')
                                                                    <span class="inline-flex items-center gap-1 font-medium text-cyan-700 dark:text-cyan-400">
                                                                        📥 Cobro de crédito solicitado
                                                                    </span>
                                                                @elseif($rCred->tipo_cambio === 'devolucion_debito')
                                                                    <span class="inline-flex items-center gap-1 font-medium text-emerald-700 dark:text-emerald-400">
                                                                        🔄 Devolución ofrecida por compañero
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center gap-1 font-medium text-gray-700 dark:text-gray-300">
                                                                        🤝 Turno cubierto a compañero
                                                                    </span>
                                                                @endif
                                                                @if($rCred->comentario_empleado)
                                                                    <span class="text-[11px] text-gray-400 block italic">{{ $rCred->comentario_empleado }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-3.5 py-3 whitespace-nowrap">
                                                                @if($rCred->estado === 'Pendiente')
                                                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50">
                                                                        Pendiente de Aceptación
                                                                    </span>
                                                                @elseif(in_array($rCred->estado, ['Aceptada', 'Aprobada']))
                                                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50">
                                                                        {{ $rCred->deuda_saldada ? 'Validado (Cobrado)' : 'Validado' }}
                                                                    </span>
                                                                @else
                                                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-900/50">
                                                                        Denegado
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="px-3.5 py-3 text-right whitespace-nowrap">
                                                                @if($rCred->estado === 'Pendiente' && (int)$rCred->empleado_id === (int)$this->empleado->id)
                                                                    <button type="button" wire:click="deleteVacacion({{ $rCred->id }})" wire:confirm="¿Deseas cancelar esta solicitud?" class="text-red-500 hover:text-red-700 text-xs font-semibold p-1" title="Cancelar Solicitud">
                                                                        Cancelar
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="px-4 py-8 text-center text-xs text-gray-400">
                                                                No hay registros de turnos a crédito.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3">
                                            {{ $this->turnosCreditoList->links() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endif

                    {{-- Histórico General de Vacaciones y Permisos --}}
                    @if(auth()->user()->can('solicitar_ver_vacaciones'))
                        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
                            <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <span class="p-2 bg-sky-500/10 text-sky-600 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                    Histórico de Vacaciones y Permisos
                                </h3>
                            </div>

                            <div class="py-4 space-y-3">
                                @forelse($vacaciones as $v)
                                    <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $v->tipo }}</span>
                                                @if($v->tipo === 'Cambio de Turno' && $v->empleado_sustituto_id)
                                                    <span class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400">
                                                        con {{ $v->empleadoSustituto?->nombre_corto ?? 'Compañero' }}
                                                    </span>
                                                @endif
                                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                                    {{ in_array($v->estado, ['Aceptada', 'Aprobada']) ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : '' }}
                                                    {{ in_array($v->estado, ['Rechazada', 'Denegada']) ? 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' : '' }}
                                                    {{ $v->estado === 'Pendiente' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400' : '' }}
                                                ">
                                                    {{ in_array($v->estado, ['Aceptada', 'Aprobada']) ? 'Aprobada' : (in_array($v->estado, ['Rechazada', 'Denegada']) ? 'Denegada' : 'Pendiente') }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                @if($v->tipo === 'Cambio de Turno')
                                                    Cubre: <strong>{{ \Carbon\Carbon::parse($v->fecha_inicio)->format('d/m/Y') }}</strong> &bull; Devuelve: <strong>{{ $v->fecha_devolucion ? \Carbon\Carbon::parse($v->fecha_devolucion)->format('d/m/Y') : '-' }}</strong>
                                                @else
                                                    Del {{ \Carbon\Carbon::parse($v->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($v->fecha_fin)->format('d/m/Y') }}
                                                @endif
                                            </p>
                                            @if(in_array($v->estado, ['Rechazada', 'Denegada']) && $v->comentario_aprobador)
                                                <p class="text-[11px] text-red-600 dark:text-red-400 mt-1 font-medium italic">
                                                    Motivo: {{ $v->comentario_aprobador }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black text-sky-600 dark:text-sky-400 mr-2">{{ $v->dias_solicitados }} {{ $v->dias_solicitados == 1 ? 'día' : 'días' }}</span>
                                            <button type="button" wire:click="verDetallesSolicitud({{ $v->id }}, 'vacacion')" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors p-1" title="Ver Detalles de la Solicitud">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            @if($v->estado === 'Pendiente')
                                                <button type="button" wire:click="deleteVacacion({{ $v->id }})" wire:confirm="¿Estás seguro de que deseas cancelar esta solicitud de vacaciones?" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Cancelar Solicitud">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-xs text-gray-500 dark:text-gray-400">
                                        No tienes solicitudes de vacaciones registradas.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            
                        @if($activeTab === 'bajas')
                <div class="space-y-6">
                    <!-- Guía e Información de Bajas Médicas e Incapacidad Temporal -->
                    <div class="p-6 bg-gradient-to-r from-rose-50/70 via-orange-50/40 to-amber-50/60 dark:from-rose-950/20 dark:via-orange-950/10 dark:to-amber-950/20 border border-rose-100 dark:border-rose-900/40 rounded-3xl shadow-sm">
                        <div class="flex items-start gap-3.5">
                            <div class="p-2.5 bg-rose-600 text-white rounded-2xl shadow-sm flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="space-y-3 flex-1">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        Protocolo e Información sobre Bajas Médicas e Incapacidad Temporal
                                        <span class="text-[11px] font-semibold px-2.5 py-0.5 bg-rose-100 dark:bg-rose-900/50 text-rose-800 dark:text-rose-300 rounded-full">Comunicación Obligatoria</span>
                                    </h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 leading-relaxed">
                                        Las bajas médicas por enfermedad común, profesional o accidente no precisan aprobación previa de la empresa, pero es indispensable seguir este proceso para la gestión laboral y de Seguridad Social:
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
                                    <div class="bg-white/80 dark:bg-gray-900/80 p-3.5 rounded-2xl border border-rose-100/80 dark:border-white/5 space-y-1">
                                        <div class="flex items-center gap-2 text-xs font-bold text-rose-700 dark:text-rose-400">
                                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-rose-100 dark:bg-rose-900/60 text-[11px] font-black">1</span>
                                            Comunicación y Parte de Baja
                                        </div>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Informa a tu responsable y pulsa <strong>"Registrar Baja"</strong> adjuntando el parte oficial emitido por el facultativo del SPS o Mutua de accidentes.
                                        </p>
                                    </div>

                                    <div class="bg-white/80 dark:bg-gray-900/80 p-3.5 rounded-2xl border border-rose-100/80 dark:border-white/5 space-y-1">
                                        <div class="flex items-center gap-2 text-xs font-bold text-amber-700 dark:text-amber-400">
                                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-amber-100 dark:bg-amber-900/60 text-[11px] font-black">2</span>
                                            Seguimiento y Partes Sucesivos
                                        </div>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Si la situación de incapacidad se prolonga, mantén al día a la empresa aportando los correspondientes partes médicos de confirmación.
                                        </p>
                                    </div>

                                    <div class="bg-white/80 dark:bg-gray-900/80 p-3.5 rounded-2xl border border-rose-100/80 dark:border-white/5 space-y-1">
                                        <div class="flex items-center gap-2 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-[11px] font-black">3</span>
                                            Registro del Alta Médica
                                        </div>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Al reincorporarte al puesto de trabajo, debes pulsar en <strong>"Registrar Alta"</strong> y adjuntar el parte oficial de alta para reactivar tus fichajes ordinarios.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->can('solicitud_baja_enfermedad'))
                        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
                            <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <span class="p-2 bg-rose-500/10 text-rose-600 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </span>
                                    Bajas Médicas
                                </h3>
                                @php
                                    $activeBaja = collect($ausencias)->first(fn($a) => $a->tipo === 'Bajas médicas' && empty($a->fecha_fin));
                                @endphp
                                @if($activeBaja)
                                    <button type="button" wire:click="abrirRegistrarAlta({{ $activeBaja->id }})" style="background-color: #10b981; color: #ffffff;" class="inline-flex items-center gap-1.5 px-3 py-1.5 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Registrar Alta
                                    </button>
                                @else
                                    <button type="button" @click="$dispatch('open-modal', { id: 'solicitar-baja-modal' })" style="background-color: #e11d48; color: #ffffff;" class="inline-flex items-center gap-1.5 px-3 py-1.5 hover:bg-rose-700 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Registrar Baja
                                    </button>
                                @endif
                            </div>

                            <div class="py-4 space-y-3">
                                @forelse($ausencias as $a)
                                    <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $a->tipo }}</span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                                    {{ $a->estado === 'Aceptada' ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : '' }}
                                                    {{ $a->estado === 'Rechazada' ? 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' : '' }}
                                                    {{ $a->estado === 'Pendiente' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400' : '' }}
                                                ">
                                                    {{ $a->estado ?? 'Pendiente' }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Inicio: {{ \Carbon\Carbon::parse($a->fecha_inicio)->format('d/m/Y') }}
                                                @if($a->fecha_fin)
                                                    | Fin: {{ \Carbon\Carbon::parse($a->fecha_fin)->format('d/m/Y') }}
                                                @else
                                                    | <span class="text-rose-600 dark:text-rose-400 font-bold">Activa</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @if($a->justificante_path)
                                                <a href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $a->justificante_path]) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    Justificante Baja
                                                </a>
                                            @endif
                                            @if($a->justificante_alta_path)
                                                <a href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $a->justificante_alta_path]) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                    </svg>
                                                    Justificante Alta
                                                </a>
                                            @endif
                                            @if(empty($a->fecha_fin))
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 font-bold rounded-lg text-[11px] border border-rose-200 dark:border-rose-900/50">
                                                    ● En curso
                                                </span>
                                            @endif
                                            <button type="button" wire:click="verDetallesSolicitud({{ $a->id }}, 'baja')" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors p-1" title="Ver Detalles de la Solicitud">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            @if(($a->estado ?? 'Pendiente') === 'Pendiente')
                                                <button type="button" wire:click="deleteAusencia({{ $a->id }})" wire:confirm="¿Estás seguro de que deseas cancelar esta solicitud de baja médica?" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Cancelar Baja">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-xs text-gray-500 dark:text-gray-400">
                                        No tienes solicitudes de baja médica registradas.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            
            @if($activeTab === 'formacion_empresa' || $activeTab === 'formacion')
                <div class="space-y-6">
                    <!-- Section: Cursos -->
                    <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100 dark:border-white/5">
                            <div class="flex items-center gap-3">
                                <span class="p-2.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-2xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/>
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        Cursos
                                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-bold border border-indigo-500/20">
                                            {{ count($cursos) }} {{ count($cursos) === 1 ? 'curso' : 'cursos' }}
                                        </span>
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Cursos y planes de formación corporativos asignados por los gestores o administradores de recursos humanos.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if(count($cursos) === 0)
                            <div class="py-10 text-center space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/5 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300">No tienes cursos asignados actualmente</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                                    Cuando el departamento de administración registre un curso o plan de formación en tu expediente, aparecerá aquí disponible con su estado y certificado.
                                </p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($cursos as $c)
                                    <div class="p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5 hover:border-indigo-500/30 transition-all space-y-3 flex flex-col justify-between">
                                        <div class="space-y-2">
                                            <div class="flex items-start justify-between gap-2">
                                                <h5 class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                                                    {{ $c->nombre_curso }}
                                                </h5>
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold shrink-0
                                                    {{ $c->estado === 'Realizado' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : '' }}
                                                    {{ $c->estado === 'En curso' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : '' }}
                                                    {{ $c->estado === 'Asignado' ? 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-400 border border-sky-200 dark:border-sky-800' : '' }}
                                                ">
                                                    {{ $c->estado ?? 'Asignado' }}
                                                </span>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                                @if($c->fecha_inicio)
                                                    <span>Inicio: <strong class="font-mono text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($c->fecha_inicio)->format('d/m/Y') }}</strong></span>
                                                @endif
                                                @if($c->fecha_fin)
                                                    <span>• Fin: <strong class="font-mono text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y') }}</strong></span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="pt-2 border-t border-gray-200/60 dark:border-white/5 flex items-center justify-between gap-2">
                                            @if($c->certificado_path)
                                                <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Certificado
                                                </span>
                                                <div class="flex items-center gap-1.5">
                                                    <a href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $c->certificado_path]) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 bg-gray-200/60 dark:bg-white/10 hover:bg-gray-200 transition-all">
                                                        Ver
                                                    </a>
                                                    <a href="{{ route('admin.recursos_humanos.descargar_archivo', ['path' => $c->certificado_path]) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition-all">
                                                        Descargar
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-[11px] text-gray-400 italic">Certificado pendiente</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($activeTab === 'mis_titulaciones')
                <div class="space-y-6">
                    <!-- Section: Diplomas -->
                    <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100 dark:border-white/5">
                            <div class="flex items-center gap-3">
                                <span class="p-2.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-2xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        Diplomas
                                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-400 font-bold border border-amber-500/20">
                                            {{ count($documentosFormacion) }} {{ count($documentosFormacion) === 1 ? 'documento' : 'documentos' }}
                                        </span>
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Títulos profesionales, carnets de conducir/carretilla, PRL o diplomas que has aportado a tu expediente.
                                    </p>
                                </div>
                            </div>

                            
                        </div>

                        <!-- Formulario para que el empleado aporte titulación -->
                        @if($isAddingTitulacion)
                            <div class="p-5 bg-emerald-50/50 dark:bg-emerald-950/10 border border-emerald-200 dark:border-emerald-800/30 rounded-2xl space-y-4">
                                <h4 class="text-sm font-bold text-emerald-900 dark:text-emerald-300 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Aportar Titulación, Diploma o Certificado
                                </h4>
                                
                                <form wire:submit.prevent="guardarTitulacion" class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nombre / Título del documento *</label>
                                            <input type="text" wire:model.defer="nuevo_doc_nombre" placeholder="Ej: Carnet Carretillero, Grado Superior, Curso PRL 20h..." class="w-full text-xs rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                            @error('nuevo_doc_nombre') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Tipo de documento *</label>
                                            <select wire:model.defer="nuevo_doc_tipo" class="w-full text-xs rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                                <option value="Titulaciones">Titulaciones</option>
                                                <option value="Certificados">Certificados / Diplomas</option>
                                                <option value="Carnets">Carnets</option>
                                                <option value="Prevención de riesgos laborales">Prevención de riesgos laborales (PRL)</option>
                                                <option value="Manipulación de alimentos">Manipulación de alimentos</option>
                                                <option value="Otros">Otros</option>
                                            </select>
                                            @error('nuevo_doc_tipo') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Fecha de expedición (opcional)</label>
                                            <input type="date" wire:model.defer="nuevo_doc_fecha_inicio" class="w-full text-xs rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                            @error('nuevo_doc_fecha_inicio') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Fecha de caducidad / renovación (opcional)</label>
                                            <input type="date" wire:model.defer="nuevo_doc_fecha_fin" class="w-full text-xs rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                            @error('nuevo_doc_fecha_fin') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Adjuntar Archivo (PDF o Imagen, máx. 10MB) *</label>
                                            <input type="file" wire:model="nuevo_doc_archivo" accept=".pdf,image/png,image/jpeg,image/jpg" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 dark:file:bg-emerald-950/40 dark:file:text-emerald-300">
                                            <div wire:loading wire:target="nuevo_doc_archivo" class="text-xs text-emerald-600 mt-1">Cargando archivo...</div>
                                            @error('nuevo_doc_archivo') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" wire:click="$set('isAddingTitulacion', false)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-bold transition-all">
                                            Cancelar
                                        </button>
                                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow transition-all">
                                            <span wire:loading.remove wire:target="guardarTitulacion">Guardar y Adjuntar</span>
                                            <span wire:loading wire:target="guardarTitulacion">Guardando...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        @if(count($documentosFormacion) === 0)
                            <div class="py-10 text-center space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/5 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300">No hay diplomas registrados</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                                    Actualmente no constan diplomas o certificados registrados en tu ficha.
                                </p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($documentosFormacion as $doc)
                                    <div class="p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5 hover:border-amber-500/30 transition-all space-y-3 flex flex-col justify-between">
                                        <div class="space-y-1.5">
                                            <span class="text-[10px] uppercase font-extrabold text-amber-600 dark:text-amber-400 block tracking-wider">
                                                {{ $doc->tipo }}
                                            </span>
                                            <h5 class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                                                {{ $doc->nombre }}
                                            </h5>
                                            @if($doc->fecha_inicio || $doc->fecha_fin)
                                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                                    @if($doc->fecha_inicio)
                                                        <span>Expedición: <strong class="font-mono text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($doc->fecha_inicio)->format('d/m/Y') }}</strong></span>
                                                    @endif
                                                    @if($doc->fecha_fin)
                                                        <span>• Caducidad: <strong class="font-mono text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($doc->fecha_fin)->format('d/m/Y') }}</strong></span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <div class="pt-2 border-t border-gray-200/60 dark:border-white/5 flex items-center justify-between gap-2">
                                            @if($doc->file_path)
                                                <span class="text-[11px] text-gray-500 dark:text-gray-400 font-mono truncate max-w-[130px]" title="{{ basename($doc->file_path) }}">
                                                    {{ basename($doc->file_path) }}
                                                </span>
                                                <div class="flex items-center gap-1.5">
                                                    <a href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 bg-gray-200/60 dark:bg-white/10 hover:bg-gray-200 transition-all">
                                                        Ver
                                                    </a>
                                                    <a href="{{ route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold shadow-sm transition-all">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        Descargar
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-[11px] text-gray-400 italic">Sin documento adjunto</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($activeTab === 'contratos_nominas' || $activeTab === 'contratos' || $activeTab === 'nominas')
                <div x-data="{ subTab: '{{ ($activeTab === 'nominas') ? 'nominas' : 'contratos' }}' }" class="space-y-6">
                    <!-- Selector de Pestañas: Contratos y Nóminas -->
                    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-white/10 pb-3">
                        <button
                            type="button"
                            @click="subTab = 'contratos'"
                            :class="subTab === 'contratos'
                                ? 'bg-indigo-600 text-white font-bold shadow-sm shadow-indigo-600/30'
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 font-semibold border border-gray-200 dark:border-white/10'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition-all duration-150 inline-flex items-center gap-2.5 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>{{ __('Contratos') }}</span>
                            <span
                                :class="subTab === 'contratos' ? 'bg-white/20 text-white' : 'bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300'"
                                class="text-xs px-2 py-0.5 rounded-full font-bold"
                            >
                                {{ count($contratos) }}
                            </span>
                        </button>

                        <button
                            type="button"
                            @click="subTab = 'nominas'"
                            :class="subTab === 'nominas'
                                ? 'bg-indigo-600 text-white font-bold shadow-sm shadow-indigo-600/30'
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 font-semibold border border-gray-200 dark:border-white/10'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition-all duration-150 inline-flex items-center gap-2.5 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>{{ __('Nóminas') }}</span>
                        </button>
                    </div>

                    <!-- PESTAÑA: CONTRATOS -->
                    <div x-show="subTab === 'contratos'" class="space-y-6">
                        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm space-y-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100 dark:border-white/5">
                                <div class="flex items-center gap-3">
                                    <span class="p-2.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-2xl">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            {{ __('Mis Contratos') }}
                                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-bold border border-indigo-500/20">
                                                {{ count($contratos) }} {{ count($contratos) === 1 ? 'contrato registrado' : 'contratos registrados' }}
                                            </span>
                                        </h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ __('Consulta y descarga todos los contratos vigentes e históricos asociados a tu puesto en la empresa.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if(count($contratos) === 0)
                                <div class="py-12 text-center space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/5 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('No hay contratos disponibles') }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                                        {{ __('Actualmente no constan contratos registrados en tu expediente.') }}
                                    </p>
                                </div>
                            @else
                                <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-white/5 shadow-xs">
                                    <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
                                        <thead class="bg-gray-50/75 dark:bg-white/5 text-[11px] font-black uppercase text-gray-500 dark:text-gray-400 tracking-wider border-b border-gray-100 dark:border-white/5">
                                            <tr>
                                                <th class="py-3.5 px-4">{{ __('Contrato') }}</th>
                                                <th class="py-3.5 px-4">{{ __('Fecha Inicio') }}</th>
                                                <th class="py-3.5 px-4">{{ __('Fecha Fin / Vencimiento') }}</th>
                                                <th class="py-3.5 px-4">{{ __('Jornada') }}</th>
                                                <th class="py-3.5 px-4">{{ __('Puesto / Estación') }}</th>
                                                <th class="py-3.5 px-4 text-right">{{ __('Acciones') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                            @foreach($contratos as $c)
                                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                                    <td class="py-3.5 px-4">
                                                        <div class="flex items-center gap-2.5">
                                                            <span class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                            </span>
                                                            <div>
                                                                <span class="font-bold text-gray-900 dark:text-white block">
                                                                    {{ $c->tipo_contrato ?: 'Contrato Laboral' }}
                                                                </span>
                                                                @if($c->nombre && $c->nombre !== $c->tipo_contrato)
                                                                    <span class="text-[11px] text-gray-400 font-normal block truncate max-w-xs" title="{{ $c->nombre }}">
                                                                        {{ $c->nombre }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3.5 px-4 whitespace-nowrap font-mono text-gray-700 dark:text-gray-300">
                                                        {{ $c->fecha_inicio ? \Carbon\Carbon::parse($c->fecha_inicio)->format('d/m/Y') : ($c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d/m/Y') : '—') }}
                                                    </td>
                                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                                        @if($c->fecha_fin)
                                                            <span class="font-mono text-gray-700 dark:text-gray-300">
                                                                {{ \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y') }}
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                                                                {{ __('Indefinido') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                                        <span class="text-xs text-gray-600 dark:text-gray-300">
                                                            {{ $c->jornada ?: 'Jornada completa' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3.5 px-4">
                                                        <div class="text-xs">
                                                            <span class="font-medium text-gray-800 dark:text-gray-200 block">
                                                                {{ $c->puesto ?: 'Expendedor' }}
                                                            </span>
                                                            @if($c->centro_trabajo && $c->centro_trabajo !== '—')
                                                                <span class="text-[11px] text-gray-400 block truncate max-w-[200px]" title="{{ $c->centro_trabajo }}">
                                                                    {{ $c->centro_trabajo }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                                        @if($c->file_path)
                                                            <div class="inline-flex items-center gap-1.5 justify-end">
                                                                <!-- Acción: Ver (con un ojito) -->
                                                                <a
                                                                    href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $c->file_path]) }}"
                                                                    target="_blank"
                                                                    title="{{ __('Ver contrato') }}"
                                                                    class="inline-flex items-center justify-center p-2 rounded-xl text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 border border-indigo-200 dark:border-indigo-800/40 transition-all shadow-xs cursor-pointer"
                                                                >
                                                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                    </svg>
                                                                </a>

                                                                <!-- Acción: Imprimir -->
                                                                <button
                                                                    type="button"
                                                                    onclick="imprimirContrato('{{ route('admin.recursos_humanos.ver_archivo', ['path' => $c->file_path]) }}')"
                                                                    title="{{ __('Imprimir contrato') }}"
                                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/15 border border-gray-200 dark:border-white/10 transition-all shadow-xs cursor-pointer"
                                                                >
                                                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                                    </svg>
                                                                    <span>{{ __('Imprimir') }}</span>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <span class="text-[11px] text-gray-400 italic">{{ __('Sin archivo adjunto') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- PESTAÑA: NÓMINAS (En blanco) -->
                    <div x-show="subTab === 'nominas'" style="display: none;" class="space-y-6">
                        <div class="p-12 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm text-center">
                            <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950/30 text-indigo-500 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-base font-bold text-gray-800 dark:text-gray-200">{{ __('Nóminas') }}</h4>
                            <p class="text-xs text-gray-400 dark:text-gray-500 max-w-sm mx-auto mt-1">
                                {{ __('Actualmente no hay nóminas disponibles.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <script>
                    function imprimirContrato(url) {
                        const win = window.open(url, '_blank');
                        if (win) {
                            win.focus();
                            win.onload = function() {
                                try {
                                    win.print();
                                } catch(e) {}
                            };
                        }
                    }
                </script>
            @endif

            <!-- MODALS -->
            <!-- Modal Flotante: Registrar Entrada -->
            <x-filament::modal id="checkin-modal" width="md">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 bg-green-500/10 text-green-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        </span>
                        <span>Registrar Entrada</span>
                        @if($fichajesDelDia && $fichajesDelDia->count() > 0)
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 font-bold border border-blue-200 dark:border-blue-800">
                                Turno {{ $fichajesDelDia->count() + 1 }}
                            </span>
                        @endif
                    </div>
                </x-slot>

                <div class="space-y-4 py-3" x-data="{
                    loading: false,
                    doCheckInModal() {
                        this.loading = true;
                        getFichajeGeolocation((lat, lng) => {
                            $wire.checkIn(lat, lng)
                                .then(() => {
                                    this.loading = false;
                                    $dispatch('close-modal', { id: 'checkin-modal' });
                                })
                                .catch(() => {
                                    this.loading = false;
                                });
                        });
                    }
                }">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Selecciona la hora de inicio de tu turno. El sistema almacenará internamente la hora real y la ubicación GPS.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-2">Hora de Entrada</label>
                        <input type="time" wire:model="hora_entrada" class="w-full text-center text-3xl font-extrabold py-3 rounded-2xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white focus:border-green-500 focus:ring-green-500 shadow-sm" />
                    </div>

                    <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-100 dark:border-white/5 space-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center justify-between">
                            <span>🕒 Hora real del sistema:</span>
                            <span class="font-mono font-bold text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::now()->timezone('Europe/Madrid')->format('H:i:s') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>📍 Geolocalización:</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Captura automática GPS
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-filament::button color="gray" type="button" x-bind:disabled="loading" x-on:click="$dispatch('close-modal', { id: 'checkin-modal' })">
                            Cancelar
                        </x-filament::button>
                        <button type="button" @click="doCheckInModal" x-bind:disabled="loading" style="background-color: #16a34a; color: #ffffff;" class="py-2.5 px-5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-600/10 hover:shadow-green-700/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50 text-sm">
                            <span x-show="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full" style="display: none;"></span>
                            <span x-show="!loading">Confirmar Entrada</span>
                            <span x-show="loading" style="display: none;">Registrando...</span>
                        </button>
                    </div>
                </div>
            </x-filament::modal>

            <!-- Modal Flotante: Registrar Salida -->
            <x-filament::modal id="checkout-modal" width="md">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 bg-orange-500/10 text-orange-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </span>
                        <span>Registrar Salida</span>
                    </div>
                </x-slot>

                <div class="space-y-4 py-3" x-data="{
                    loading: false,
                    doCheckOutModal() {
                        this.loading = true;
                        getFichajeGeolocation((lat, lng) => {
                            $wire.checkOut(lat, lng)
                                .then(() => {
                                    this.loading = false;
                                    $dispatch('close-modal', { id: 'checkout-modal' });
                                })
                                .catch(() => {
                                    this.loading = false;
                                });
                        });
                    }
                }">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Selecciona la hora de finalización de tu turno. El sistema almacenará internamente la hora real y la ubicación GPS.
                    </p>

                    @if($fichajeActivo && $fichajeActivo->hora_entrada)
                        <div class="p-3 bg-amber-50 dark:bg-amber-950/20 rounded-xl border border-amber-200 dark:border-amber-900/40 text-xs text-amber-800 dark:text-amber-300 flex items-center justify-between">
                            <span>Entrada registrada de este turno:</span>
                            <span class="font-bold font-mono text-sm">{{ \Carbon\Carbon::parse($fichajeActivo->hora_entrada)->format('H:i') }}</span>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-2">Hora de Salida</label>
                        <input type="time" wire:model="hora_salida" class="w-full text-center text-3xl font-extrabold py-3 rounded-2xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white focus:border-orange-500 focus:ring-orange-500 shadow-sm" />
                    </div>

                    <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-100 dark:border-white/5 space-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center justify-between">
                            <span>🕒 Hora real del sistema:</span>
                            <span class="font-mono font-bold text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::now()->timezone('Europe/Madrid')->format('H:i:s') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>📍 Geolocalización:</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Captura automática GPS
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-filament::button color="gray" type="button" x-bind:disabled="loading" x-on:click="$dispatch('close-modal', { id: 'checkout-modal' })">
                            Cancelar
                        </x-filament::button>
                        <button type="button" @click="doCheckOutModal" x-bind:disabled="loading" style="background-color: #ea580c; color: #ffffff;" class="py-2.5 px-5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg shadow-orange-600/10 hover:shadow-orange-700/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50 text-sm">
                            <span x-show="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full" style="display: none;"></span>
                            <span x-show="!loading">Confirmar Salida</span>
                            <span x-show="loading" style="display: none;">Registrando...</span>
                        </button>
                    </div>
                </div>
            </x-filament::modal>

            <x-filament::modal id="edit-fichaje-modal" width="md">
                <x-slot name="heading">
                    Editar Fichaje del {{ $editingFecha ? \Carbon\Carbon::parse($editingFecha)->format('d/m/Y') : '' }}
                </x-slot>

                <div class="space-y-4 py-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Hora de Entrada</label>
                        <input type="time" wire:model="editingHoraEntrada" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Hora de Salida</label>
                        <input type="time" wire:model="editingHoraSalida" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'edit-fichaje-modal' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="success" wire:click="updateFichaje" style="background-color: #16a34a !important; color: #ffffff !important; border-color: #16a34a !important;">
                            Guardar Cambios
                        </x-filament::button>
                    </div>
                </x-slot>
            </x-filament::modal>

            <x-filament::modal id="delete-fichaje-modal" width="md">
                <x-slot name="heading">
                    ¿Eliminar registro de fichaje?
                </x-slot>

                <div class="py-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        ¿Estás seguro de que deseas eliminar este registro de fichaje? Esta acción no se puede deshacer y borrará el historial de entrada y salida del día correspondiente.
                    </p>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'delete-fichaje-modal' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="danger" wire:click="confirmDeleteFichaje">
                            Eliminar Registro
                        </x-filament::button>
                    </div>
                </x-slot>
            </x-filament::modal>

            <!-- modal-cobrar-credito -->
            <x-filament::modal id="modal-cobrar-credito" width="md">
                <x-slot name="heading">
                    Cobrar Turno a Crédito
                </x-slot>

                @php
                    $deudorObj = $cobro_empleado_deudor_id ? \App\Models\Empleado::find($cobro_empleado_deudor_id) : null;
                @endphp

                <form wire:submit.prevent="enviarSolicitudCobroCredito" class="space-y-4 py-4">
                    <div class="p-3.5 bg-cyan-50 dark:bg-cyan-950/30 border border-cyan-200 dark:border-cyan-900/40 rounded-2xl text-xs text-cyan-900 dark:text-cyan-200">
                        <p class="font-bold">Compañero: {{ $deudorObj ? ($deudorObj->apellidos . ', ' . $deudorObj->nombre) : '' }}</p>
                        <p class="mt-1 text-[11px] text-cyan-700 dark:text-cyan-300">
                            Selecciona la fecha en la que <strong>no puedes acudir a trabajar</strong> para que tu compañero cubra tu turno y se salde el turno a tu favor.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                            Fecha en la que necesitas que te cubra <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model.live="cobro_fecha" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm font-bold focus:border-cyan-500 focus:ring-cyan-500 shadow-sm" />
                        @error('cobro_fecha') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                            Motivo o comentario (opcional)
                        </label>
                        <input type="text" wire:model="cobro_motivo" placeholder="Ej. Compensación del turno de la semana pasada..." class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-cyan-500 focus:ring-cyan-500 shadow-sm" />
                        @error('cobro_motivo') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'modal-cobrar-credito' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="info" type="submit">
                            Solicitar Cobertura de Turno
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::modal>

            <!-- modal-devolver-turno -->
            <x-filament::modal id="modal-devolver-turno" width="md">
                <x-slot name="heading">
                    Proponer Devolución de Turno a Débito
                </x-slot>

                @php
                    $acreedorObj = $devolucion_empleado_acreedor_id ? \App\Models\Empleado::find($devolucion_empleado_acreedor_id) : null;
                @endphp

                <form wire:submit.prevent="enviarSolicitudDevolucionDebito" class="space-y-4 py-4">
                    <div class="p-3.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 rounded-2xl text-xs text-amber-900 dark:text-amber-200">
                        <p class="font-bold">Compañero: {{ $acreedorObj ? ($acreedorObj->apellidos . ', ' . $acreedorObj->nombre) : '' }}</p>
                        <p class="mt-1 text-[11px] text-amber-700 dark:text-amber-300">
                            Selecciona la fecha en la que <strong>te ofreces a cubrir el turno de tu compañero</strong> para saldar tu turno a débito.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                            Fecha en la que te ofreces a cubrir su turno <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model.live="devolucion_fecha" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                        @error('devolucion_fecha') <span class="text-xs text-red-500 block mt-1 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                            Motivo o comentario (opcional)
                        </label>
                        <input type="text" wire:model="devolucion_motivo" placeholder="Ej. Te cubro este viernes para devolverte el turno..." class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                        @error('devolucion_motivo') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'modal-devolver-turno' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="warning" type="submit">
                            Enviar Oferta de Devolución
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::modal>

            <!-- solicitar-baja-modal -->
            <x-filament::modal id="solicitar-baja-modal" width="md">
                <x-slot name="heading">
                    Registrar Baja Médica
                </x-slot>

                <form wire:submit.prevent="solicitarBaja" class="space-y-4 py-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Fecha de Inicio de la Baja</label>
                        <input type="date" wire:model="baja_fecha_inicio" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-rose-500 focus:ring-rose-500 shadow-sm" />
                        @error('baja_fecha_inicio') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Fecha de Finalización Estimada (Opcional)</label>
                        <input type="date" wire:model="baja_fecha_fin" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-rose-500 focus:ring-rose-500 shadow-sm" />
                        @error('baja_fecha_fin') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Justificante Médico (PDF, Imagen)</label>
                        <input type="file" wire:model="baja_justificante" class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100" />
                        @error('baja_justificante') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'solicitar-baja-modal' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="danger" type="submit">
                            Registrar Baja
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::modal>

            <!-- registrar-alta-modal -->
            <x-filament::modal id="registrar-alta-modal" width="md">
                <x-slot name="heading">
                    Registrar Alta Médica (Fin de Baja)
                </x-slot>

                <form wire:submit.prevent="registrarAlta" class="space-y-4 py-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Fecha de Finalización de la Baja (Alta)</label>
                        <input type="date" wire:model="alta_fecha_fin" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" />
                        @error('alta_fecha_fin') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Justificante Médico de Alta (PDF, Imagen)</label>
                        <input type="file" wire:model="alta_justificante" class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" />
                        @error('alta_justificante') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'registrar-alta-modal' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="success" type="submit">
                            Registrar Alta
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::modal>
        @endif

        <!-- Floating Modal for Solicitud Details (for employees) -->
        @if($selectedSolicitud)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="cerrarDetallesSolicitud">
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl w-full max-w-xl overflow-hidden transform transition-all border border-gray-100 dark:border-white/5">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-1.5 bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        Detalles de la Solicitud
                    </h3>
                    <button type="button" wire:click="cerrarDetallesSolicitud" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Content -->
                <div class="p-6 space-y-5">
                    <!-- Status Row -->
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50 dark:bg-gray-950/20 border border-gray-100 dark:border-white/5">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Estado de la Solicitud</span>
                        @if($selectedSolicitud->estado === 'Aceptada')
                            <span class="px-3 py-1 bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 rounded-full text-xs font-bold">
                                Aprobada
                            </span>
                        @elseif($selectedSolicitud->estado === 'Rechazada')
                            <span class="px-3 py-1 bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 rounded-full text-xs font-bold">
                                Denegada
                            </span>
                        @else
                            <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold animate-pulse">
                                Pendiente
                            </span>
                        @endif
                    </div>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipo de Solicitud</span>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ $selectedSolicitudType === 'vacacion' ? 'Vacaciones (' . ($selectedSolicitud->tipo ?? 'Normal') . ')' : 'Baja Médica / Ausencia' }}
                            </p>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Fecha de Inicio</span>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 font-mono">
                                {{ \Carbon\Carbon::parse($selectedSolicitud->fecha_inicio)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Fecha Fin / Prevista</span>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 font-mono">
                                {{ $selectedSolicitud->fecha_fin ? \Carbon\Carbon::parse($selectedSolicitud->fecha_fin)->format('d/m/Y') : 'No definida' }}
                            </p>
                        </div>
                        @if($selectedSolicitudType === 'vacacion')
                            <div>
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Días Solicitados</span>
                                <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400 font-mono">
                                    {{ $selectedSolicitud->dias_solicitados }}
                                </p>
                            </div>
                        @endif
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Última Actualización</span>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ \Carbon\Carbon::parse($selectedSolicitud->updated_at)->translatedFormat('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    @if($selectedSolicitud->comentario_empleado)
                        <div class="p-3 bg-gray-50 dark:bg-gray-950/20 border border-gray-100 dark:border-white/5 rounded-2xl">
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tu Explicación</span>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $selectedSolicitud->comentario_empleado }}
                            </p>
                        </div>
                    @endif

                    @if($selectedSolicitud->comentario_aprobador)
                        <div class="p-3 bg-amber-500/5 border border-amber-500/20 rounded-2xl">
                            <span class="block text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1">Motivo / Razón de la Resolución</span>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                                {{ $selectedSolicitud->comentario_aprobador }}
                            </p>
                        </div>
                    @endif

                    @if($selectedSolicitudType === 'baja' && $selectedSolicitud->justificante_path)
                        <div class="p-3 bg-indigo-500/5 border border-indigo-500/20 rounded-2xl flex items-center justify-between">
                            <div>
                                <span class="block text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Documento Justificante</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Hay un archivo justificante adjunto.</span>
                            </div>
                            <a href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $selectedSolicitud->justificante_path]) }}" target="_blank" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all text-center">
                                Abrir Justificante
                            </a>
                        </div>
                    @endif
                </div>
                
                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-950/20 border-t border-gray-100 dark:border-white/5 flex items-center justify-end">
                    <button type="button" wire:click="cerrarDetallesSolicitud" style="background-color: #4f46e5; color: #ffffff;" class="px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition-all hover:bg-indigo-700">
                        Cerrar Detalles
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Retroactive Check-in Modal -->
        @if($selectedRetroactiveDate)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold">
                            @if($isCreatingNewRetroactive)
                                Registrar Fichaje Retroactivo
                            @else
                                Registrar Fichaje Faltante
                            @endif
                        </h3>
                        @if(!$isCreatingNewRetroactive)
                            <p class="text-xs text-amber-100 mt-0.5">
                                Fecha: {{ \Carbon\Carbon::parse($selectedRetroactiveDate)->translatedFormat('l, d \d\e F \d\e Y') }}
                            </p>
                        @endif
                    </div>
                    <button type="button" wire:click="cerrarFichajeRetroactivo" class="text-white hover:text-amber-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div class="space-y-4">
                        @if($isCreatingNewRetroactive)
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Selecciona Fecha <span class="text-red-500">*</span></label>
                                <input type="date" max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" wire:model.live="retroactive_fecha" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm font-semibold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                            </div>
                        @endif
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Hora de Entrada <span class="text-red-500">*</span></label>
                            <input type="time" wire:model="retroactive_hora_entrada" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Hora de Salida</label>
                            <input type="time" wire:model="retroactive_hora_salida" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                            <p class="text-[10px] text-gray-400 mt-1">Puedes dejar la salida en blanco si la jornada sigue abierta, aunque para días pasados se recomienda indicar ambas horas.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-950/20 border-t border-gray-100 dark:border-white/5 flex items-center justify-end gap-3">
                    <button type="button" wire:click="cerrarFichajeRetroactivo" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/15 text-gray-700 dark:text-gray-200 rounded-xl text-xs font-bold transition-all">
                        Cancelar
                    </button>
                    <button type="button" wire:click="guardarFichajeRetroactivo" style="background-color: #16a34a; color: #ffffff;" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Fichaje
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script>
        function getFichajeGeolocation(callback) {
            if (!navigator.geolocation) {
                callback(null, null);
                return;
            }

            // 1. Intentar con alta precisión (GPS por satélite) hasta 10 segundos
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    callback(pos.coords.latitude, pos.coords.longitude);
                },
                (err) => {
                    console.warn('Alta precisión no disponible o agotada, reintentando con precisión de red/wifi...', err);
                    // 2. Si falla o timeout en satélite, intentar con red/wifi antes de descartar
                    navigator.geolocation.getCurrentPosition(
                        (fallbackPos) => {
                            callback(fallbackPos.coords.latitude, fallbackPos.coords.longitude);
                        },
                        (finalErr) => {
                            console.warn('Geolocalización no disponible:', finalErr);
                            callback(null, null);
                        },
                        { enableHighAccuracy: false, timeout: 6000, maximumAge: 60000 }
                    );
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
            );
        }
    </script>
</x-filament-panels::page>
