<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Utrecar | Red de Estaciones de Servicio</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('ronda_norte_logo.svg') }}">
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS para los mapas -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .text-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .map-container .leaflet-container {
            font-family: 'Outfit', sans-serif;
        }
        /* Ocultar barra de scroll en nav de pestañas */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        [x-cloak] { display: none !important; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
    </style>
</head>
<body class="bg-slate-50/50 text-slate-800 antialiased min-h-screen relative overflow-x-hidden" x-data="{ tab: (window.location.pathname === '/' ? ((new URLSearchParams(window.location.search)).get('tab') === 'contacto' ? 'inicio' : ((new URLSearchParams(window.location.search)).get('tab') || 'inicio')) : ''), showTerms: false, showLegal: false, showPrivacy: false }" x-effect="if (tab === 'contacto') { setTimeout(() => { if (window.globalMapInstance) { window.globalMapInstance.invalidateSize(); } }, 200); }" @keydown.window.escape="showTerms = false; showLegal = false; showPrivacy = false;">

    <!-- Glowing background orbs for modern depth -->
    <div class="absolute top-[40vh] left-1/2 -translate-x-1/2 w-full max-w-7xl h-[120vh] pointer-events-none overflow-hidden z-0 opacity-40">
        <div class="absolute top-10 left-10 w-[500px] h-[500px] bg-blue-400/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-10 right-10 w-[500px] h-[500px] bg-cyan-400/10 rounded-full blur-[120px]"></div>
    </div>

    @php
        $sliderImages = [];
        if ($homeConfig && !empty($homeConfig->slider_images)) {
            $images = is_string($homeConfig->slider_images) ? json_decode($homeConfig->slider_images, true) : $homeConfig->slider_images;
            if (is_array($images) && count($images) > 0) {
                $images = array_values($images);
                foreach ($images as $img) {
                    $sliderImages[] = Storage::disk('public')->url($img);
                }
                $sliderImages = array_reverse($sliderImages);
            }
        }
        
        // Fallback images if no sliders are uploaded yet in configuration page
        if (empty($sliderImages)) {
            $sliderImages = [
                'https://images.unsplash.com/photo-1545084920-56ef6d1a5e95?auto=format&fit=crop&q=80&w=2000',
                'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?auto=format&fit=crop&q=80&w=2000'
            ];
        }
    @endphp

    <style>
        .slider-container-dynamic {
            width: 100%;
            aspect-ratio: 3.5 / 1;
        }
        @media (max-width: 767px) {
            .slider-container-dynamic {
                height: 45vh !important;
                min-height: 300px !important;
                aspect-ratio: auto !important;
            }
        }
    </style>

    <!-- Cinematic Alpine.js Header Slider -->
    <div class="relative w-full overflow-hidden bg-slate-900 slider-container-dynamic" 
         x-data="{ activeSlide: 0, slides: {{ json_encode($sliderImages) }} }" 
         x-init="setInterval(() => { activeSlide = activeSlide === slides.length - 1 ? 0 : activeSlide + 1 }, 10000)">
        
        @foreach ($sliderImages as $index => $slide)
            <div x-show="activeSlide === {{ $index }}" 
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 transform scale-105"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-1000"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-105"
                 class="absolute inset-0 w-full h-full"
                 style="display: {{ $index === 0 ? 'block' : 'none' }};"
                 :class="activeSlide === {{ $index }} ? 'z-10 pointer-events-auto' : 'z-0 pointer-events-none'">
                <img src="{{ $slide }}" class="w-full h-full object-cover object-center" alt="Slider image">
                <!-- Soft gradient for branding overlay readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/10 to-transparent pointer-events-none"></div>
            </div>
        @endforeach
        
        <!-- Navbar Overlay -->
        <header class="absolute top-0 left-0 right-0 z-20 max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="flex items-center gap-3 bg-slate-950/20 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/10">
                <img src="{{ asset('ronda_norte_logo.svg') }}" class="w-9 h-9 object-contain" alt="Utrecar" />
                <div>
                    <p class="font-black text-sm tracking-tight leading-none text-white">Utrecar</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                        <p class="text-[7px] uppercase tracking-[0.2em] text-emerald-400 font-extrabold">Active Network</p>
                    </div>
                </div>
            </div>
            
            <nav class="flex gap-3 items-center">
                @if (Route::has('login'))
                    @auth
@if(optional(auth()->user())->email === 'jarodriguezbonilla@gmail.com' || optional(auth()->user())->id === 1 || optional(auth()->user())->hasRole('Admin') || optional(auth()->user())->can('gestion_portada'))
                            <a href="{{ url('/admin/manage-home') }}" class="text-xs font-bold bg-white/10 text-white backdrop-blur-md border border-white/20 px-5 py-2.5 rounded-full hover:bg-white hover:text-slate-800 transition-all duration-300 shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                EDITAR PORTADA
                            </a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="text-xs font-bold bg-white/95 text-slate-800 backdrop-blur-md px-5 py-2.5 rounded-full hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-lg">
                            IR AL PANEL
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-bold bg-white/10 text-white backdrop-blur-md border border-white/20 px-5 py-2.5 rounded-full hover:bg-white hover:text-slate-800 transition-all duration-300 shadow-lg">
                            ACCEDER
                        </a>
                    @endauth
                @endif
                        <a href="{{ route('offers.index') }}" class="ml-3 text-xs font-bold bg-gradient-to-r from-purple-600 to-pink-600 text-white backdrop-blur-md px-5 py-2.5 rounded-full hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg">Ver Ofertas de Trabajo</a>
            </nav>
        </header>

        <!-- Left Navigation Arrow (Inside Slider) -->
        <template x-if="slides.length > 1">
            <button @click="activeSlide = activeSlide === 0 ? slides.length - 1 : activeSlide - 1" 
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-30 bg-slate-950/60 hover:bg-blue-600 text-white p-2.5 md:p-3 rounded-full shadow-2xl transition-all duration-300 transform active:scale-90 hover:scale-110 focus:outline-none flex items-center justify-center pointer-events-auto border border-white/10"
                    title="Anterior">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </template>

        <!-- Right Navigation Arrow (Inside Slider) -->
        <template x-if="slides.length > 1">
            <button @click="activeSlide = activeSlide === slides.length - 1 ? 0 : activeSlide + 1" 
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-30 bg-slate-950/60 hover:bg-blue-600 text-white p-2.5 md:p-3 rounded-full shadow-2xl transition-all duration-300 transform active:scale-90 hover:scale-110 focus:outline-none flex items-center justify-center pointer-events-auto border border-white/10"
                    title="Siguiente">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </template>

        <!-- Title Card Overlay (Inside Slider) -->
        <div class="absolute left-0 right-0 z-20 max-w-7xl mx-auto px-6 pointer-events-none" style="bottom: 24px;">
            <div class="inline-block border border-white/15 p-3.5 md:p-5 rounded-2xl shadow-2xl max-w-xl pointer-events-auto" style="background-color: rgba(2, 6, 23, 0.75); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);">
                <span class="px-2 py-0.5 bg-blue-500/25 backdrop-blur-md border border-blue-400/30 rounded-full text-[9px] font-bold text-blue-300 tracking-wider uppercase">
                    Nuestra Red
                </span>
                <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-white drop-shadow-2xl mt-2 mb-1 leading-tight">
                    {{ $homeConfig->titulo ?? 'Red de Estaciones de Servicio' }}
                </h1>
                <p class="text-xs md:text-sm text-slate-300 font-medium drop-shadow-md leading-relaxed">
                    {{ $homeConfig->subtitulo ?? 'Precios en tiempo real y servicios premium en carretera. Consulta los combustibles de cada estación and planifica tu ruta.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs Bar Below Slider -->
    <div class="sticky top-0 z-40 bg-white/75 backdrop-blur-xl border-b border-slate-200/40 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.015)] mb-12 py-3.5">
        <div class="max-w-5xl mx-auto px-6">
            <div class="bg-slate-100/80 p-1 rounded-2xl inline-flex gap-1.5 relative overflow-hidden border border-slate-200/40 shadow-inner">
                <button @click="window.location.pathname === '/' ? tab = 'inicio' : window.location.href = '/?tab=inicio'" 
                        :class="tab === 'inicio' ? 'bg-white text-blue-600 shadow-sm border-slate-200/30 font-extrabold' : 'text-slate-500 hover:text-slate-800 font-bold'" 
                        class="whitespace-nowrap py-2 px-5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 outline-none border border-transparent">
                    Inicio
                </button>
                <button @click="window.location.pathname === '/' ? tab = 'gasolineras' : window.location.href = '/?tab=gasolineras'" 
                        :class="tab === 'gasolineras' ? 'bg-white text-blue-600 shadow-sm border-slate-200/30 font-extrabold' : 'text-slate-500 hover:text-slate-800 font-bold'" 
                        class="whitespace-nowrap py-2 px-5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 outline-none border border-transparent">
                    Gasolineras
                </button>
                <button @click="window.location.pathname === '/' ? tab = 'quienes_somos' : window.location.href = '/?tab=quienes_somos'" 
                        :class="tab === 'quienes_somos' ? 'bg-white text-blue-600 shadow-sm border-slate-200/30 font-extrabold' : 'text-slate-500 hover:text-slate-800 font-bold'" 
                        class="whitespace-nowrap py-2 px-5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 outline-none border border-transparent">
                    Quiénes Somos
                </button>
                {{-- 
                <button @click="window.location.pathname === '/' ? tab = 'contacto' : window.location.href = '/?tab=contacto'" 
                        :class="tab === 'contacto' ? 'bg-white text-blue-600 shadow-sm border-slate-200/30 font-extrabold' : 'text-slate-500 hover:text-slate-800 font-bold'" 
                        class="whitespace-nowrap py-2 px-5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 outline-none border border-transparent">
                    Contacto
                </button>
                --}}
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <main class="max-w-5xl mx-auto px-6 pb-24">
        {!! $slot !!}
    </main>

    <!-- Public Footer -->
    <footer class="bg-slate-900 text-slate-300 mt-16 relative z-10 border-t border-slate-800">
        <div class="max-w-5xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: Brand & Logo -->
                <div class="space-y-4 md:col-span-2">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('ronda_norte_logo.svg') }}" class="w-9 h-9 object-contain" alt="Utrecar" />
                        <div>
                            <p class="font-black text-sm tracking-tight leading-none text-white">Utrecar</p>
                            <div class="flex items-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                <p class="text-[7px] uppercase tracking-[0.2em] text-blue-400 font-extrabold">Red de Estaciones</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        Tu red de confianza para repostar al mejor precio con servicios de alta calidad. Precios actualizados en tiempo real y atención al cliente premium en carretera.
                    </p>
                </div>

                <!-- Col 2: Navigation Links -->
                <div>
                    <h4 class="text-xs font-extrabold text-white uppercase tracking-widest mb-4">Navegación</h4>
                    <ul class="space-y-2.5">
                        <li>
                            <a href="#" @click.prevent="window.location.pathname === '/' ? (tab = 'inicio', window.scrollTo({top: 0, behavior: 'smooth'})) : window.location.href = '/?tab=inicio'" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Inicio</a>
                        </li>
                        <li>
                            <a href="#" @click.prevent="window.location.pathname === '/' ? (tab = 'gasolineras', window.scrollTo({top: 0, behavior: 'smooth'})) : window.location.href = '/?tab=gasolineras'" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Gasolineras</a>
                        </li>
                        <li>
                            <a href="#" @click.prevent="window.location.pathname === '/' ? (tab = 'quienes_somos', window.scrollTo({top: 0, behavior: 'smooth'})) : window.location.href = '/?tab=quienes_somos'" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Quiénes Somos</a>
                        </li>
                        {{-- 
                        <li>
                            <a href="#" @click.prevent="window.location.pathname === '/' ? (tab = 'contacto', window.scrollTo({top: 0, behavior: 'smooth'})) : window.location.href = '/?tab=contacto'" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Contacto</a>
                        </li>
                        --}}
                    </ul>
                </div>

                <!-- Col 3: Legal & Support -->
                <div>
                    <h4 class="text-xs font-extrabold text-white uppercase tracking-widest mb-4">Información Legal</h4>
                    <ul class="space-y-2.5">
                        <li>
                            <a href="#" @click.prevent="showTerms = true" class="text-xs text-blue-400 hover:text-blue-300 transition-colors font-bold flex items-center gap-1">
                                <span class="w-1 h-1 bg-blue-500 rounded-full"></span>
                                Condiciones de Uso
                            </a>
                        </li>
                        <li>
                            <a href="#" @click.prevent="showLegal = true" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Aviso Legal</a>
                        </li>
                        <li>
                            <a href="#" @click.prevent="showPrivacy = true" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Política de Privacidad</a>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="border-slate-800 my-8">

            <!-- Bottom bar -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left">
                <p class="text-xs text-slate-500">
                    &copy; {{ date('Y') }} Utrecar. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js Modales Legales -->
    <!-- Modal: Condiciones de Uso -->
    <div x-show="showTerms" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="showTerms" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" 
                 @click="showTerms = false"></div>

            <!-- Align modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="showTerms" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-slate-100 sm:p-8">
                
                <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Condiciones de Uso
                    </h3>
                    <button @click="showTerms = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="prose max-w-none text-sm text-slate-600 leading-relaxed overflow-y-auto max-h-[60vh] pr-2">
                    @if($homeConfig && $homeConfig->condiciones_uso)
                        {!! $homeConfig->condiciones_uso !!}
                    @else
                        <p class="text-slate-500 italic">No se han configurado los términos y condiciones de uso.</p>
                    @endif
                </div>

                <div class="mt-8 pt-4 border-t border-slate-100 flex justify-end">
                    <button @click="showTerms = false" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-blue-500/20 transition-all uppercase tracking-wider">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Aviso Legal -->
    <div x-show="showLegal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="showLegal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" 
                 @click="showLegal = false"></div>

            <!-- Align modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="showLegal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-slate-100 sm:p-8">
                
                <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                        Aviso Legal
                    </h3>
                    <button @click="showLegal = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="prose max-w-none text-sm text-slate-600 leading-relaxed overflow-y-auto max-h-[60vh] pr-2">
                    @if($homeConfig && $homeConfig->aviso_legal)
                        {!! $homeConfig->aviso_legal !!}
                    @else
                        <p class="font-bold mb-2">1. Información Legal</p>
                        <p class="mb-4">En cumplimiento del artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y Comercio Electrónico (LSSI-CE), se informa que este sitio web es operado por Utrecar S.L., con domicilio social en la sede corporativa provista en la sección de contacto.</p>
                        
                        <p class="font-bold mb-2">2. Propiedad Intelectual</p>
                        <p class="mb-4">Todos los contenidos de este sitio web, incluyendo textos, gráficos, imágenes, su diseño y los derechos de propiedad intelectual que pudieran corresponder a dichos contenidos, así como todas las marcas, nombres comerciales o cualquier otro signo distintivo son propiedad de Utrecar o de sus legítimos propietarios, quedando reservados todos los derechos sobre los mismos.</p>
                        
                        <p class="font-bold mb-2">3. Exclusión de Responsabilidad</p>
                        <p class="mb-4">Los precios de carburantes y la información sobre las estaciones de servicio se muestran únicamente con carácter informativo. Aunque nos esforzamos por mantener la información actualizada, no nos responsabilizamos de posibles discrepancias en los precios o disponibilidad de servicios en el momento del repostaje.</p>
                    @endif
                </div>

                <div class="mt-8 pt-4 border-t border-slate-100 flex justify-end">
                    <button @click="showLegal = false" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-blue-500/20 transition-all uppercase tracking-wider">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Política de Privacidad -->
    <div x-show="showPrivacy" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="showPrivacy" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" 
                 @click="showPrivacy = false"></div>

            <!-- Align modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="showPrivacy" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-slate-100 sm:p-8">
                
                <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Política de Privacidad
                    </h3>
                    <button @click="showPrivacy = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="prose max-w-none text-sm text-slate-600 leading-relaxed overflow-y-auto max-h-[60vh] pr-2">
                    @if($homeConfig && $homeConfig->politica_privacidad)
                        {!! $homeConfig->politica_privacidad !!}
                    @else
                        <p class="font-bold mb-2">1. Responsable del Tratamiento</p>
                        <p class="mb-4">Utrecar S.L. es el responsable de sus datos personales. Nos comprometemos a proteger su privacidad de acuerdo con el Reglamento General de Protección de Datos (RGPD) y la legislación local vigente.</p>
                        
                        <p class="font-bold mb-2">2. Datos que Recopilamos</p>
                        <p class="mb-4">A través del formulario de contacto público recopilamos su nombre, dirección de correo electrónico y el contenido de su mensaje con el único fin de responder a su consulta.</p>
                        
                        <p class="font-bold mb-2">3. Conservación y Seguridad</p>
                        <p class="mb-4">Sus datos personales se conservarán únicamente durante el tiempo necesario para resolver su consulta y no serán compartidos con terceros sin su consentimiento expreso, salvo obligación legal. Implementamos medidas de seguridad técnicas y organizativas para proteger sus datos contra accesos no autorizados.</p>
                    @endif
                </div>

                <div class="mt-8 pt-4 border-t border-slate-100 flex justify-end">
                    <button @click="showPrivacy = false" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-blue-500/20 transition-all uppercase tracking-wider">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @foreach($gasolineras as $gasolinera)
                @if($gasolinera->contenido && $gasolinera->contenido->latitud && $gasolinera->contenido->longitud)
                    try {
                        const mapId = 'map-{{ $gasolinera->Codigo }}';
                        const lat = {{ $gasolinera->contenido->latitud }};
                        const lng = {{ $gasolinera->contenido->longitud }};
                        const nombre = {!! json_encode($gasolinera->Nombre) !!};
                        const direccion = {!! json_encode(($gasolinera->contenido && $gasolinera->contenido->donde_estamos_texto) ? $gasolinera->contenido->donde_estamos_texto : $gasolinera->Direccion) !!};

                        const map = L.map(mapId, {
                            zoomControl: false,
                            attributionControl: false
                        }).setView([lat, lng], 15);

                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            tileSize: 512,
                            zoomOffset: -1,
                            detectRetina: true
                        }).addTo(map);

                        const markerColor = '#ef4444';
                        const markerHtml = `<svg xmlns="http://www.w3.org/2000/svg" fill="${markerColor}" width="36" height="36" viewBox="0 0 24 24" style="stroke: #dbeafe; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg>`;
                        const customIcon = L.divIcon({
                            html: markerHtml,
                            className: '',
                            iconSize: [36, 36],
                            iconAnchor: [18, 36]
                        });

                        const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
                        marker.bindPopup(`<b style="font-family: 'Outfit', sans-serif; color: #0f172a; display: block; margin-bottom: 2px;">${nombre}</b><span style="font-family: 'Outfit', sans-serif; font-size: 11px; color: #64748b;">${direccion}</span>`);
                    } catch (e) {
                        console.error("Error al inicializar el mapa para: " + mapId, e);
                    }
                @endif
            @endforeach

            // Global Map Initialization
            @if($homeConfig && $homeConfig->latitud && $homeConfig->longitud)
                try {
                    const globalLat = {{ $homeConfig->latitud }};
                    const globalLng = {{ $homeConfig->longitud }};
                    const globalNombre = "Oficinas Centrales";
                    const globalDireccion = {!! json_encode($homeConfig->contacto_direccion) !!};

                    window.globalMapInstance = L.map('map-global', {
                        zoomControl: true,
                        attributionControl: false
                    }).setView([globalLat, globalLng], 15);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        tileSize: 512,
                        zoomOffset: -1,
                        detectRetina: true
                    }).addTo(window.globalMapInstance);

                    const globalMarkerColor = '#ef4444';
                    const globalMarkerHtml = `<svg xmlns="http://www.w3.org/2000/svg" fill="${globalMarkerColor}" width="36" height="36" viewBox="0 0 24 24" style="stroke: #dbeafe; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg>`;
                    const globalCustomIcon = L.divIcon({
                        html: globalMarkerHtml,
                        className: '',
                        iconSize: [36, 36],
                        iconAnchor: [18, 36]
                    });

                    const globalMarker = L.marker([globalLat, globalLng], { icon: globalCustomIcon }).addTo(window.globalMapInstance);
                    globalMarker.bindPopup(`<b style="font-family: 'Outfit', sans-serif; color: #0f172a; display: block; margin-bottom: 2px;">${globalNombre}</b><span style="font-family: 'Outfit', sans-serif; font-size: 11px; color: #64748b;">${globalDireccion}</span>`).openPopup();
                } catch (e) {
                    console.error("Error al inicializar el mapa global:", e);
                }
            @endif
        });
    </script>
</body>
</html>
