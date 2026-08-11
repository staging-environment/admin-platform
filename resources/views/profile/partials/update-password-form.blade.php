<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6" x-data="{ pass: '', confirmPass: '' }">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" x-model="pass" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <p class="mt-1 text-xs font-semibold flex items-center gap-1 transition-colors duration-200"
               :class="pass.length >= 8 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400'">
                <span x-text="pass.length >= 8 ? '✓' : '✕'"></span>
                <span>Mínimo 8 caracteres.</span>
            </p>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" x-model="confirmPass" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <p class="mt-1 text-xs font-semibold flex items-center gap-1 transition-colors duration-200"
               :class="(confirmPass.length >= 8 && confirmPass === pass) ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400'">
                <span x-text="(confirmPass.length >= 8 && confirmPass === pass) ? '✓' : '✕'"></span>
                <span x-text="(confirmPass.length > 0 && confirmPass !== pass) ? 'Las contraseñas deben coincidir (mínimo 8 caracteres).' : 'Mínimo 8 caracteres.'"></span>
            </p>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

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
    </form>
</section>
