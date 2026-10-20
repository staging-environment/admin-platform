@php
    $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower(auth()->user()->email)])->first();
    $hasAcceptedPolicies = (bool) ($empleado && $empleado->politicas_aceptadas_at);
    $isDefaultPassword = \Illuminate\Support\Facades\Hash::check('1234', auth()->user()->password);
    $necesitaNormativas = $isDefaultPassword && !$hasAcceptedPolicies;
@endphp

<section x-data="{
    pass: '',
    confirmPass: '',
    currentPass: '',
    isDefaultPassword: {{ $isDefaultPassword ? 'true' : 'false' }},
    necesitaNormativas: {{ $necesitaNormativas ? 'true' : 'false' }},
    aceptaRgpd: {{ old('acepta_rgpd') ? 'true' : 'false' }},
    aceptaNormativa: {{ old('acepta_normativa') ? 'true' : 'false' }},
    aceptaPrl: {{ old('acepta_prl') ? 'true' : 'false' }},
    errorMessage: '',
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
    },

    marcarTodas() {
        this.aceptaRgpd = true;
        this.aceptaNormativa = true;
        this.aceptaPrl = true;
        this.errorMessage = '';
    },

    validarYEnviar(e) {
        this.errorMessage = '';

        if (!this.currentPass) {
            this.errorMessage = 'Por favor, introduce tu contraseña actual (o 1234 si es tu primer acceso).';
            e.preventDefault();
            return false;
        }

        if (this.pass.length < 8) {
            this.errorMessage = 'La nueva contraseña debe tener al menos 8 caracteres.';
            e.preventDefault();
            return false;
        }

        if (this.pass !== this.confirmPass) {
            this.errorMessage = 'La confirmación de la contraseña no coincide con la nueva contraseña.';
            e.preventDefault();
            return false;
        }

        if (this.necesitaNormativas && (!this.aceptaRgpd || !this.aceptaNormativa || !this.aceptaPrl)) {
            this.errorMessage = '¡Atención! Para poder guardar la contraseña y acceder a la plataforma, debes marcar las 3 casillas de aceptación de normativas (RGPD, Normativa Interna y PRL). Puedes consultar el texto completo en los enlaces.';
            e.preventDefault();
            return false;
        }

        return true;
    }
}">
    <header>
        <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            {{ __('Actualizar Contraseña') }}
        </h2>

        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ __('Asegúrate de que tu cuenta utilice una contraseña segura de al menos 8 caracteres para mantener tus datos protegidos.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" @submit="validarYEnviar($event)" class="mt-6 space-y-4">
        @csrf
        @method('put')
        @if($necesitaNormativas)
            <input type="hidden" name="check_normativas_present" value="1">
        @endif

        <div>
            <x-input-label for="update_password_current_password" value="Contraseña Actual" />
            <x-text-input id="update_password_current_password" x-model="currentPass" name="current_password" type="password" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" autocomplete="current-password" placeholder="Tu contraseña actual (o 1234)" required />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Nueva Contraseña Personal" />
            <x-text-input id="update_password_password" x-model="pass" name="password" type="password" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" autocomplete="new-password" placeholder="Mínimo 8 caracteres" required />
            <p class="mt-1 text-xs font-semibold flex items-center gap-1 transition-colors duration-200"
               :class="pass.length >= 8 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500'">
                <span x-text="pass.length >= 8 ? '✓' : '•'"></span>
                <span>Mínimo 8 caracteres requeridos.</span>
            </p>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmar Nueva Contraseña" />
            <x-text-input id="update_password_password_confirmation" x-model="confirmPass" name="password_confirmation" type="password" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" autocomplete="new-password" placeholder="Repite la nueva contraseña" required />
            <p class="mt-1 text-xs font-semibold flex items-center gap-1 transition-colors duration-200"
               :class="(confirmPass.length >= 8 && confirmPass === pass) ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500'">
                <span x-text="(confirmPass.length >= 8 && confirmPass === pass) ? '✓' : '•'"></span>
                <span x-text="(confirmPass.length > 0 && confirmPass !== pass) ? 'Las contraseñas no coinciden.' : 'Las contraseñas deben ser idénticas.'"></span>
            </p>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        {{-- SECCIÓN DE NORMATIVAS SOLO SI ES NECESARIO --}}
        @if($necesitaNormativas)
        <div class="pt-4 border-t border-gray-100 dark:border-white/10 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-bold">!</span>
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                        Cumplimiento de RGPD y Normativas de Empresa (Obligatorio)
                    </h3>
                </div>

            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                Para completar la activación de tu cuenta y acceder al portal, haz clic en los enlaces para consultar y aceptar los documentos legales correspondientes:
            </p>

            <div class="space-y-3">
                {{-- 1. RGPD Checkbox y Enlace --}}
                <div class="p-4 rounded-2xl border transition-all"
                     :class="aceptaRgpd ? 'bg-emerald-50/70 border-emerald-400 dark:bg-emerald-950/30 dark:border-emerald-700/50' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10'">
                    <div class="flex items-start gap-3.5">
                        <input type="checkbox" id="acepta_rgpd" name="acepta_rgpd" value="1" x-model="aceptaRgpd" class="mt-1 w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-white/10">
                        <div class="text-xs space-y-1.5 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <label for="acepta_rgpd" class="font-bold text-gray-900 dark:text-white cursor-pointer select-none">
                                    Protección de Datos Personales (RGPD / LOPDGDD)
                                </label>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                      :class="aceptaRgpd ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-gray-200 text-gray-600 dark:bg-white/10 dark:text-gray-400'"
                                      x-text="aceptaRgpd ? '✓ Aceptado' : 'Pendiente'">
                                </span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                He sido informado y consiento el tratamiento de mis datos personales para fines exclusivamente laborales, registro de jornada, nóminas y seguridad social por parte de UTRECAR / ACTIVE NETWORK.
                            </p>
                            <div class="pt-1">
                                <button type="button" @click="modalRgpd = true" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:underline cursor-pointer">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    <span>Leer documento y normativa completa RGPD</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('acepta_rgpd')" class="mt-2" />
                </div>

                {{-- 2. Normativa Interna Checkbox y Enlace --}}
                <div class="p-4 rounded-2xl border transition-all"
                     :class="aceptaNormativa ? 'bg-emerald-50/70 border-emerald-400 dark:bg-emerald-950/30 dark:border-emerald-700/50' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10'">
                    <div class="flex items-start gap-3.5">
                        <input type="checkbox" id="acepta_normativa" name="acepta_normativa" value="1" x-model="aceptaNormativa" class="mt-1 w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-white/10">
                        <div class="text-xs space-y-1.5 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <label for="acepta_normativa" class="font-bold text-gray-900 dark:text-white cursor-pointer select-none">
                                    Normativa Interna y Código de Conducta
                                </label>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                      :class="aceptaNormativa ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-gray-200 text-gray-600 dark:bg-white/10 dark:text-gray-400'"
                                      x-text="aceptaNormativa ? '✓ Aceptado' : 'Pendiente'">
                                </span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Me comprometo a cumplir las directrices operativas, registro de jornada y la <strong>obligación legal de notificar inmediatamente cualquier cambio, retirada de incapacidad o bajada de discapacidad (<33%)</strong> bajo apercibimiento de despido.
                            </p>
                            <div class="pt-1">
                                <button type="button" @click="modalNormativa = true" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:underline cursor-pointer">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    <span>Leer Normativa Interna de Empresa y Conducta</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('acepta_normativa')" class="mt-2" />
                </div>

                {{-- 3. PRL Checkbox y Enlace --}}
                <div class="p-4 rounded-2xl border transition-all"
                     :class="aceptaPrl ? 'bg-emerald-50/70 border-emerald-400 dark:bg-emerald-950/30 dark:border-emerald-700/50' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10'">
                    <div class="flex items-start gap-3.5">
                        <input type="checkbox" id="acepta_prl" name="acepta_prl" value="1" x-model="aceptaPrl" class="mt-1 w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-white/10">
                        <div class="text-xs space-y-1.5 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <label for="acepta_prl" class="font-bold text-gray-900 dark:text-white cursor-pointer select-none">
                                    Prevención de Riesgos Laborales (PRL) y Seguridad
                                </label>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                      :class="aceptaPrl ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-gray-200 text-gray-600 dark:bg-white/10 dark:text-gray-400'"
                                      x-text="aceptaPrl ? '✓ Aceptado' : 'Pendiente'">
                                </span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Confirmo haber recibido las instrucciones de seguridad laboral, uso obligatorio de EPIs y cumplimiento de protocolos de prevención en mi centro de trabajo.
                            </p>
                            <div class="pt-1">
                                <button type="button" @click="modalPrl = true" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:underline cursor-pointer">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    <span>Leer Protocolo de Prevención y Seguridad Laboral</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('acepta_prl')" class="mt-2" />
                </div>
            </div>
        </div>
        @endif

        <div x-show="errorMessage" class="p-3 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-300 text-xs font-bold" style="display: none;">
            <p x-text="errorMessage"></p>
        </div>

        {{-- BOTÓN DE GUARDAR ABAJO DEL TODO --}}
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-white/10">
            <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-600/20 transition-all focus:outline-none">
                Guardar Nueva Contraseña
            </button>
        </div>
    </form>

    {{-- ================= MODALES FLOTANTES DE LECTURA DE POLÍTICAS ================= --}}
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
                    <p><strong>Responsable del Tratamiento:</strong> UTRECAR, S.L. (C.I.F. B-41527250), con domicilio social en C/ Écija-Jerez, Nº 11, 41710, Utrera (Sevilla).</p>
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
                            @click="modalRgpd = false; aceptaRgpd = true" 
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
                    <div class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/40 rounded-xl text-red-900 dark:text-red-200 space-y-1.5 text-[11px] leading-relaxed">
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
                            @click="modalNormativa = false; aceptaNormativa = true" 
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
                            @click="modalPrl = false; aceptaPrl = true" 
                            x-bind:class="readPrl ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer shadow-md' : 'bg-gray-200 dark:bg-white/10 text-gray-400 cursor-not-allowed'"
                            class="px-5 py-2.5 text-xs font-bold rounded-xl transition-all">
                        He leído y Acepto las Normas de PRL
                    </button>
                </div>
            </div>
        </div>
    </template>
</section>
