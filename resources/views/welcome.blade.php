<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Utrecar | Red de Estaciones de Servicio</title>
    
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
    </style>
</head>
<body class="bg-slate-50/50 text-slate-800 antialiased min-h-screen" x-data="{ tab: 'inicio', showTerms: false, showLegal: false, showPrivacy: false }" x-effect="if (tab === 'contacto') { setTimeout(() => { if (window.globalMapInstance) { window.globalMapInstance.invalidateSize(); } }, 200); }" @keydown.window.escape="showTerms = false; showLegal = false; showPrivacy = false;">

    @php
        $sliderImages = [];
        if ($homeConfig && !empty($homeConfig->slider_images)) {
            $images = is_string($homeConfig->slider_images) ? json_decode($homeConfig->slider_images, true) : $homeConfig->slider_images;
            if (is_array($images) && count($images) > 0) {
                foreach ($images as $img) {
                    $sliderImages[] = Storage::disk('public')->url($img);
                }
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

    <!-- Cinematic Alpine.js Header Slider (Identical to details page slider) -->
    <div class="relative w-full h-[50vh] min-h-[400px] overflow-hidden bg-slate-900" 
         x-data="{ activeSlide: 0, slides: {{ json_encode($sliderImages) }} }" 
         x-init="setInterval(() => { activeSlide = activeSlide === slides.length - 1 ? 0 : activeSlide + 1 }, 5000)">
        
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="activeSlide === index" 
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 transform scale-105"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-1000"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-105"
                 class="absolute inset-0 w-full h-full">
                <img :src="slide" class="w-full h-full object-cover" alt="Slider image">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent"></div>
            </div>
        </template>
        
        <!-- Navbar Overlay -->
        <header class="absolute top-0 left-0 right-0 z-10 max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="flex items-center gap-3 bg-slate-950/20 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/10">
                <div class="bg-gradient-to-tr from-blue-600 to-cyan-500 text-white w-9 h-9 rounded-xl flex items-center justify-center font-black text-lg shadow-lg">
                    U
                </div>
                <div>
                    <p class="font-black text-sm tracking-tight leading-none text-white">Utrecar</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                        <p class="text-[7px] uppercase tracking-[0.2em] text-emerald-400 font-extrabold">Active Network</p>
                    </div>
                </div>
            </div>
            
            <nav class="flex gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-xs font-bold bg-white/95 text-slate-800 backdrop-blur-md px-5 py-2.5 rounded-full hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-lg">
                            IR AL PANEL
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-bold bg-white/10 text-white backdrop-blur-md border border-white/20 px-5 py-2.5 rounded-full hover:bg-white hover:text-slate-800 transition-all duration-300 shadow-lg">
                            ACCEDER
                        </a>
                    @endauth
                @endif
            </nav>
        </header>

        <!-- Titulo en el Slider -->
        <div class="absolute bottom-12 left-0 right-0 z-10 max-w-7xl mx-auto px-6">
            <span class="px-3.5 py-1.5 bg-blue-500/20 backdrop-blur-md border border-blue-400/20 rounded-full text-xs font-bold text-blue-300 tracking-wider uppercase">
                Nuestra Red
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white drop-shadow-2xl mt-4 mb-2 leading-tight">
                {{ $homeConfig->titulo ?? 'Red de Estaciones de Servicio' }}
            </h1>
            <p class="text-sm md:text-base text-slate-300 font-medium drop-shadow-md max-w-2xl">
                {{ $homeConfig->subtitulo ?? 'Precios en tiempo real y servicios premium en carretera. Consulta los combustibles de cada estación y planifica tu ruta.' }}
            </p>
        </div>
    </div>

    <!-- Main Navigation Tabs Bar Below Slider -->
    <div class="sticky top-0 z-40 bg-white/90 backdrop-blur-lg border-b border-slate-200 shadow-sm mb-12">
        <div class="max-w-5xl mx-auto px-6">
            <nav class="flex gap-8 overflow-x-auto no-scrollbar" aria-label="Tabs">
                <button @click="tab = 'inicio'" 
                        :class="tab === 'inicio' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors uppercase tracking-wider outline-none">
                    Inicio
                </button>
                <button @click="tab = 'quienes_somos'" 
                        :class="tab === 'quienes_somos' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors uppercase tracking-wider outline-none">
                    Quiénes Somos
                </button>
                <button @click="tab = 'contacto'" 
                        :class="tab === 'contacto' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors uppercase tracking-wider outline-none">
                    Contacto
                </button>
            </nav>
        </div>
    </div>

    <!-- Main Content Container -->
    <main class="max-w-5xl mx-auto px-6 pb-24">
        
        <!-- Session status alerts -->
        @if (session('success'))
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3">
                <svg class="w-6 h-6 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Tab 1: Inicio (Gas Stations Accordion List) -->
        <div x-show="tab === 'inicio'" class="space-y-4">
            @if($homeConfig && $homeConfig->texto_inicio)
                <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.015)] border border-slate-100 prose max-w-none text-slate-600 leading-relaxed mb-6">
                    {!! $homeConfig->texto_inicio !!}
                </div>
            @endif

            @forelse($gasolineras as $gasolinera)
                @php
                    $contenido = $gasolinera->contenido;
                    $mainImage = null;
                    
                    // 1. Check if custom main image exists
                    if ($contenido && $contenido->imagen) {
                        $mainImage = Storage::disk('public')->url($contenido->imagen);
                    } 
                    // 2. Fallback to first slider image
                    elseif ($contenido && !empty($contenido->slider_images)) {
                        $images = is_string($contenido->slider_images) ? json_decode($contenido->slider_images, true) : $contenido->slider_images;
                        if (is_array($images) && count($images) > 0) {
                            $mainImage = Storage::disk('public')->url($images[0]);
                        }
                    }
                    
                    $lat = $contenido->latitud ?? null;
                    $lng = $contenido->longitud ?? null;
                    $hasMap = $lat && $lng;
                    
                    // Priority address logic: use donde_estamos_texto, fallback to central DB address
                    $addressText = ($contenido && $contenido->donde_estamos_texto) 
                        ? $contenido->donde_estamos_texto 
                        : ($gasolinera->Direccion . ' — ' . $gasolinera->Poblacion . ' (' . $gasolinera->Provincia . ')');
                @endphp

                <!-- Station Card Container -->
                <div class="group bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.015)] hover:border-blue-500/20 transition-all duration-300 overflow-hidden">
                     
                    <!-- Card Body -->
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 gap-5">
                        
                        <!-- Left: Thumbnail & Details -->
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            
                            <!-- Small format Thumbnail (Square) -->
                            @if($mainImage)
                                <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl overflow-hidden bg-slate-100 shrink-0 shadow-sm relative">
                                    <img src="{{ $mainImage }}" alt="{{ $gasolinera->Nombre }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-16 h-16 md:w-20 md:h-20 bg-slate-50 border border-slate-200/50 rounded-2xl flex flex-col items-center justify-center text-slate-400 shrink-0">
                                    <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="text-[7px] font-bold text-slate-400 tracking-wider uppercase mt-1">UTRECAR</span>
                                </div>
                            @endif

                            <!-- Details (Uses Custom Address and Displays Brand & Prices) -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <a href="{{ route('estacion.show', \Illuminate\Support\Str::slug($gasolinera->Nombre)) }}" class="hover:text-blue-600 transition-colors duration-300">
                                        <h3 class="text-lg md:text-xl font-bold text-slate-900 tracking-tight group-hover:text-blue-600 transition-colors duration-300 truncate">
                                            {{ $gasolinera->Nombre }}
                                        </h3>
                                    </a>
                                    @if($gasolinera->marca)
                                        <span class="px-2 py-0.5 bg-slate-100 border border-slate-200/40 text-[7px] font-extrabold uppercase tracking-widest text-slate-600 rounded-full shrink-0">
                                            {{ $gasolinera->marca }}
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="flex items-start gap-1 text-xs text-slate-500 font-medium mb-3">
                                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <span class="line-clamp-2 text-slate-400" title="{{ $addressText }}">
                                        {{ $addressText }}
                                    </span>
                                </p>

                                <!-- Prices block (increased font size) -->
                                <div class="flex gap-2.5 mb-3">
                                    <div class="bg-slate-50 border border-slate-200/60 px-3.5 py-1.5 rounded-xl flex items-center gap-2">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Diesel</span>
                                        <span class="text-lg md:text-xl font-black text-slate-900 leading-none">
                                            {{ $gasolinera->diesel ? number_format($gasolinera->diesel, 3) : '---' }}€
                                        </span>
                                    </div>
                                    <div class="bg-blue-50/40 border border-blue-200/40 px-3.5 py-1.5 rounded-xl flex items-center gap-2">
                                        <span class="text-[9px] font-bold text-blue-500 uppercase tracking-wider">SP 95</span>
                                        <span class="text-lg md:text-xl font-black text-blue-600 leading-none">
                                            {{ $gasolinera->gasolina95 ? number_format($gasolinera->gasolina95, 3) : '---' }}€
                                        </span>
                                    </div>
                                </div>

                                <!-- CTA Ficha Completa Button Directly Below Prices -->
                                <div class="mb-4">
                                    <a href="{{ route('estacion.show', \Illuminate\Support\Str::slug($gasolinera->Nombre)) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition-all duration-300 shadow-md shadow-blue-500/10 hover:shadow-lg hover:shadow-blue-500/20 transform hover:-translate-y-0.5 uppercase tracking-wider">
                                        Ver Ficha Completa
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </a>
                                </div>

                                <!-- Services block (moved below prices, increased font size) -->
                                @if($contenido && $contenido->servicios && count($contenido->servicios) > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach(array_slice($contenido->servicios, 0, 4) as $servicio)
                                            @php
                                                $badgeClasses = 'bg-slate-50 text-slate-500 border-slate-200/50';
                                                if (in_array($servicio, ['Tienda', 'Cafeteria', 'Restaurante'])) {
                                                    $badgeClasses = 'bg-emerald-50 text-emerald-700 border-emerald-100/50';
                                                } elseif (in_array($servicio, ['GLP', 'Electrico'])) {
                                                    $badgeClasses = 'bg-amber-50 text-amber-700 border-amber-100/50';
                                                } elseif (in_array($servicio, ['Lavado'])) {
                                                    $badgeClasses = 'bg-indigo-50/70 text-indigo-700 border-indigo-100/70';
                                                }
                                            @endphp
                                            <span class="px-2.5 py-1 border rounded-full text-[10px] font-bold tracking-wide {{ $badgeClasses }}">
                                                {{ $servicio }}
                                            </span>
                                        @endforeach
                                        @if(count($contenido->servicios) > 4)
                                            <span class="px-2 py-1 bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-400 rounded-full">
                                                +{{ count($contenido->servicios) - 4 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Right: Small Map, CTA Button & Accordion Toggle Icon -->
                        <div class="flex items-center gap-4 shrink-0 w-full md:w-auto justify-between md:justify-end">
                            @if($hasMap)
                                <div class="relative rounded-xl overflow-hidden border border-slate-100 shadow-inner w-36 h-24 md:w-44 md:h-24 map-container shrink-0">
                                    <div id="map-{{ $gasolinera->Codigo }}" class="w-full h-full z-0"></div>
                                    
                                    <!-- Google Maps Directions Link overlay (Opens in a new tab) -->
                                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $lat }},{{ $lng }}" 
                                       target="_blank" 
                                       class="absolute bottom-1.5 right-1.5 bg-blue-600 text-white font-bold text-[8px] px-2.5 py-1 rounded-lg shadow-md hover:bg-blue-700 transition duration-200 z-10 uppercase tracking-wider flex items-center gap-1">
                                        <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                        </svg>
                                        Cómo llegar
                                    </a>
                                </div>
                            @else
                                <div class="bg-slate-50 border border-slate-100 rounded-xl flex flex-col items-center justify-center p-3 text-center w-36 h-24 md:w-44 md:h-24 shrink-0">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Sin Mapa</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-100 rounded-[2rem] p-16 text-center">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="text-xl font-bold text-slate-800">No hay estaciones cargadas</h3>
                    <p class="text-sm text-slate-400 mt-1">Vuelve a consultar en unos minutos.</p>
                </div>
            @endforelse
        </div>

        <!-- Tab 2: Quiénes Somos -->
        <div x-show="tab === 'quienes_somos'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="bg-white p-8 md:p-12 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.015)] border border-slate-100 max-w-4xl mx-auto"
             style="display: none;">
            <h2 class="text-3xl font-black text-slate-900 mb-6">Quiénes Somos</h2>
            <div class="prose max-w-none text-slate-600 leading-relaxed">
                @if($homeConfig && $homeConfig->quienes_somos)
                    {!! $homeConfig->quienes_somos !!}
                @else
                    <p>Bienvenido a nuestra red de estaciones de servicio. Nos esforzamos por ofrecer combustibles de la más alta calidad y un excelente servicio de atención al cliente en cada una de nuestras estaciones distribuidas estratégicamente por todo el territorio.</p>
                @endif
            </div>
        </div>

        <!-- Tab 3: Contacto -->
        <div x-show="tab === 'contacto'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="bg-white p-8 md:p-12 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.015)] border border-slate-100 max-w-4xl mx-auto"
             style="display: none;">
            <h2 class="text-3xl font-black text-slate-900 mb-2">Contacto</h2>
            <p class="text-slate-500 mb-8 text-base">¿Tienes alguna duda o sugerencia? Completa el formulario a continuación y nos pondremos en contacto contigo lo antes posible.</p>
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Form (Col 1-7) -->
                <form action="{{ route('home.contacto') }}" method="POST" class="lg:col-span-7 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tu Nombre</label>
                            <input type="text" name="nombre" required class="w-full bg-slate-50 border border-slate-200/60 rounded-xl px-4 py-3 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-sm" placeholder="Ej. Juan Pérez">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tu Correo Electrónico</label>
                            <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200/60 rounded-xl px-4 py-3 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-sm" placeholder="ejemplo@correo.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mensaje</label>
                        <textarea name="mensaje" rows="5" required class="w-full bg-slate-50 border border-slate-200/60 rounded-xl px-4 py-3 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-sm resize-none" placeholder="¿En qué podemos ayudarte?"></textarea>
                    </div>
                    <button type="submit" class="w-full md:w-auto px-8 py-3.5 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/30 uppercase tracking-wider">
                        Enviar Mensaje
                    </button>
                </form>

                <!-- Sidebar (Col 8-12) -->
                <div class="lg:col-span-5 bg-slate-50 border border-slate-100 p-6 rounded-2xl space-y-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-2">Oficinas Centrales</h3>
                    
                    @if($homeConfig && $homeConfig->contacto_direccion)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Dirección</span>
                                <span class="text-xs font-medium text-slate-700">{{ $homeConfig->contacto_direccion }}</span>
                            </div>
                        </div>
                    @endif

                    @if($homeConfig && $homeConfig->contacto_telefono)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Teléfono</span>
                                <a href="tel:{{ $homeConfig->contacto_telefono }}" class="text-xs font-medium text-blue-600 hover:underline">{{ $homeConfig->contacto_telefono }}</a>
                            </div>
                        </div>
                    @endif

                    @if($homeConfig && $homeConfig->contacto_email)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Email</span>
                                <a href="mailto:{{ $homeConfig->contacto_email }}" class="text-xs font-medium text-blue-600 hover:underline break-all block">{{ $homeConfig->contacto_email }}</a>
                            </div>
                        </div>
                    @endif

                    @if($homeConfig && $homeConfig->latitud && $homeConfig->longitud)
                        <div class="pt-4 border-t border-slate-200/60">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nuestra Ubicación</span>
                            <div class="relative rounded-xl overflow-hidden border border-slate-100 shadow-inner w-full h-48 map-container shrink-0">
                                <div id="map-global" class="w-full h-full z-0"></div>
                                
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $homeConfig->latitud }},{{ $homeConfig->longitud }}" 
                                   target="_blank" 
                                   class="absolute bottom-2 right-2 bg-blue-600 text-white font-bold text-[9px] px-3 py-1.5 rounded-lg shadow-md hover:bg-blue-700 transition duration-200 z-10 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                    Cómo llegar
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
    </main>

    <!-- Public Footer -->
    <footer class="bg-slate-900 text-slate-300 mt-16 relative z-10 border-t border-slate-800">
        <div class="max-w-5xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: Brand & Logo -->
                <div class="space-y-4 md:col-span-2">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-tr from-blue-600 to-cyan-500 text-white w-9 h-9 rounded-xl flex items-center justify-center font-black text-lg shadow-md">
                            U
                        </div>
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
                            <a href="#" @click.prevent="tab = 'inicio'; window.scrollTo({top: 0, behavior: 'smooth'})" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Inicio</a>
                        </li>
                        <li>
                            <a href="#" @click.prevent="tab = 'quienes_somos'; window.scrollTo({top: 0, behavior: 'smooth'})" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Quiénes Somos</a>
                        </li>
                        <li>
                            <a href="#" @click.prevent="tab = 'contacto'; window.scrollTo({top: 0, behavior: 'smooth'})" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Contacto</a>
                        </li>
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
                    <p class="font-bold mb-2">1. Información Legal</p>
                    <p class="mb-4">En cumplimiento del artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y Comercio Electrónico (LSSI-CE), se informa que este sitio web es operado por Utrecar S.L., con domicilio social en la sede corporativa provista en la sección de contacto.</p>
                    
                    <p class="font-bold mb-2">2. Propiedad Intelectual</p>
                    <p class="mb-4">Todos los contenidos de este sitio web, incluyendo textos, gráficos, imágenes, su diseño y los derechos de propiedad intelectual que pudieran corresponder a dichos contenidos, así como todas las marcas, nombres comerciales o cualquier otro signo distintivo son propiedad de Utrecar o de sus legítimos propietarios, quedando reservados todos los derechos sobre los mismos.</p>
                    
                    <p class="font-bold mb-2">3. Exclusión de Responsabilidad</p>
                    <p class="mb-4">Los precios de carburantes y la información sobre las estaciones de servicio se muestran únicamente con carácter informativo. Aunque nos esforzamos por mantener la información actualizada, no nos responsabilizamos de posibles discrepancias en los precios o disponibilidad de servicios en el momento del repostaje.</p>
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
                    <p class="font-bold mb-2">1. Responsable del Tratamiento</p>
                    <p class="mb-4">Utrecar S.L. es el responsable de sus datos personales. Nos comprometemos a proteger su privacidad de acuerdo con el Reglamento General de Protección de Datos (RGPD) y la legislación local vigente.</p>
                    
                    <p class="font-bold mb-2">2. Datos que Recopilamos</p>
                    <p class="mb-4">A través del formulario de contacto público recopilamos su nombre, dirección de correo electrónico y el contenido de su mensaje con el único fin de responder a su consulta.</p>
                    
                    <p class="font-bold mb-2">3. Conservación y Seguridad</p>
                    <p class="mb-4">Sus datos personales se conservarán únicamente durante el tiempo necesario para resolver su consulta y no serán compartidos con terceros sin su consentimiento expreso, salvo obligación legal. Implementamos medidas de seguridad técnicas y organizativas para proteger sus datos contra accesos no autorizados.</p>
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
