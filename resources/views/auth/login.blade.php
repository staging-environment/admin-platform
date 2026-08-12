<x-guest-layout>
    @php
        $now = \Carbon\Carbon::now();
        $year = $now->year;
        $esFeriaUtrera = $now->between(
            \Carbon\Carbon::create($year, 8, 10, 0, 0, 0),
            \Carbon\Carbon::create($year, 9, 10, 8, 0, 0)
        );
    @endphp

    @if($esFeriaUtrera)
        <div class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-red-700 via-rose-800 to-amber-700 text-white shadow-lg text-center transform hover:scale-[1.01] transition-all relative overflow-hidden border border-amber-400/40">
            <div class="feria-garland mb-2 pt-1">
                <div class="farolillo farolillo-red"></div>
                <div class="farolillo farolillo-yellow"></div>
                <div class="farolillo farolillo-green"></div>
                <div class="farolillo farolillo-blue"></div>
                <div class="farolillo farolillo-purple"></div>
                <div class="farolillo farolillo-yellow"></div>
                <div class="farolillo farolillo-red"></div>
            </div>
            <h4 class="font-black text-base text-white tracking-wide flex items-center justify-center gap-2 mt-2 drop-shadow-md">
                <span class="text-xl">💃</span>
                <span>¡Feliz Feria de Utrera!</span>
                <span class="text-xl">🎪</span>
            </h4>
            <p class="text-xs font-extrabold text-amber-200 mt-1 drop-shadow-sm">
                ¡Que paséis unos felices días de fiesta! 🍷✨
            </p>
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @php
        $quotes = [
            "Y verás que la vida es hermosa, si te paras a ver cómo crecen las cosas.",
            "Vivir a la deriva, sentir que cada día es el primero.",
            "Dejaré que el viento sople a mi favor, y que me lleve donde quiera, sin buscar explicación.",
            "Me colé por la rendija de tu alma y me quedé a vivir allí.",
            "No quiero saber si el cielo es azul o gris, solo quiero saber si estás aquí.",
            "Me pongo de puntillas y me asomo al tejado a ver pasar las nubes que tú has dibujado.",
            "Que no me da la gana pasar media vida buscando tu olor, que no me da la gana vivir en un mundo que no tenga color.",
            "A mí me gusta el viento, no sé por qué, pero me limpia la cabeza.",
            "Si fuera mi vida una sola canción, la cantaría contigo de principio a fin.",
            "Hoy es el día más hermoso de nuestra vida, el mañana no existe y el ayer ya pasó.",
            "Y, si cae la lluvia, que nos moje la piel; y, si sopla el viento, que nos lleve con él.",
            "Quiero ser tu noche y tu día, tu alegría y tu tristeza, tu sol y tu luna.",
            "Busco el camino que lleva al olvido, y me pierdo en tus ojos.",
            "No me importan los mapas si el destino eres tú.",
            "Y en la frontera del bien y del mal, me quedo contigo a ver qué pasa.",
            "Quiero que me hables de ti, del color de tus sueños, de lo que te hace feliz.",
            "Buscando mi destino, viviendo en diferido, sin saber dónde voy ni de dónde he venido.",
            "Si tú me miras, me lleno de luz; si tú me tocas, me lleno de vida.",
            "Y me rebelo contra el tiempo que pasa y nos roba la juventud.",
            "Que la vida es muy corta para vivirla con miedo."
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
