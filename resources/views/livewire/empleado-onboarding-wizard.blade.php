<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6" x-data="{
    modalRgpd: false,
    modalNormativa: false,
    modalPrl: false,
    readRgpd: false,
    readNormativa: false,
    readPrl: false,
    checkScroll(el, type) {
        if (el.scrollHeight - el.scrollTop <= el.clientHeight + 25) {
            if (type === 'rgpd') this.readRgpd = true;
            if (type === 'normativa') this.readNormativa = true;
            if (type === 'prl') this.readPrl = true;
        }
    }
}">

    <!-- Top Branding Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-800 to-amber-950 p-6 rounded-3xl text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex items-center gap-4 z-10">
            @if(file_exists(public_path('images/utrecar.png')))
                <img src="{{ asset('images/utrecar.png') }}" alt="Logo Utrecar" class="h-12 w-auto bg-white/10 p-2 rounded-2xl backdrop-blur-sm border border-white/10">
            @endif
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-400">Portal del Empleado</span>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    Bienvenida e Inducción
                    <span class="text-xs bg-amber-500/20 text-amber-300 font-bold px-2.5 py-0.5 rounded-full border border-amber-500/30">UTRECAR</span>
                </h1>
            </div>
        </div>
        <div class="text-right z-10 hidden sm:block">
            <span class="text-xs text-slate-400 block font-medium">Empleado/a:</span>
            <span class="text-sm font-bold text-slate-200">{{ auth()->user()->name }}</span>
        </div>
    </div>

    <!-- Multi-step Navigation Bar -->
    <div class="bg-white dark:bg-gray-900 rounded-3xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-white/5">
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-4">
            
            <!-- Step 1 -->
            <button type="button" wire:click="irPaso(1)" class="flex flex-col items-center text-center p-3 rounded-2xl transition-all {{ $paso === 1 ? 'bg-amber-500/10 border-2 border-amber-500 text-amber-600 dark:text-amber-400 shadow-sm' : ($paso > 1 ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 cursor-pointer hover:bg-emerald-500/20' : 'bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-400 opacity-60 cursor-not-allowed') }}">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs mb-1.5 {{ $paso === 1 ? 'bg-amber-500 text-white shadow-md' : ($paso > 1 ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-white/10 text-gray-500') }}">
                    @if($paso > 1) ✓ @else 1 @endif
                </div>
                <span class="text-xs font-bold leading-tight">Quiénes Somos</span>
                <span class="text-[10px] text-gray-400 hidden sm:block">Cultura & Red</span>
            </button>

            <!-- Step 2 -->
            <button type="button" wire:click="irPaso(2)" class="flex flex-col items-center text-center p-3 rounded-2xl transition-all {{ $paso === 2 ? 'bg-amber-500/10 border-2 border-amber-500 text-amber-600 dark:text-amber-400 shadow-sm' : ($paso > 2 ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 cursor-pointer hover:bg-emerald-500/20' : 'bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-400 opacity-60 cursor-not-allowed') }}">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs mb-1.5 {{ $paso === 2 ? 'bg-amber-500 text-white shadow-md' : ($paso > 2 ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-white/10 text-gray-500') }}">
                    @if($paso > 2) ✓ @else 2 @endif
                </div>
                <span class="text-xs font-bold leading-tight">Seguridad</span>
                <span class="text-[10px] text-gray-400 hidden sm:block">Nueva Clave</span>
            </button>

            <!-- Step 3 -->
            <button type="button" wire:click="irPaso(3)" class="flex flex-col items-center text-center p-3 rounded-2xl transition-all {{ $paso === 3 ? 'bg-amber-500/10 border-2 border-amber-500 text-amber-600 dark:text-amber-400 shadow-sm' : ($paso > 3 ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 cursor-pointer hover:bg-emerald-500/20' : 'bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-400 opacity-60 cursor-not-allowed') }}">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs mb-1.5 {{ $paso === 3 ? 'bg-amber-500 text-white shadow-md' : ($paso > 3 ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-white/10 text-gray-500') }}">
                    @if($paso > 3) ✓ @else 3 @endif
                </div>
                <span class="text-xs font-bold leading-tight">Tus Datos</span>
                <span class="text-[10px] text-gray-400 hidden sm:block">IBAN y Contacto</span>
            </button>

            <!-- Step 4 -->
            <button type="button" wire:click="irPaso(4)" class="flex flex-col items-center text-center p-3 rounded-2xl transition-all {{ $paso === 4 ? 'bg-amber-500/10 border-2 border-amber-500 text-amber-600 dark:text-amber-400 shadow-sm' : ($paso > 4 ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 cursor-pointer hover:bg-emerald-500/20' : 'bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-400 opacity-60 cursor-not-allowed') }}">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs mb-1.5 {{ $paso === 4 ? 'bg-amber-500 text-white shadow-md' : ($paso > 4 ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-white/10 text-gray-500') }}">
                    @if($paso > 4) ✓ @else 4 @endif
                </div>
                <span class="text-xs font-bold leading-tight">Documentación</span>
                <span class="text-[10px] text-gray-400 hidden sm:block">DNI & PRL</span>
            </button>

            <!-- Step 5 -->
            <button type="button" wire:click="irPaso(5)" class="col-span-2 sm:col-span-1 flex flex-col items-center text-center p-3 rounded-2xl transition-all {{ $paso === 5 ? 'bg-amber-500/10 border-2 border-amber-500 text-amber-600 dark:text-amber-400 shadow-sm' : ($paso > 5 ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 cursor-pointer hover:bg-emerald-500/20' : 'bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-400 opacity-60 cursor-not-allowed') }}">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs mb-1.5 {{ $paso === 5 ? 'bg-amber-500 text-white shadow-md' : ($paso > 5 ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-white/10 text-gray-500') }}">
                    @if($paso > 5) ✓ @else 5 @endif
                </div>
                <span class="text-xs font-bold leading-tight">Políticas</span>
                <span class="text-[10px] text-gray-400 hidden sm:block">RGPD & Inicio</span>
            </button>

        </div>
    </div>

    @if (session('success_step'))
        <div class="p-4 bg-emerald-500/10 border-2 border-emerald-500/30 rounded-2xl text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2.5 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success_step') }}</span>
        </div>
    @endif

    <!-- Wizard Card Content -->
    <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 sm:p-10 shadow-sm border border-gray-100 dark:border-white/5">

        {{-- ========================================================================= --}}
        {{-- PASO 1: QUIÉNES SOMOS Y CÓMO FUNCIONAMOS --}}
        {{-- ========================================================================= --}}
        @if ($paso === 1)
            <div class="space-y-8 animate-fadeIn">
                <!-- Welcome Title -->
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-extrabold uppercase tracking-wider">
                        👋 ¡Bienvenido/a al Equipo!
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                        Quiénes Somos y Cómo Funcionamos
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        En <strong class="text-gray-900 dark:text-white">Utrecar - Active Network</strong> nos mueve la excelencia, el servicio cercano y el compromiso con cada una de las personas que forman nuestro equipo.
                    </p>
                </div>

                <!-- 4 Operational Pillars Grid -->
                <div>
                    <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">
                        Tu Día a Día en la Empresa (4 Pilares Clave)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Pillar 1: Fichajes -->
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50/50 dark:from-gray-800/60 dark:to-gray-800/30 border border-amber-200/60 dark:border-amber-500/20 space-y-2 hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">1. Registro de Jornada y Fichajes</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                Por normativa legal y organización interna, debes registrar puntualmente tu <strong>Entrada</strong> y <strong>Salida</strong> en cada turno desde el portal del empleado en tu móvil o terminal de la estación.
                            </p>
                        </div>

                        <!-- Pillar 2: Vacaciones -->
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50/50 dark:from-gray-800/60 dark:to-gray-800/30 border border-emerald-200/60 dark:border-emerald-500/20 space-y-2 hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">2. Vacaciones y Solicitudes</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                Podrás solicitar tus días de vacaciones, permisos y ausencias directamente desde la plataforma con antelación, conociendo el estado de aprobación en tiempo real.
                            </p>
                        </div>

                        <!-- Pillar 3: Nóminas -->
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50/50 dark:from-gray-800/60 dark:to-gray-800/30 border border-blue-200/60 dark:border-blue-500/20 space-y-2 hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V9a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2zM9 7V5a2 2 0 012-2h2a2 2 0 012-2v2"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">3. Nóminas y Documentación</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                Tu contrato, recibos de nómina mensuales y certificados estarán siempre disponibles de forma digital y segura en tu expediente para descargarlos cuando lo necesites.
                            </p>
                        </div>

                        <!-- Pillar 4: Seguridad y PRL -->
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50/50 dark:from-gray-800/60 dark:to-gray-800/30 border border-purple-200/60 dark:border-purple-500/20 space-y-2 hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">4. Seguridad, PRL y Soporte</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                Tu seguridad y salud son prioritarias. Dispones de protocolos claros, uso obligatorio de EPIs y comunicación directa con el equipo de Recursos Humanos.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Network Stations Grid -->
                <div>
                    <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">
                        Nuestras Estaciones de Servicio y Centros de Operación
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                            <span class="text-xs font-bold text-gray-900 dark:text-white block">E.S. Vistalegre</span>
                            <span class="text-[10px] text-gray-400">Utrera (Sevilla)</span>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                            <span class="text-xs font-bold text-gray-900 dark:text-white block">Ronda Norte</span>
                            <span class="text-[10px] text-gray-400">Sevilla Capital</span>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                            <span class="text-xs font-bold text-gray-900 dark:text-white block">E.S. Rodalabota</span>
                            <span class="text-[10px] text-gray-400">El Cuervo (Sevilla)</span>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                            <span class="text-xs font-bold text-gray-900 dark:text-white block">E.S. Atenas</span>
                            <span class="text-[10px] text-gray-400">Lebrija (Sevilla)</span>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-6 border-t border-gray-100 dark:border-white/5 flex justify-end">
                    <button type="button" wire:click="completarPaso1Bienvenida" class="inline-flex items-center justify-center px-8 py-3.5 bg-amber-600 hover:bg-amber-700 text-white rounded-2xl text-sm font-bold transition-all shadow-lg hover:shadow-xl gap-2 cursor-pointer">
                        <span>Comenzar Mi Incorporación</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- PASO 2: SEGURIDAD Y CAMBIO DE CONTRASEÑA --}}
        {{-- ========================================================================= --}}
        @if ($paso === 2)
            <div class="space-y-6 animate-fadeIn">
                <div class="border-b border-gray-100 dark:border-white/5 pb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-extrabold">2</span>
                        Seguridad: Cambiar Contraseña Inicial
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Para garantizar la privacidad de tu cuenta, sustituye la contraseña temporal por defecto (1234) por una clave personal de al menos 8 caracteres.
                    </p>
                </div>

                <form wire:submit.prevent="guardarPaso2Password" class="space-y-6 max-w-xl">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Contraseña Actual</label>
                        <input type="password" wire:model="current_password" placeholder="Tu contraseña temporal (1234)" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-amber-500 focus:ring-amber-500 py-2.5 px-3.5 shadow-sm" />
                        @error('current_password') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Nueva Contraseña Personal</label>
                        <input type="password" wire:model.live="new_password" placeholder="Mínimo 8 caracteres" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-amber-500 focus:ring-amber-500 py-2.5 px-3.5 shadow-sm" />
                        <span class="text-xs font-semibold block mt-1.5 {{ strlen($new_password) >= 8 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400' }}">
                            {{ strlen($new_password) >= 8 ? '✓ Cumple el requisito de 8 caracteres' : '• Mínimo 8 caracteres requeridos' }}
                        </span>
                        @error('new_password') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Confirmar Nueva Contraseña</label>
                        <input type="password" wire:model.live="new_password_confirmation" placeholder="Repite la nueva contraseña" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-amber-500 focus:ring-amber-500 py-2.5 px-3.5 shadow-sm" />
                        @if($new_password && $new_password_confirmation)
                            <span class="text-xs font-semibold block mt-1.5 {{ $new_password === $new_password_confirmation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">
                                {{ $new_password === $new_password_confirmation ? '✓ Las contraseñas coinciden' : '✕ Las contraseñas no coinciden' }}
                            </span>
                        @endif
                        @error('new_password_confirmation') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-white/5">
                        <button type="button" wire:click="irPaso(1)" class="px-5 py-2.5 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-xs font-bold transition-all">
                            ← Volver
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center px-7 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg gap-2">
                            <span>Guardar Clave y Continuar</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- PASO 3: TUS DATOS PERSONALES, CONTACTO E IBAN --}}
        {{-- ========================================================================= --}}
        @if ($paso === 3)
            <div class="space-y-6 animate-fadeIn">
                <div class="border-b border-gray-100 dark:border-white/5 pb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-extrabold">3</span>
                        Verificación de Datos Personales, Contacto e IBAN
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Comprueba y completa tus datos para la confección del contrato, alta en Seguridad Social y abono de nóminas.
                    </p>
                </div>

                <form wire:submit.prevent="guardarPaso3Datos" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Nombre *</label>
                            <input type="text" wire:model="nombre" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('nombre') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Apellidos *</label>
                            <input type="text" wire:model="apellidos" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('apellidos') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">DNI / NIE *</label>
                            <input type="text" wire:model="dni" placeholder="12345678Z" class="w-full text-sm font-mono uppercase rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('dni') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Fecha de Nacimiento *</label>
                            <input type="date" wire:model="fecha_nacimiento" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('fecha_nacimiento') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Teléfono de Contacto *</label>
                            <input type="text" wire:model="telefono_principal" placeholder="600000000" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('telefono_principal') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>



                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Cuenta Bancaria (IBAN para cobro de nómina) *</label>
                            <input type="text" wire:model="iban" placeholder="ES00 0000 0000 0000 0000 0000" class="w-full text-sm font-mono uppercase rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('iban') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Dirección Completa *</label>
                            <input type="text" wire:model="direccion" placeholder="Calle, número, piso, puerta..." class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('direccion') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Código Postal *</label>
                            <input type="text" wire:model="codigo_postal" placeholder="41710" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('codigo_postal') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Localidad / Municipio *</label>
                            <input type="text" wire:model="localidad" placeholder="Utrera" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('localidad') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Provincia *</label>
                            <input type="text" wire:model="provincia" placeholder="Sevilla" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('provincia') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Nombre Contacto Emergencia <span class="text-gray-400 font-normal text-[11px]">(Opcional)</span></label>
                            <input type="text" wire:model="contacto_emergencia_nombre" placeholder="Familiar o persona de contacto" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('contacto_emergencia_nombre') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Teléfono Contacto Emergencia <span class="text-gray-400 font-normal text-[11px]">(Opcional)</span></label>
                            <input type="text" wire:model="contacto_emergencia_telefono" placeholder="600000000" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                            @error('contacto_emergencia_telefono') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-white/5">
                        <button type="button" wire:click="irPaso(2)" class="px-5 py-2.5 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-xs font-bold transition-all">
                            ← Volver
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center px-7 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg gap-2">
                            <span>Guardar Datos y Continuar</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- PASO 4: DOCUMENTACIÓN DIGITAL --}}
        {{-- ========================================================================= --}}
        @if ($paso === 4)
            <div class="space-y-6 animate-fadeIn">
                <div class="border-b border-gray-100 dark:border-white/5 pb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-extrabold">4</span>
                        Documentación de Incorporación
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Sube los archivos oficiales requeridos en formato PDF o imagen nítida (JPG, PNG).
                    </p>
                </div>

                <form wire:submit.prevent="guardarPaso4Documentos" class="space-y-6">
                    <div class="space-y-5">
                        
                        <!-- DNI Caducidad y Archivo -->
                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <span>🪪</span> DNI / NIE (Documento Obligatorio)
                                </h3>
                                @if($empleado->documentos()->where('tipo', 'DNI')->exists())
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-full border border-emerald-300 dark:border-emerald-800">
                                        ✓ Ya tienes un DNI adjuntado
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Fecha de Caducidad del DNI *</label>
                                    <input type="date" wire:model="fecha_caducidad_dni" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-2.5 px-3.5 shadow-sm" />
                                    @error('fecha_caducidad_dni') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Archivo DNI (PDF / Foto) *</label>
                                    <label class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 dark:border-white/10 hover:border-amber-500 rounded-xl cursor-pointer bg-white dark:bg-gray-800 transition-all text-xs text-gray-500 hover:text-amber-600">
                                        <svg class="w-6 h-6 mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <span>Seleccionar archivo DNI...</span>
                                        <input type="file" wire:model="file_dni" class="hidden" />
                                    </label>
                                    @if ($file_dni)
                                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold block mt-1">✓ Archivo seleccionado: {{ $file_dni->getClientOriginalName() }}</span>
                                    @endif
                                    @error('file_dni') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Justificante Bancario -->
                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 space-y-3">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span>🏦</span> Certificado de Titularidad Bancaria / Justificante de IBAN (Recomendado)
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Documento del banco o captura de la banca online donde aparezca tu nombre y el número de cuenta IBAN para garantizar los pagos de nómina.
                            </p>
                            <label class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 dark:border-white/10 hover:border-amber-500 rounded-xl cursor-pointer bg-white dark:bg-gray-800 transition-all text-xs text-gray-500 hover:text-amber-600">
                                <span>Seleccionar certificado bancario...</span>
                                <input type="file" wire:model="file_banco" class="hidden" />
                            </label>
                            @if ($file_banco)
                                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold block mt-1">✓ Archivo seleccionado: {{ $file_banco->getClientOriginalName() }}</span>
                            @endif
                        </div>

                        <!-- Formación PRL -->
                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 space-y-3">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span>🦺</span> Formación en Prevención de Riesgos Laborales (PRL)
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Si dispones de cursos previos de PRL (Convenio del sector, 20h, básico 60h, etc.), adjunta aquí tu titulación.
                            </p>
                            <label class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 dark:border-white/10 hover:border-amber-500 rounded-xl cursor-pointer bg-white dark:bg-gray-800 transition-all text-xs text-gray-500 hover:text-amber-600">
                                <span>Seleccionar certificado PRL...</span>
                                <input type="file" wire:model="file_prl" class="hidden" />
                            </label>
                            @if ($file_prl)
                                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold block mt-1">✓ Archivo seleccionado: {{ $file_prl->getClientOriginalName() }}</span>
                            @endif
                        </div>

                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-white/5">
                        <button type="button" wire:click="irPaso(3)" class="px-5 py-2.5 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-xs font-bold transition-all">
                            ← Volver
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center px-7 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg gap-2">
                            <span>Guardar Documentos y Continuar</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- PASO 5: CUMPLIMIENTO NORMATIVO, RGPD Y FINALIZACIÓN --}}
        {{-- ========================================================================= --}}
        @if ($paso === 5)
            <div class="space-y-6 animate-fadeIn">
                <div class="border-b border-gray-100 dark:border-white/5 pb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-extrabold">5</span>
                        Cumplimiento Normativo, RGPD y Bienvenida Oficial
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Lee y confirma la aceptación de las normativas obligatorias para activar definitivamente tu usuario.
                    </p>
                </div>

                <form wire:submit.prevent="finalizarOnboarding" class="space-y-6">
                    <div class="space-y-4">
                        
                        <!-- Check 1: RGPD -->
                        <div class="p-4 rounded-2xl border transition-all cursor-pointer select-none {{ $acepta_rgpd ? 'bg-emerald-50/70 border-emerald-400 dark:bg-emerald-950/30 dark:border-emerald-700/50' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10' }}"
                             wire:click="$toggle('acepta_rgpd')">
                            <div class="flex items-start gap-3.5">
                                <input type="checkbox" wire:model.live="acepta_rgpd" id="onb_rgpd" @click.stop class="mt-1 w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-white/10" />
                                <div class="text-xs space-y-1 flex-1">
                                    <div class="flex items-center justify-between">
                                        <label for="onb_rgpd" class="font-bold text-gray-900 dark:text-white cursor-pointer">
                                            Protección de Datos Personales (RGPD / LOPDGDD)
                                        </label>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $acepta_rgpd ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-gray-200 text-gray-600 dark:bg-white/10 dark:text-gray-400' }}">
                                            {{ $acepta_rgpd ? '✓ Aceptado' : 'Pendiente' }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        Consiento el tratamiento de mis datos personales con fines exclusivamente laborales, de registro de jornada, nóminas y seguridad social por parte de UTRECAR / ACTIVE NETWORK.
                                    </p>
                                    <div>
                                        <button type="button" @click.stop="modalRgpd = true" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                                            Leer normativa completa RGPD
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('acepta_rgpd') <span class="text-xs text-red-500 block px-2 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Check 2: Normativa Interna -->
                        <div class="p-4 rounded-2xl border transition-all cursor-pointer select-none {{ $acepta_normativa ? 'bg-emerald-50/70 border-emerald-400 dark:bg-emerald-950/30 dark:border-emerald-700/50' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10' }}"
                             wire:click="$toggle('acepta_normativa')">
                            <div class="flex items-start gap-3.5">
                                <input type="checkbox" wire:model.live="acepta_normativa" id="onb_normativa" @click.stop class="mt-1 w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-white/10" />
                                <div class="text-xs space-y-1 flex-1">
                                    <div class="flex items-center justify-between">
                                        <label for="onb_normativa" class="font-bold text-gray-900 dark:text-white cursor-pointer">
                                            Normativa Interna y Código de Conducta
                                        </label>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $acepta_normativa ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-gray-200 text-gray-600 dark:bg-white/10 dark:text-gray-400' }}">
                                            {{ $acepta_normativa ? '✓ Aceptado' : 'Pendiente' }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        Me comprometo a respetar las directrices operativas, horarios asignados, obligación de registro horario de jornada en cada turno y políticas de la empresa.
                                    </p>
                                    <div>
                                        <button type="button" @click.stop="modalNormativa = true" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                                            Leer Normativa Interna
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('acepta_normativa') <span class="text-xs text-red-500 block px-2 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Check 3: PRL -->
                        <div class="p-4 rounded-2xl border transition-all cursor-pointer select-none {{ $acepta_prl ? 'bg-emerald-50/70 border-emerald-400 dark:bg-emerald-950/30 dark:border-emerald-700/50' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10' }}"
                             wire:click="$toggle('acepta_prl')">
                            <div class="flex items-start gap-3.5">
                                <input type="checkbox" wire:model.live="acepta_prl" id="onb_prl" @click.stop class="mt-1 w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-white/10" />
                                <div class="text-xs space-y-1 flex-1">
                                    <div class="flex items-center justify-between">
                                        <label for="onb_prl" class="font-bold text-gray-900 dark:text-white cursor-pointer">
                                            Prevención de Riesgos Laborales (PRL) y Seguridad
                                        </label>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $acepta_prl ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-gray-200 text-gray-600 dark:bg-white/10 dark:text-gray-400' }}">
                                            {{ $acepta_prl ? '✓ Aceptado' : 'Pendiente' }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        Confirmo haber recibido las instrucciones de seguridad laboral, uso obligatorio de EPIs y cumplimiento de protocolos de prevención en mi centro de trabajo.
                                    </p>
                                    <div>
                                        <button type="button" @click.stop="modalPrl = true" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                                            Leer Protocolo PRL
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('acepta_prl') <span class="text-xs text-red-500 block px-2 mt-1">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <!-- Celebratory Banner -->
                    <div class="p-6 rounded-3xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-emerald-500/10 border border-amber-500/30 text-center space-y-2">
                        <span class="text-base font-black text-amber-800 dark:text-amber-300 block">✨ ¡Todo preparado para comenzar!</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 max-w-lg mx-auto">
                            Al finalizar, tu expediente quedará guardado y tendrás acceso instantáneo a todas las funcionalidades de tu <strong>Portal del Empleado</strong> (fichajes, vacaciones, nóminas y solicitudes).
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-white/5">
                        <button type="button" wire:click="irPaso(4)" class="px-5 py-2.5 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-xs font-bold transition-all">
                            ← Volver
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-black transition-all shadow-xl hover:shadow-2xl gap-2 cursor-pointer">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Finalizar Onboarding y Acceder al Portal</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

    {{-- ================= MODALES FLOTANTES DE LECTURA ================= --}}
    <!-- Modal RGPD -->
    <template x-teleport="body">
        <div x-show="modalRgpd" class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm" style="display: none;" @click="modalRgpd = false" @keydown.escape.window="modalRgpd = false">
            <div x-show="modalRgpd" class="w-full max-w-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-3xl shadow-2xl space-y-4 text-left" @click.stop>
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-white/10">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>🛡️</span> Protección de Datos Personales (RGPD / LOPDGDD)
                    </h3>
                    <button type="button" @click="modalRgpd = false" class="text-gray-400 hover:text-gray-500 p-1">✕</button>
                </div>
                <div @scroll="checkScroll($el, 'rgpd')" x-init="$nextTick(() => { if ($el.scrollHeight <= $el.clientHeight + 10) readRgpd = true; })" class="space-y-3 max-h-[50vh] overflow-y-auto pr-3 text-xs text-gray-600 dark:text-gray-300 leading-relaxed border border-gray-100 dark:border-white/5 p-4 rounded-2xl bg-gray-50/50 dark:bg-gray-950/30">
                    <p><strong>Responsable del Tratamiento:</strong> UTRECAR S.L. / ACTIVE NETWORK (C.I.F. B-41710000), con domicilio social en Ctra. Écija-Jerez, Km 11, Utrera (Sevilla).</p>
                    <p><strong>Finalidad del Tratamiento:</strong> En cumplimiento del Reglamento General de Protección de Datos (RGPD UE 2016/679) y la Ley Orgánica 3/2018 (LOPDGDD), le informamos que sus datos serán tratados exclusivamente para:
                    <ul class="list-disc pl-5 space-y-1 mt-1">
                        <li>Gestión y mantenimiento de la relación laboral y contractual.</li>
                        <li>Registro diario obligatorio de la jornada de trabajo y control horario (Art. 34.9 Estatuto de los Trabajadores).</li>
                        <li>Confección y abono de nóminas, cotizaciones a la Seguridad Social y retenciones tributarias.</li>
                        <li>Gestión de la prevención de riesgos laborales y vigilancia de la salud laboral.</li>
                    </ul>
                    </p>
                    <p><strong>Legitimación:</strong> Cumplimiento de obligaciones legales aplicables, ejecución del contrato de trabajo e interés legítimo empresarial.</p>
                    <p><strong>Destinatarios:</strong> Sus datos únicamente se comunicarán a organismos públicos oficiales (Seguridad Social, Agencia Tributaria, Ministerio de Trabajo) y entidades bancarias para el abono de salarios.</p>
                    <p><strong>Derechos del Trabajador:</strong> Puede ejercitar sus derechos de acceso, rectificación, supresión, limitación del tratamiento y portabilidad dirigiéndose por escrito al departamento de Recursos Humanos o a través del canal oficial de la empresa.</p>
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 rounded-xl text-amber-800 dark:text-amber-300 font-medium">
                        📜 Fin del documento de Protección de Datos. Al hacer clic en aceptar, confirma haber leído y comprendido íntegramente estas cláusulas.
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-3 border-t border-gray-100 dark:border-white/10">
                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                        <span x-show="!readRgpd" class="text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1">
                            ⚠️ Desplázate hasta el final del texto para habilitar la aceptación
                        </span>
                        <span x-show="readRgpd" class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1" style="display: none;">
                            ✓ Documento completado
                        </span>
                    </div>
                    <button type="button" 
                            x-bind:disabled="!readRgpd"
                            @click="modalRgpd = false; $wire.set('acepta_rgpd', true)" 
                            x-bind:class="readRgpd ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer shadow-md' : 'bg-gray-200 dark:bg-white/10 text-gray-400 cursor-not-allowed'"
                            class="px-5 py-2.5 text-xs font-bold rounded-xl transition-all">
                        He leído y Acepto el RGPD
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Modal Normativa -->
    <template x-teleport="body">
        <div x-show="modalNormativa" class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm" style="display: none;" @click="modalNormativa = false" @keydown.escape.window="modalNormativa = false">
            <div x-show="modalNormativa" class="w-full max-w-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-3xl shadow-2xl space-y-4 text-left" @click.stop>
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-white/10">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>📋</span> Normativa Interna y Código de Conducta
                    </h3>
                    <button type="button" @click="modalNormativa = false" class="text-gray-400 hover:text-gray-500 p-1">✕</button>
                </div>
                <div @scroll="checkScroll($el, 'normativa')" x-init="$nextTick(() => { if ($el.scrollHeight <= $el.clientHeight + 10) readNormativa = true; })" class="space-y-3 max-h-[50vh] overflow-y-auto pr-3 text-xs text-gray-600 dark:text-gray-300 leading-relaxed border border-gray-100 dark:border-white/5 p-4 rounded-2xl bg-gray-50/50 dark:bg-gray-950/30">
                    <p><strong>1. Obligatoriedad del Registro de Jornada:</strong> Cada empleado es responsable único e intransferible de realizar el fichaje de entrada y de salida puntual en cada turno laboral a través de los canales autorizados (Portal Web corporativo o terminales en estación).</p>
                    <p><strong>2. Credenciales y Acceso:</strong> Las credenciales y contraseñas de acceso al sistema informático son de uso estrictamente personal. Queda terminantemente prohibido ceder o compartir las claves de usuario con otros compañeros o terceras personas.</p>
                    <p><strong>3. Uso de Instalaciones y Equipos:</strong> El trabajador se compromete a hacer un uso diligente, responsable y seguro de los surtidores, terminales TPV, sistemas de cobro y demás medios proporcionados por la empresa.</p>
                    <p><strong>4. Comunicación de Incidencias y Solicitudes:</strong> Cualquier baja médica, permiso retribuido o solicitud de vacaciones deberá tramitarse con la debida antelación a través del portal de Recursos Humanos, aportando los justificantes reglamentarios.</p>
                    <p><strong>5. Atención al Cliente e Imagen Corporativa:</strong> En los puestos de cara al público, se mantendrá un trato cordial, respetuoso y profesional, portando el uniforme reglamentario en perfectas condiciones de higiene y seguridad.</p>
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 rounded-xl text-amber-800 dark:text-amber-300 font-medium">
                        📜 Fin del Código de Conducta. Al hacer clic en aceptar, confirma haber leído y aceptado las normas laborales de la empresa.
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-3 border-t border-gray-100 dark:border-white/10">
                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                        <span x-show="!readNormativa" class="text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1">
                            ⚠️ Desplázate hasta el final del texto para habilitar la aceptación
                        </span>
                        <span x-show="readNormativa" class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1" style="display: none;">
                            ✓ Documento completado
                        </span>
                    </div>
                    <button type="button" 
                            x-bind:disabled="!readNormativa"
                            @click="modalNormativa = false; $wire.set('acepta_normativa', true)" 
                            x-bind:class="readNormativa ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer shadow-md' : 'bg-gray-200 dark:bg-white/10 text-gray-400 cursor-not-allowed'"
                            class="px-5 py-2.5 text-xs font-bold rounded-xl transition-all">
                        He leído y Acepto la Normativa Interna
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Modal PRL -->
    <template x-teleport="body">
        <div x-show="modalPrl" class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm" style="display: none;" @click="modalPrl = false" @keydown.escape.window="modalPrl = false">
            <div x-show="modalPrl" class="w-full max-w-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-3xl shadow-2xl space-y-4 text-left" @click.stop>
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-white/10">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>🦺</span> Prevención de Riesgos Laborales (PRL) y Seguridad
                    </h3>
                    <button type="button" @click="modalPrl = false" class="text-gray-400 hover:text-gray-500 p-1">✕</button>
                </div>
                <div @scroll="checkScroll($el, 'prl')" x-init="$nextTick(() => { if ($el.scrollHeight <= $el.clientHeight + 10) readPrl = true; })" class="space-y-3 max-h-[50vh] overflow-y-auto pr-3 text-xs text-gray-600 dark:text-gray-300 leading-relaxed border border-gray-100 dark:border-white/5 p-4 rounded-2xl bg-gray-50/50 dark:bg-gray-950/30">
                    <p><strong>1. Equipos de Protección Individual (EPIs):</strong> Es obligatorio el uso continuo de los EPIs reglamentarios suministrados por la empresa según el puesto (calzado de seguridad con puntera reforzada y suela antideslizante, chaleco reflectante de alta visibilidad, guantes de nitrilo para repostaje/limpieza y gafas protectoras).</p>
                    <p><strong>2. Manipulación Segura de Combustibles:</strong> Cumplir rigurosamente con la prohibición absoluta de fumar, encender fuego o utilizar dispositivos móviles en la zona de pistas y surtidores (zonas ATEX clasificadas con riesgo de atmósfera explosiva).</p>
                    <p><strong>3. Protocolo en caso de Emergencia o Derrame:</strong> Conocer la ubicación de los extintores, paradas de emergencia de los surtidores (setas de corte de corriente) y kit de absorción de derrames de hidrocarburos.</p>
                    <p><strong>4. Ergonomía y Manejo Manual de Cargas:</strong> Aplicar las técnicas ergonómicas adecuadas para la elevación de cargas pesadas en tienda y almacén (flexionar rodillas y mantener la espalda recta).</p>
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 rounded-xl text-amber-800 dark:text-amber-300 font-medium">
                        📜 Fin del Protocolo de Seguridad y PRL. Al hacer clic en aceptar, certifica haber recibido y comprendido las normas de prevención.
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-3 border-t border-gray-100 dark:border-white/10">
                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                        <span x-show="!readPrl" class="text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1">
                            ⚠️ Desplázate hasta el final del texto para habilitar la aceptación
                        </span>
                        <span x-show="readPrl" class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1" style="display: none;">
                            ✓ Documento completado
                        </span>
                    </div>
                    <button type="button" 
                            x-bind:disabled="!readPrl"
                            @click="modalPrl = false; $wire.set('acepta_prl', true)" 
                            x-bind:class="readPrl ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer shadow-md' : 'bg-gray-200 dark:bg-white/10 text-gray-400 cursor-not-allowed'"
                            class="px-5 py-2.5 text-xs font-bold rounded-xl transition-all">
                        He leído y Acepto las Normas de PRL
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
