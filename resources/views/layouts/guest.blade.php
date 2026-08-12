@php
    $now = \Carbon\Carbon::now();
    $year = $now->year;
    // Se activa automáticamente TODOS LOS AÑOS en época de Feria de Utrera (Agosto 10 - Septiembre 10 a las 08:00 AM)
    $esFeriaUtrera = $now->between(
        \Carbon\Carbon::create($year, 8, 10, 0, 0, 0),
        \Carbon\Carbon::create($year, 9, 10, 8, 0, 0)
    );
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('ronda_norte_logo.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if($esFeriaUtrera)
        <style>
            /* Banderitas / Farolillos de Feria Animation */
            .feria-garland {
                display: flex;
                justify-content: space-around;
                align-items: flex-start;
                width: 100%;
                margin-bottom: 0px;
                position: relative;
                z-index: 10;
            }
            .farolillo {
                width: 22px;
                height: 30px;
                border-radius: 10px 10px 14px 14px;
                position: relative;
                animation: swingFarolillo 3s ease-in-out infinite alternate;
                transform-origin: top center;
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            }
            .farolillo::before {
                content: '';
                position: absolute;
                top: -3px;
                left: 50%;
                transform: translateX(-50%);
                width: 8px;
                height: 3px;
                background: #f3f4f6;
                border-radius: 2px;
            }
            .farolillo::after {
                content: '';
                position: absolute;
                bottom: -5px;
                left: 50%;
                transform: translateX(-50%);
                width: 10px;
                height: 5px;
                background: #fbbf24;
                border-radius: 0 0 3px 3px;
            }
            .farolillo-red { background: linear-gradient(135deg, #ef4444, #b91c1c); }
            .farolillo-green { background: linear-gradient(135deg, #10b981, #047857); }
            .farolillo-yellow { background: linear-gradient(135deg, #f59e0b, #b45309); }
            .farolillo-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
            .farolillo-purple { background: linear-gradient(135deg, #a855f7, #6b21a8); }

            .farolillo:nth-child(2n) { animation-delay: 0.4s; }
            .farolillo:nth-child(3n) { animation-delay: 0.8s; }
            .farolillo:nth-child(5n) { animation-delay: 1.2s; }

            @keyframes swingFarolillo {
                0% { transform: rotate(-7deg); }
                100% { transform: rotate(7deg); }
            }

            /* Floating confetti dots background */
            .bg-feria-wrapper {
                position: fixed;
                inset: 0;
                pointer-events: none;
                overflow: hidden;
                z-index: 0;
            }
            .confetti-particle {
                position: absolute;
                border-radius: 50%;
                opacity: 0.5;
                animation: floatUp 8s linear infinite;
            }
            @keyframes floatUp {
                0% { transform: translateY(105vh) scale(0.8) rotate(0deg); opacity: 0; }
                20% { opacity: 0.6; }
                80% { opacity: 0.6; }
                100% { transform: translateY(-10vh) scale(1.2) rotate(360deg); opacity: 0; }
            }
        </style>
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased relative">
        @if($esFeriaUtrera)
        <div class="bg-feria-wrapper">
            <div class="confetti-particle bg-red-400 w-3 h-3" style="left: 8%; animation-duration: 7s; animation-delay: 0s;"></div>
            <div class="confetti-particle bg-amber-400 w-4 h-4" style="left: 22%; animation-duration: 9s; animation-delay: 2s;"></div>
            <div class="confetti-particle bg-emerald-400 w-3 h-3" style="left: 38%; animation-duration: 6.5s; animation-delay: 1s;"></div>
            <div class="confetti-particle bg-sky-400 w-4 h-4" style="left: 55%; animation-duration: 8.5s; animation-delay: 3s;"></div>
            <div class="confetti-particle bg-purple-400 w-3 h-3" style="left: 72%; animation-duration: 10s; animation-delay: 0.5s;"></div>
            <div class="confetti-particle bg-rose-400 w-4 h-4" style="left: 88%; animation-duration: 7.5s; animation-delay: 4s;"></div>
        </div>
        @endif

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 relative z-10">
            <div>
                <a href="/">
                    <x-application-logo style="width: 180px; height: auto;" class="mx-auto drop-shadow-md hover:scale-105 transition-transform duration-300" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg relative">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
