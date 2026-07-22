<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @php
        $quotes = [
            "Y verás que la vida es hermosa, si te paras a ver cómo crecen las cosas.",
            "Vivir a la deriva, sentir que cada día es el primero.",
            "Dejaré que el viento sople a mi favor, y que me lleve donde quiera, sin buscar explicación.",
            "Me colé por la rendija de tu alma y me quedé a vivir allí.",
            "No quiero saber si el cielo es azul o gris, solo quiero saber si estás aquí."
        ];
        $quoteText = $quotes[array_rand($quotes)];
    @endphp

    <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 text-center shadow-inner">
        <p class="text-sm italic font-medium text-amber-800 dark:text-amber-300">
            "{{ $quoteText }}"
        </p>
        <span class="block text-[10px] uppercase font-bold text-amber-500 dark:text-amber-400 tracking-widest mt-2">
            — Robe (Roberto Iniesta)
        </span>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
