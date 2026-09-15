<div class="min-h-screen bg-slate-50 dark:bg-gray-950 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">
        
        {{-- Cabecera con Logo y Bienvenida --}}
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center p-3 bg-amber-500/10 rounded-2xl border border-amber-500/20 text-amber-600 dark:text-amber-400 mb-1 shadow-sm">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Proceso de Onboarding e Incorporación
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 max-w-xl mx-auto">
                ¡Hola <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $empleado->nombre }}</span>! Por favor, completa los siguientes 4 pasos para configurar tu cuenta y formalizar tu incorporación.
            </p>
        </div>

        {{-- Stepper / Indicador de Pasos --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-200/80 dark:border-white/10">
            <div class="grid grid-cols-4 gap-2 sm:gap-4 relative">
                
                {{-- Paso 1 --}}
                <button type="button" wire:click="irPaso(1)" class="flex flex-col items-center text-center group cursor-pointer">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center font-bold text-xs sm:text-sm transition-all {{ $paso === 1 ? 'bg-amber-600 text-white ring-4 ring-amber-500/20 shadow-md' : ($empleado->onboarding_paso_actual > 1 ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-400') }}">
                        @if($empleado->onboarding_paso_actual > 1 && $paso !== 1)
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            1
                        @endif
                    </div>
                    <span class="text-[11px] sm:text-xs font-semibold mt-2 {{ $paso === 1 ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Seguridad</span>
                </button>

                {{-- Paso 2 --}}
                <button type="button" wire:click="irPaso(2)" class="flex flex-col items-center text-center group {{ $empleado->onboarding_paso_actual >= 2 ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center font-bold text-xs sm:text-sm transition-all {{ $paso === 2 ? 'bg-amber-600 text-white ring-4 ring-amber-500/20 shadow-md' : ($empleado->onboarding_paso_actual > 2 ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-400') }}">
                        @if($empleado->onboarding_paso_actual > 2 && $paso !== 2)
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            2
                        @endif
                    </div>
                    <span class="text-[11px] sm:text-xs font-semibold mt-2 {{ $paso === 2 ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Datos</span>
                </button>

                {{-- Paso 3 --}}
                <button type="button" wire:click="irPaso(3)" class="flex flex-col items-center text-center group {{ $empleado->onboarding_paso_actual >= 3 ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center font-bold text-xs sm:text-sm transition-all {{ $paso === 3 ? 'bg-amber-600 text-white ring-4 ring-amber-500/20 shadow-md' : ($empleado->onboarding_paso_actual > 3 ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-400') }}">
                        @if($empleado->onboarding_paso_actual > 3 && $paso !== 3)
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            3
                        @endif
                    </div>
                    <span class="text-[11px] sm:text-xs font-semibold mt-2 {{ $paso === 3 ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Documentos</span>
                </button>

                {{-- Paso 4 --}}
                <button type="button" wire:click="irPaso(4)" class="flex flex-col items-center text-center group {{ $empleado->onboarding_paso_actual >= 4 ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center font-bold text-xs sm:text-sm transition-all {{ $paso === 4 ? 'bg-amber-600 text-white ring-4 ring-amber-500/20 shadow-md' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }}">
                        4
                    </div>
                    <span class="text-[11px] sm:text-xs font-semibold mt-2 {{ $paso === 4 ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Políticas</span>
                </button>
            </div>
        </div>

        {{-- Alertas y Mensajes --}}
        @if (session()->has('success_step'))
            <div class="p-4 rounded-xl bg-green-50 dark:bg-green-950/30 text-green-800 dark:text-green-300 text-sm border border-green-200 dark:border-green-800/40 shadow-sm flex items-center gap-3 animate-fade-in">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success_step') }}</span>
            </div>
        @endif

        {{-- CONTENIDO DEL WIZARD SEGÚN EL PASO --}}
        <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 sm:p-10 shadow-lg border border-gray-200/80 dark:border-white/10 transition-all">
            
            {{-- ======================== PASO 1: CAMBIO DE CONTRASEÑA ======================== --}}
            @if ($paso === 1)
                <div class="space-y-6">
                    <div class="border-b border-gray-100 dark:border-white/5 pb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-extrabold">1</span>
                            Seguridad: Establece tu Nueva Contraseña
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Por motivos de seguridad y confidencialidad, debes cambiar tu contraseña inicial por una contraseña personal y segura.
                        </p>
                    </div>

                    <form wire:submit.prevent="guardarPaso1" class="space-y-5 max-w-lg">
                        @if (!\Illuminate\Support\Facades\Hash::check('1234', auth()->user()->password))
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Contraseña Actual</label>
                                <input type="password" wire:model="current_password" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" placeholder="Introduce tu contraseña actual" />
                                @error('current_password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nueva Contraseña <span class="text-red-500">*</span></label>
                            <input type="password" wire:model="new_password" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" placeholder="Mínimo 8 caracteres" />
                            @error('new_password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Confirmar Nueva Contraseña <span class="text-red-500">*</span></label>
                            <input type="password" wire:model="new_password_confirmation" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" placeholder="Repite la nueva contraseña" />
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg gap-2">
                                <span>Guardar Contraseña y Continuar</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ======================== PASO 2: DATOS PERSONALES, NUSS E IBAN ======================== --}}
            @if ($paso === 2)
                <div class="space-y-6">
                    <div class="border-b border-gray-100 dark:border-white/5 pb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-extrabold">2</span>
                            Datos Personales, Seguridad Social y Cuenta Bancaria
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Verifica y completa tu información para la gestión de contratos, afiliación a la Seguridad Social y pago de nóminas.
                        </p>
                    </div>

                    <form wire:submit.prevent="guardarPaso2" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="nombre" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('nombre') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Apellidos <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="apellidos" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('apellidos') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">DNI / NIE <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="dni" placeholder="Ej: 12345678Z" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm uppercase" />
                                @error('dni') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Fecha de Nacimiento <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="fecha_nacimiento" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('fecha_nacimiento') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Teléfono Principal <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="telefono_principal" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('telefono_principal') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nº Afiliación Seguridad Social (NUSS) <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="nuss" placeholder="Ej: 411234567890" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('nuss') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Código Cuenta Bancaria (IBAN) <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="iban" placeholder="ES00 0000 0000 0000 0000 0000" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm uppercase font-mono tracking-wider" />
                                @error('iban') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Dirección Completa <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="direccion" placeholder="Calle, número, piso, puerta..." class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('direccion') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Código Postal <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="codigo_postal" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('codigo_postal') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Localidad <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="localidad" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('localidad') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Provincia <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="provincia" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('provincia') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nombre Contacto de Emergencia <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="contacto_emergencia_nombre" placeholder="Familiar o persona de contacto" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('contacto_emergencia_nombre') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Teléfono Contacto de Emergencia <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="contacto_emergencia_telefono" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('contacto_emergencia_telefono') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-white/5">
                            <button type="button" wire:click="irPaso(1)" class="px-5 py-2.5 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-xs font-bold transition-all">
                                ← Volver
                            </button>
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg gap-2">
                                <span>Guardar y Continuar a Documentación</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ======================== PASO 3: DOCUMENTACIÓN ======================== --}}
            @if ($paso === 3)
                <div class="space-y-6">
                    <div class="border-b border-gray-100 dark:border-white/5 pb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-extrabold">3</span>
                            Documentación Obligatoria y Archivos Adjuntos
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Adjunta copia legible de tus documentos para validar tu expediente laboral. Formatos aceptados: PDF, JPG, PNG (máx. 10MB).
                        </p>
                    </div>

                    <form wire:submit.prevent="guardarPaso3" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- DNI --}}
                            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.333 0 4 .667 4 2v1H9v-1c0-1.333 2.667-2 4-2z"/></svg>
                                        DNI / NIE (Ambas Caras) <span class="text-red-500">*</span>
                                    </span>
                                    @if ($empleado->documentos()->where('tipo', 'DNI')->exists())
                                        <span class="text-[10px] text-green-600 dark:text-green-400 font-semibold bg-green-100 dark:bg-green-950/30 px-2 py-0.5 rounded-full">✓ Ya adjuntado</span>
                                    @endif
                                </div>
                                <label class="inline-flex items-center justify-center px-4 py-3 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-xl text-xs font-semibold cursor-pointer hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all border border-amber-300 dark:border-amber-700/30 w-full text-center">
                                    <span>Seleccionar archivo DNI...</span>
                                    <input type="file" wire:model="file_dni" class="hidden" />
                                </label>
                                @if ($file_dni)
                                    <span class="text-xs text-green-600 dark:text-green-400 block font-medium">✓ Archivo cargado: {{ $file_dni->getClientOriginalName() }}</span>
                                @endif
                                @error('file_dni') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror

                                <div class="pt-2">
                                    <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Fecha de Caducidad del DNI <span class="text-red-500">*</span></label>
                                    <input type="date" wire:model="fecha_caducidad_dni" class="w-full rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                    @error('fecha_caducidad_dni') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Certificado Bancario --}}
                            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        Certificado Titularidad Bancaria / Justificante IBAN
                                    </span>
                                </div>
                                <label class="inline-flex items-center justify-center px-4 py-3 bg-cyan-50 dark:bg-cyan-950/20 text-cyan-700 dark:text-cyan-400 rounded-xl text-xs font-semibold cursor-pointer hover:bg-cyan-100 dark:hover:bg-cyan-900/40 transition-all border border-cyan-300 dark:border-cyan-700/30 w-full text-center">
                                    <span>Seleccionar justificante bancario...</span>
                                    <input type="file" wire:model="file_banco" class="hidden" />
                                </label>
                                @if ($file_banco)
                                    <span class="text-xs text-green-600 dark:text-green-400 block font-medium">✓ Archivo cargado: {{ $file_banco->getClientOriginalName() }}</span>
                                @endif
                                @error('file_banco') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                <p class="text-[10px] text-gray-400">Documento emitido por tu banco donde aparezca tu nombre completo e IBAN.</p>
                            </div>

                            {{-- Formación / PRL --}}
                            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 space-y-3">
                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    Cursos Previos de PRL / Formación (Opcional)
                                </span>
                                <label class="inline-flex items-center justify-center px-4 py-3 bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 rounded-xl text-xs font-semibold cursor-pointer hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-all border border-indigo-300 dark:border-indigo-700/30 w-full text-center">
                                    <span>Seleccionar títulos/diplomas...</span>
                                    <input type="file" wire:model="file_prl" class="hidden" />
                                </label>
                                @if ($file_prl)
                                    <span class="text-xs text-green-600 dark:text-green-400 block font-medium">✓ Archivo cargado: {{ $file_prl->getClientOriginalName() }}</span>
                                @endif
                                @error('file_prl') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Discapacidad --}}
                            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 space-y-3">
                                <label class="flex items-center gap-2.5 text-xs font-bold text-gray-800 dark:text-gray-200 cursor-pointer">
                                    <input type="checkbox" wire:model.live="tiene_discapacidad" class="rounded border-gray-300 dark:border-white/10 text-amber-600 focus:ring-amber-500 shadow-sm" />
                                    <span>Tengo certificado de discapacidad reconocido</span>
                                </label>
                                @if ($tiene_discapacidad)
                                    <div class="pt-2 space-y-2">
                                        <label class="inline-flex items-center justify-center px-4 py-3 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-xl text-xs font-semibold cursor-pointer hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all border border-amber-300 dark:border-amber-700/30 w-full text-center">
                                            <span>Subir Certificado de Discapacidad...</span>
                                            <input type="file" wire:model="file_discapacidad" class="hidden" />
                                        </label>
                                        @if ($file_discapacidad)
                                            <span class="text-xs text-green-600 dark:text-green-400 block font-medium">✓ Archivo cargado</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-white/5">
                            <button type="button" wire:click="irPaso(2)" class="px-5 py-2.5 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-xs font-bold transition-all">
                                ← Volver
                            </button>
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg gap-2">
                                <span>Guardar y Continuar a Políticas</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ======================== PASO 4: POLÍTICAS Y CONFIRMACIÓN ======================== --}}
            @if ($paso === 4)
                <div class="space-y-6">
                    <div class="border-b border-gray-100 dark:border-white/5 pb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-extrabold">4</span>
                            Políticas de Empresa, RGPD y Prevención
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Lee y confirma la aceptación de las normativas de la empresa para completar tu incorporación oficial.
                        </p>
                    </div>

                    <form wire:submit.prevent="finalizarOnboarding" class="space-y-6">
                        <div class="space-y-4">
                            
                            {{-- Check RGPD --}}
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 flex items-start gap-3.5">
                                <input type="checkbox" wire:model="acepta_rgpd" id="acepta_rgpd" class="mt-1 rounded border-gray-300 dark:border-white/10 text-amber-600 focus:ring-amber-500 shadow-sm" />
                                <label for="acepta_rgpd" class="text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                    <strong class="block text-gray-900 dark:text-white mb-0.5">Protección de Datos Personales (RGPD / LOPDGDD)</strong>
                                    He sido informado y consiento el tratamiento de mis datos personales con fines exclusivamente laborales, de gestión de nóminas y seguridad social por parte de la empresa.
                                </label>
                            </div>
                            @error('acepta_rgpd') <span class="text-xs text-red-500 block px-2">{{ $message }}</span> @enderror

                            {{-- Check Normativa Interna --}}
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 flex items-start gap-3.5">
                                <input type="checkbox" wire:model="acepta_normativa" id="acepta_normativa" class="mt-1 rounded border-gray-300 dark:border-white/10 text-amber-600 focus:ring-amber-500 shadow-sm" />
                                <label for="acepta_normativa" class="text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                    <strong class="block text-gray-900 dark:text-white mb-0.5">Normativa Interna y Código de Conducta</strong>
                                    Me comprometo a respetar las directrices operativas, horarios asignados, registro de jornada y políticas internas de la organización.
                                </label>
                            </div>
                            @error('acepta_normativa') <span class="text-xs text-red-500 block px-2">{{ $message }}</span> @enderror

                            {{-- Check PRL --}}
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 flex items-start gap-3.5">
                                <input type="checkbox" wire:model="acepta_prl" id="acepta_prl" class="mt-1 rounded border-gray-300 dark:border-white/10 text-amber-600 focus:ring-amber-500 shadow-sm" />
                                <label for="acepta_prl" class="text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                    <strong class="block text-gray-900 dark:text-white mb-0.5">Prevención de Riesgos Laborales (PRL) y Seguridad</strong>
                                    Confirmo que he recibido las directrices sobre seguridad y salud en el puesto de trabajo, comprometiéndome al uso adecuado de EPIs y cumplimiento de protocolos de seguridad.
                                </label>
                            </div>
                            @error('acepta_prl') <span class="text-xs text-red-500 block px-2">{{ $message }}</span> @enderror

                        </div>

                        <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-center space-y-2">
                            <span class="text-sm font-bold text-amber-800 dark:text-amber-300 block">✨ Todo listo para empezar</span>
                            <p class="text-xs text-gray-600 dark:text-gray-400 max-w-lg mx-auto">
                                Al pulsar en finalizar, tu expediente quedará registrado para que el equipo de Recursos Humanos valide tu incorporación. Tendrás acceso inmediato al portal del empleado.
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-white/5">
                            <button type="button" wire:click="irPaso(3)" class="px-5 py-2.5 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-xs font-bold transition-all">
                                ← Volver
                            </button>
                            <button type="submit" class="inline-flex items-center justify-center px-8 py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl gap-2">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Finalizar Onboarding y Acceder al Portal</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>

    </div>
</div>
