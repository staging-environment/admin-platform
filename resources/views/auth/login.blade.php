<x-guest-layout>
    @php
        $esFeriaUtrera = (function() {
            $now = \Carbon\Carbon::now();
            $year = $now->year;

            $sept8 = \Carbon\Carbon::create($year, 9, 8, 0, 0, 0);
            $startFeria = \Carbon\Carbon::create($year, 9, 4, 20, 0, 0);
            $endFeria = $sept8->copy()->endOfWeek()->addDay()->setTime(8, 0, 0);

            return $now->between($startFeria, $endFeria);
        })();
    @endphp

    @if($esFeriaUtrera)
        <div class="mb-5 p-4 rounded-2xl text-white shadow-lg text-center transform hover:scale-[1.01] transition-all relative overflow-hidden" style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 50%, #b45309 100%) !important; border: 1.5px solid #f59e0b !important;">
            <div class="feria-garland mb-2 pt-1">
                <div class="farolillo farolillo-red"></div>
                <div class="farolillo farolillo-yellow"></div>
                <div class="farolillo farolillo-green"></div>
                <div class="farolillo farolillo-blue"></div>
                <div class="farolillo farolillo-purple"></div>
                <div class="farolillo farolillo-yellow"></div>
                <div class="farolillo farolillo-red"></div>
            </div>
            <h4 class="font-black text-base tracking-wide flex items-center justify-center gap-2 mt-2" style="color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.6);">
                <span class="text-xl">💃</span>
                <span>¡Feliz Feria de Utrera!</span>
                <span class="text-xl">🎪</span>
            </h4>
            <p class="text-xs font-extrabold mt-1" style="color: #fef08a !important; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                ¡Que paséis unos felices días de fiesta! 🍷✨
            </p>
        </div>
    @else
        @php
            $jokes = [
                "— ¿Cuánto le pongo, jefe? — Échale 10 euros de gasolina y 50 de fe a ver si llego a fin de mes.",
                "— Buenas, ¿me revisa el aceite y la presión de las ruedas? — Señor, esto es un túnel de lavado, salga del coche por favor.",
                "— ¿Tiene cambio de 50€? — Sí, claro. — Pues póngame 5€ de diésel y deme los 45€ que tengo que hacer la compra.",
                "Regla de oro del gasolinero: el cliente que pide 'llénelo' siempre aparca en el lado contrario al del depósito.",
                "— ¿Gasolina 95 o 98? — La que esté más barata, que el coche tiene sed pero yo tengo hipoteca.",
                "— Buenas, ¿me pone 2 euros de gasolina? — ¿Qué pasa, que el mechero no le enciende?",
                "Ese momento mágico en el que intentas clavar el importe exacto en el surtidor y pasa de 19,99€ a 20,01€... Tragedia nacional.",
                "— Oiga, ¿este túnel de lavado encoge los coches? — No, ¿por qué? — Porque entré con un monovolumen y he salido con un Twingo.",
                "— ¿Por qué los gasolineros son tan sabios? — Porque manejan los niveles de presión de todo el barrio.",
                "— Jefe, ¿me limpia el parabrisas? — Pero si viene usted en moto... — Bueno, pues las gafas, no te pongas tiquismiquis.",
                "— ¿Me mira la presión de las ruedas? — Claro... veo que están bajo mucha presión, igual necesitan terapia.",
                "— Buenas, ¿el baño está libre? — Sí, pero la llave está atada a una llanta de camión de 40 kilos por seguridad.",
                "— ¿Qué hace un pistero cuando se aburre? — Contar cuántos conductores intentan estirar la manguera hasta el otro lado del coche.",
                "— Póngame 20 euros de diésel. — ¿Le cobro con tarjeta o con lágrimas?",
                "— Oiga, ¿la gasolina sube o baja? — Subir sube siempre, lo que baja es mi paciencia en el turno de noche.",
                "— ¿Le miro el agua del limpiaparabrisas? — No gracias, si llueve saco la cabeza por la ventanilla.",
                "— Buenas, ¿acepta tarjeta de puntos? — Sí, pero con los puntos que tiene le llega para un ambientador de pino y una servilleta.",
                "— ¿Por qué vino en grúa si la gasolinera estaba a 100 metros? — Por confiar en la luz de la reserva hasta el último aliento.",
                "— ¿Me pone 10€ de 95? — ¿Para llevar o se la bebe aquí?",
                "El superpoder del empleado de gasolinera: adivinar a la primera cuál es 'el coche gris del fondo'."
            ];
            $jokeText = $jokes[array_rand($jokes)];
        @endphp

        <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 text-center shadow-inner">
            <p class="text-sm italic font-medium text-amber-800 dark:text-amber-300">
                "{{ $jokeText }}"
            </p>
            <span class="block text-[10px] uppercase font-bold text-amber-500 dark:text-amber-400 tracking-widest mt-2">
                — Humor de Gasolinera ⛽
            </span>
        </div>
    @endif

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
