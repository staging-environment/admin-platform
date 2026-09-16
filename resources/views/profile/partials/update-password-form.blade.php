<section x-data="{
    pass: '',
    confirmPass: '',
    isDefaultPassword: {{ (auth()->check() && \Illuminate\Support\Facades\Hash::check('1234', auth()->user()->password)) ? 'true' : 'false' }},
    aceptaRgpd: {{ old('acepta_rgpd') ? 'true' : 'false' }},
    aceptaNormativa: {{ old('acepta_normativa') ? 'true' : 'false' }},
    aceptaPrl: {{ old('acepta_prl') ? 'true' : 'false' }},
    modalRgpd: false,
    modalNormativa: false,
    modalPrl: false,
    get canSubmit() {
        if (this.pass.length < 8 || this.confirmPass !== this.pass) {
            return false;
        }
        if (this.isDefaultPassword && (!this.aceptaRgpd || !this.aceptaNormativa || !this.aceptaPrl)) {
            return false;
        }
        return true;
    }
}">
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            {{ __('Actualizar Contraseña') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Asegúrate de que tu cuenta utilice una contraseña segura de al menos 8 caracteres para mantener tus datos protegidos.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')
        <input type="hidden" name="check_normativas_present" value="1">

        <div>
            <x-input-label for="update_password_current_password" value="Contraseña Actual" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" autocomplete="current-password" placeholder="Tu contraseña actual (o 1234 si es primer acceso)" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Nueva Contraseña Personal" />
            <x-text-input id="update_password_password" x-model="pass" name="password" type="password" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" autocomplete="new-password" placeholder="Mínimo 8 caracteres" />
            <p class="mt-1.5 text-xs font-semibold flex items-center gap-1 transition-colors duration-200"
               :class="pass.length >= 8 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500'">
                <span x-text="pass.length >= 8 ? '✓' : '•'"></span>
                <span>Mínimo 8 caracteres requeridos.</span>
            </p>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmar Nueva Contraseña" />
            <x-text-input id="update_password_password_confirmation" x-model="confirmPass" name="password_confirmation" type="password" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-white/10 dark:bg-gray-800" autocomplete="new-password" placeholder="Repite la nueva contraseña" />
            <p class="mt-1.5 text-xs font-semibold flex items-center gap-1 transition-colors duration-200"
               :class="(confirmPass.length >= 8 && confirmPass === pass) ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500'">
                <span x-text="(confirmPass.length >= 8 && confirmPass === pass) ? '✓' : '•'"></span>
                <span x-text="(confirmPass.length > 0 && confirmPass !== pass) ? 'Las contraseñas no coinciden.' : 'Las contraseñas deben ser idénticas.'"></span>
            </p>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- ================= SECCIÓN DE ACEPTACIÓN DE RGPD Y NORMATIVAS ================= --}}
        <div class="pt-4 border-t border-gray-100 dark:border-white/10 space-y-4">
            <div class="flex items-center gap-2">
                <span class="flex items-center justify-center w-5 h-5 rounded-full bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-bold">!</span>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                    Cumplimiento de RGPD y Normativas de Empresa (Obligatorio)
                </h3>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                Para completar la activación de tu usuario y acceder a las herramientas de la plataforma, debes marcar las siguientes casillas de conformidad legal y laboral:
            </p>

            {{-- 1. RGPD Checkbox --}}
            <div class="p-3.5 rounded-xl border transition-all"
                 :class="aceptaRgpd ? 'bg-emerald-50/50 border-emerald-300 dark:bg-emerald-950/20 dark:border-emerald-800/40' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10'">
                <div class="flex items-start gap-3">
                    <input type="checkbox" id="acepta_rgpd" name="acepta_rgpd" x-model="aceptaRgpd" value="1" class="mt-0.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-white/20 dark:bg-gray-800" required />
                    <div class="text-xs space-y-1">
                        <label for="acepta_rgpd" class="font-bold text-gray-900 dark:text-white cursor-pointer select-none">
                            Protección de Datos Personales (RGPD / LOPDGDD)
                        </label>
                        <p class="text-gray-600 dark:text-gray-400">
                            He sido informado y consiento el tratamiento de mis datos personales para fines de gestión laboral, registro de jornada y nóminas por parte de UTRECAR / ACTIVE NETWORK.
                        </p>
                        <div>
                            <button type="button" @click="modalRgpd = true" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Leer normativa completa de Protección de Datos
                            </button>
                        </div>
                    </div>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('acepta_rgpd')" class="mt-2" />
            </div>

            {{-- 2. Normativa Interna Checkbox --}}
            <div class="p-3.5 rounded-xl border transition-all"
                 :class="aceptaNormativa ? 'bg-emerald-50/50 border-emerald-300 dark:bg-emerald-950/20 dark:border-emerald-800/40' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10'">
                <div class="flex items-start gap-3">
                    <input type="checkbox" id="acepta_normativa" name="acepta_normativa" x-model="aceptaNormativa" value="1" class="mt-0.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-white/20 dark:bg-gray-800" required />
                    <div class="text-xs space-y-1">
                        <label for="acepta_normativa" class="font-bold text-gray-900 dark:text-white cursor-pointer select-none">
                            Normativa Interna y Código de Conducta
                        </label>
                        <p class="text-gray-600 dark:text-gray-400">
                            Me comprometo a cumplir las directrices operativas, confidencialidad, horarios asignados y la obligación legal de registro horario de jornada en cada turno.
                        </p>
                        <div>
                            <button type="button" @click="modalNormativa = true" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Leer Normativa Interna de Empresa
                            </button>
                        </div>
                    </div>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('acepta_normativa')" class="mt-2" />
            </div>

            {{-- 3. PRL Checkbox --}}
            <div class="p-3.5 rounded-xl border transition-all"
                 :class="aceptaPrl ? 'bg-emerald-50/50 border-emerald-300 dark:bg-emerald-950/20 dark:border-emerald-800/40' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10'">
                <div class="flex items-start gap-3">
                    <input type="checkbox" id="acepta_prl" name="acepta_prl" x-model="aceptaPrl" value="1" class="mt-0.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-white/20 dark:bg-gray-800" required />
                    <div class="text-xs space-y-1">
                        <label for="acepta_prl" class="font-bold text-gray-900 dark:text-white cursor-pointer select-none">
                            Prevención de Riesgos Laborales (PRL) y Seguridad
                        </label>
                        <p class="text-gray-600 dark:text-gray-400">
                            Confirmo haber recibido las instrucciones de seguridad laboral, uso obligatorio de EPIs y protocolos de emergencia en el puesto de trabajo.
                        </p>
                        <div>
                            <button type="button" @click="modalPrl = true" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Leer Protocolo de Prevención y Seguridad
                            </button>
                        </div>
                    </div>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('acepta_prl')" class="mt-2" />
            </div>
        </div>

        <div class="pt-2">
            <template x-if="!canSubmit && isDefaultPassword">
                <p class="text-xs text-amber-700 dark:text-amber-400 font-medium mb-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Debes indicar una nueva contraseña válida (mínimo 8 caracteres) y marcar los 3 checks de normativas para poder guardar y acceder.
                </p>
            </template>

            <div class="flex items-center gap-4">
                <button
                    type="submit"
                    :disabled="!canSubmit"
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-amber-600 hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl text-xs font-bold transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 gap-2"
                >
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Guardar Contraseña y Aceptar Normativas</span>
                </button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 4000)"
                        class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5"
                    >
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Contraseña actualizada correctamente.
                    </p>
                @endif
            </div>
        </div>
    </form>

    {{-- ================= MODAL FLOTANTE 1: RGPD ================= --}}
    <template x-teleport="body">
        <div x-show="modalRgpd" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm"
             style="display: none;"
             @click="modalRgpd = false"
             @keydown.escape.window="modalRgpd = false">
             
            <div x-show="modalRgpd"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="w-full max-w-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl space-y-4 text-left"
                 @click.stop>
                 
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-white/10">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Política de Protección de Datos Personales (RGPD / LOPDGDD)
                    </h3>
                    <button type="button" @click="modalRgpd = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/30 rounded-xl space-y-1">
                        <strong class="text-amber-800 dark:text-amber-400 block font-bold">Información Básica sobre Protección de Datos:</strong>
                        <p><strong>Responsable:</strong> UTRECAR S.L. / ACTIVE NETWORK</p>
                        <p><strong>Finalidad:</strong> Gestión integral de la relación laboral, confección de nóminas, cotizaciones a la Seguridad Social, control y registro de jornada según el Art. 34.9 del Estatuto de los Trabajadores.</p>
                        <p><strong>Legitimación:</strong> Ejecución del contrato de trabajo y cumplimiento de obligaciones legales aplicables.</p>
                        <p><strong>Destinatarios:</strong> Administraciones Públicas competentes (Seguridad Social, Agencia Tributaria), entidades bancarias para el abono de salarios y entidades de prevención.</p>
                    </div>

                    <h4 class="font-bold text-gray-900 dark:text-white pt-1">1. Tratamiento de Datos Personales</h4>
                    <p>En cumplimiento del Reglamento (UE) 2016/679 (RGPD) y la Ley Orgánica 3/2018 (LOPDGDD), el trabajador queda informado de que los datos facilitados serán incorporados a los sistemas de tratamiento de la empresa para el mantenimiento, desarrollo y control de la relación laboral.</p>

                    <h4 class="font-bold text-gray-900 dark:text-white pt-1">2. Registro de Jornada</h4>
                    <p>Los datos horarios y de ubicación capturados durante los fichajes se emplean de forma proporcional y exclusiva para constatar el cumplimiento de la jornada laboral establecida legalmente.</p>

                    <h4 class="font-bold text-gray-900 dark:text-white pt-1">3. Ejercicio de Derechos</h4>
                    <p>El interesado podrá ejercer sus derechos de acceso, rectificación, supresión, limitación del tratamiento, portabilidad y oposición remitiendo una solicitud por escrito al departamento de Recursos Humanos o a través de los canales oficiales de contacto de la empresa.</p>
                </div>
                
                <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-white/10">
                    <span class="text-[11px] text-gray-400">UTRECAR - Gestión de Recursos Humanos</span>
                    <button type="button" @click="modalRgpd = false; aceptaRgpd = true" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                        He leído y Acepto el RGPD
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ================= MODAL FLOTANTE 2: NORMATIVA INTERNA ================= --}}
    <template x-teleport="body">
        <div x-show="modalNormativa" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm"
             style="display: none;"
             @click="modalNormativa = false"
             @keydown.escape.window="modalNormativa = false">
             
            <div x-show="modalNormativa"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="w-full max-w-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl space-y-4 text-left"
                 @click.stop>
                 
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-white/10">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Normativa Interna y Código de Conducta
                    </h3>
                    <button type="button" @click="modalNormativa = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                    <h4 class="font-bold text-gray-900 dark:text-white">1. Obligación de Registro de Jornada</h4>
                    <p>Es deber inexcusable de todo trabajador registrar puntualmente la hora de entrada y la hora de salida de su puesto a través del portal de fichajes habilitado.</p>

                    <h4 class="font-bold text-gray-900 dark:text-white pt-1">2. Credenciales y Confidencialidad</h4>
                    <p>Las claves de acceso son personales e intransferibles. Queda expresamente prohibido ceder credenciales o fichar en nombre de otros compañeros.</p>

                    <h4 class="font-bold text-gray-900 dark:text-white pt-1">3. Cuidado de Instalaciones y Medios</h4>
                    <p>El personal debe velar por el buen estado de las instalaciones, equipos informáticos, surtidores y herramientas de trabajo asignadas en cada centro.</p>

                    <h4 class="font-bold text-gray-900 dark:text-white pt-1">4. Notificación de Ausencias</h4>
                    <p>Cualquier imprevisto, retraso o solicitud de permiso debe comunicarse a través del portal o directamente al responsable del centro con la antelación debida.</p>
                </div>
                
                <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-white/10">
                    <span class="text-[11px] text-gray-400">UTRECAR - Gestión de Recursos Humanos</span>
                    <button type="button" @click="modalNormativa = false; aceptaNormativa = true" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                        He leído y Acepto la Normativa Interna
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ================= MODAL FLOTANTE 3: PRL ================= --}}
    <template x-teleport="body">
        <div x-show="modalPrl" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm"
             style="display: none;"
             @click="modalPrl = false"
             @keydown.escape.window="modalPrl = false">
             
            <div x-show="modalPrl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="w-full max-w-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl space-y-4 text-left"
                 @click.stop>
                 
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-white/10">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Prevención de Riesgos Laborales (PRL) y Seguridad
                    </h3>
                    <button type="button" @click="modalPrl = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                    <h4 class="font-bold text-gray-900 dark:text-white">1. Uso Obligatorio de Equipos de Protección Individual (EPIs)</h4>
                    <p>El trabajador se compromete a utilizar adecuadamente la ropa de trabajo, calzado de seguridad y elementos de protección suministrados por la empresa según los riesgos de su puesto.</p>

                    <h4 class="font-bold text-gray-900 dark:text-white pt-1">2. Medidas de Seguridad en Estaciones de Servicio</h4>
                    <p>Cumplimiento estricto de las normas relativas a manipulación de combustibles, prohibición de fumar, uso del teléfono móvil en pista y actuación inmediata ante derrames o conatos de incendio.</p>

                    <h4 class="font-bold text-gray-900 dark:text-white pt-1">3. Comunicación de Riesgos</h4>
                    <p>Cualquier anomalía que suponga un riesgo para la salud o seguridad propia o de terceros debe ser comunicada inmediatamente al responsable de seguridad.</p>
                </div>
                
                <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-white/10">
                    <span class="text-[11px] text-gray-400">UTRECAR - Prevención de Riesgos Laborales</span>
                    <button type="button" @click="modalPrl = false; aceptaPrl = true" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                        He leído y Acepto las Normas de PRL
                    </button>
                </div>
            </div>
        </div>
    </template>
</section>
