<!DOCTYPE html>
<html lang="es"> {{-- Eliminada la clase "dark" --}}
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Utrecar | Red de Estaciones</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased min-h-screen"> {{-- Cambiado el fondo y el color de texto --}}
<header class="max-w-7xl mx-auto px-6 py-8 flex justify-between items-center border-b border-gray-200"> {{-- Borde más claro --}}
    <div class="flex items-center gap-3">
        <div class="bg-blue-600 text-white px-3 py-1 rounded-xl font-black text-2xl shadow-xl">U</div> {{-- Ajustado el color del logo --}}
        <div>
            <p class="font-bold text-xl tracking-tight leading-none">Utrecar</p>
            <p class="text-[10px] uppercase tracking-[0.3em] text-blue-600 font-bold mt-1">Virtusgesnet Active</p> {{-- Color de texto ajustado --}}
        </div>
    </div>

    <nav>
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="text-xs font-bold border border-blue-500/50 bg-blue-500/10 px-5 py-2.5 rounded-full hover:bg-blue-500 hover:text-white transition-all">IR AL PANEL</a> {{-- Ajustado hover --}}
            @else
                <a href="{{ route('login') }}" class="text-xs font-bold border border-gray-300 px-5 py-2.5 rounded-full hover:bg-gray-200 transition-all">ACCEDER</a> {{-- Ajustado el botón de acceder --}}
            @endauth
        @endif
    </nav>
</header>

{{-- SLIDER DE IMÁGENES DE PRUEBA --}}
<div class="max-w-7xl mx-auto px-6 mt-8 mb-16">
    <div class="relative w-full overflow-hidden rounded-3xl shadow-lg" id="slider-wrapper">
        <div class="flex transition-transform duration-500 ease-in-out" id="slider-container">
            <img src="https://picsum.photos/seed/gasstation1/1200/400" alt="Slider Image 1" class="w-full h-64 object-cover flex-shrink-0">
            <img src="https://picsum.photos/seed/fuelpump2/1200/400" alt="Slider Image 2" class="w-full h-64 object-cover flex-shrink-0">
            <img src="https://picsum.photos/seed/carrefueling3/1200/400" alt="Slider Image 3" class="w-full h-64 object-cover flex-shrink-0">
            <img src="https://picsum.photos/seed/petrolstation4/1200/400" alt="Slider Image 4" class="w-full h-64 object-cover flex-shrink-0">
            <img src="https://picsum.photos/seed/highwayservice5/1200/400" alt="Slider Image 5" class="w-full h-64 object-cover flex-shrink-0">
        </div>

        <!-- Navigation Buttons -->
        <button id="prev-slide" class="absolute top-1/2 left-4 -translate-y-1/2 bg-white hover:bg-gray-200 text-gray-800 p-2 rounded-full shadow-md focus:outline-none z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button id="next-slide" class="absolute top-1/2 right-4 -translate-y-1/2 bg-white hover:bg-gray-200 text-gray-800 p-2 rounded-full shadow-md focus:outline-none z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>
</div>
{{-- FIN SLIDER --}}

<main class="max-w-7xl mx-auto px-6 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($gasolineras as $gasolinera)
            <a href="{{ route('estacion.show', $gasolinera->Codigo) }}"
               class="group block bg-white border border-gray-200 p-8 rounded-[2.5rem] backdrop-blur-xl hover:border-blue-500/50 transition-all duration-500 hover:-translate-y-2"> {{-- Fondo y borde de tarjeta más claros --}}
                <div class="w-12 h-12 bg-blue-500/20 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors"> {{-- Color de icono ajustado --}}
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-1">{{ $gasolinera->Nombre }}</h3>
                <p class="text-[10px] text-gray-600 uppercase tracking-widest font-bold mb-8">{{ $gasolinera->Direccion }}</p> {{-- Color de texto ajustado --}}
                <div class="space-y-4 pt-8 border-t border-gray-200"> {{-- Borde más claro --}}
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-tighter">Diesel</span> {{-- Color de texto ajustado --}}
                        <span class="text-xl font-black text-gray-900">{{ number_format($gasolinera->diesel ?? 0, 3) }}€</span> {{-- Color de texto ajustado --}}
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-tighter">SP 95</span> {{-- Color de texto ajustado --}}
                        <span class="text-xl font-black text-blue-600">{{ number_format($gasolinera->gasolina95 ?? 0, 3) }}€</span> {{-- Color de texto ajustado --}}
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</main>
</body>
</html>
