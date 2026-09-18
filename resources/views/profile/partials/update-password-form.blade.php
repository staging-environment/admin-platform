@php
    $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower(auth()->user()->email)])->first();
    $hasAcceptedPolicies = ($empleado && $empleado->politicas_aceptadas_at) || ($empleado && $empleado->onboarding_completado);
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
            this.errorMessage = '¡Atención! Para poder guardar la contraseña y acceder a la plataforma, debes marcar las 3 casillas de aceptación de normativas (RGPD, Normativa Interna y PRL).';
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

            <div class="space-y-3">
                <div class="p-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl flex items-start gap-3">
                    <input type="checkbox" id="acepta_rgpd" name="acepta_rgpd" value="1" x-model="aceptaRgpd" class="mt-0.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                    <label for="acepta_rgpd" class="text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                        Acepto la <strong>Política de Protección de Datos (RGPD)</strong>.
                    </label>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl flex items-start gap-3">
                    <input type="checkbox" id="acepta_normativa" name="acepta_normativa" value="1" x-model="aceptaNormativa" class="mt-0.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                    <label for="acepta_normativa" class="text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                        Acepto la <strong>Normativa Interna y Código de Conducta</strong>.
                    </label>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl flex items-start gap-3">
                    <input type="checkbox" id="acepta_prl" name="acepta_prl" value="1" x-model="aceptaPrl" class="mt-0.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                    <label for="acepta_prl" class="text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                        Acepto las <strong>Normas de Prevención de Riesgos Laborales (PRL)</strong>.
                    </label>
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
</section>
