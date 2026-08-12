<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        @if (session('status') === 'profile-updated')
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-md text-sm font-medium">
                ✅ La información de tu perfil se ha guardado correctamente.
            </div>
        @endif

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="telefono" :value="__('Teléfono')" />
            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono', $user->telefono)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('telefono')" />

            @can('recibir_notificaciones_competencia')
                <div class="mt-4 mb-4">
                    <x-input-label for="telegram_chat_id" :value="__('ID de Telegram')" />
                    <x-text-input id="telegram_chat_id" name="telegram_chat_id" type="text" class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed" :value="$user->telegram_chat_id ?? 'No asociado'" disabled readonly />
                </div>

                <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-md text-sm text-blue-900 space-y-2">
                    <p class="font-semibold flex items-center gap-1">
                        📢 Tienes activo el permiso para recibir alertas de competencia. Sigue estos pasos para configurar las alertas en tu móvil:
                    </p>
                    <ol class="list-decimal list-inside space-y-2 text-blue-800">
                        <li><b>Instala la aplicación de Telegram</b> en tu móvil desde Google Play Store (Android) o App Store (iPhone) si aún no la tienes instalada.</li>
                        <li>Escribe tu número de teléfono móvil en el campo superior y pulsa el botón <b>Save</b> (Guardar).</li>
                        <li>Abre Telegram y busca el bot <b>@utrecar_alertas_bot</b> o pulsa directamente este enlace: <a href="https://t.me/utrecar_alertas_bot" target="_blank" class="underline font-semibold hover:text-blue-950">t.me/utrecar_alertas_bot</a>.</li>
                        <li>Pulsa el botón <b>Iniciar</b> (Start) dentro del bot.</li>
                        <li>Pulsa el botón <b>📱 Compartir Teléfono</b> que aparecerá abajo para verificar tu número.</li>
                    </ol>
                    <p class="text-xs text-blue-700 font-medium">
                        El sistema validará tu contacto y asociará tu cuenta automáticamente para enviarte las alertas al instante.
                    </p>
            @endcan
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
