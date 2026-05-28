<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $estacion->Nombre }} | Utrecar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Leaflet para el mapa -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 0, 0, 0.05);
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
<body class="bg-slate-50 text-slate-800 min-h-screen font-sans antialiased" x-data="{ tab: 'inicio', showTerms: false, showLegal: false, showPrivacy: false }" @keydown.window.escape="showTerms = false; showLegal = false; showPrivacy = false;">

    @php
        $contenido = $estacion->contenido;
        $sliderImages = [];
        if ($contenido && !empty($contenido->slider_images)) {
            $images = is_string($contenido->slider_images) ? json_decode($contenido->slider_images, true) : $contenido->slider_images;
            if (is_array($images) && count($images) > 0) {
                foreach ($images as $img) {
                    $sliderImages[] = Storage::disk('public')->url($img);
                }
            }
        }
        
        if (empty($sliderImages)) {
            $sliderImages = [
                'https://images.unsplash.com/photo-1545084920-56ef6d1a5e95?auto=format&fit=crop&q=80&w=2000',
                'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?auto=format&fit=crop&q=80&w=2000'
            ];
        }
        $lat = $contenido->latitud ?? 40.4168;
        $lng = $contenido->longitud ?? -3.7038;
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

    <!-- Slider Header -->
    <div class="relative w-full overflow-hidden bg-slate-900 slider-container-dynamic" x-data="{ activeSlide: 0, slides: {{ json_encode($sliderImages) }}, lightboxOpen: false, lightboxIndex: 0 }" x-init="setInterval(() => { if (!lightboxOpen) { activeSlide = activeSlide === slides.length - 1 ? 0 : activeSlide + 1 } }, 5000)">
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="activeSlide === index" 
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 transform scale-105"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-1000"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-105"
                 class="absolute inset-0 w-full h-full">
                <img :src="slide" class="w-full h-full object-cover object-center cursor-zoom-in" alt="Slider image" @click="lightboxOpen = true; lightboxIndex = index">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/10 to-transparent pointer-events-none"></div>
            </div>
        </template>

        <!-- Lightbox Modal -->
        <div x-show="lightboxOpen" 
             class="fixed inset-0 z-50 overflow-hidden bg-slate-950/95 flex flex-col items-center justify-center p-4 md:p-10 select-none" 
             style="display: none;" 
             x-cloak
             @keydown.window.escape="lightboxOpen = false"
             @keydown.window.arrow-left="lightboxIndex = lightboxIndex === 0 ? slides.length - 1 : lightboxIndex - 1"
             @keydown.window.arrow-right="lightboxIndex = lightboxIndex === slides.length - 1 ? 0 : lightboxIndex + 1">
            
            <!-- Close Button -->
            <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-white/70 hover:text-white transition p-2 rounded-full hover:bg-white/10 z-50">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Prev Button -->
            <button @click="lightboxIndex = lightboxIndex === 0 ? slides.length - 1 : lightboxIndex - 1" 
                    class="absolute left-6 text-white/70 hover:text-white transition p-3 rounded-full hover:bg-white/10 z-50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>

            <!-- Next Button -->
            <button @click="lightboxIndex = lightboxIndex === slides.length - 1 ? 0 : lightboxIndex + 1" 
                    class="absolute right-6 text-white/70 hover:text-white transition p-3 rounded-full hover:bg-white/10 z-50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>

            <!-- Image Container -->
            <div class="relative max-w-5xl max-h-[80vh] flex items-center justify-center" @click.away="lightboxOpen = false">
                <img :src="slides[lightboxIndex]" class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl" alt="Full image">
            </div>

            <!-- Counter / Caption -->
            <div class="mt-6 text-white/60 text-sm font-semibold tracking-wider">
                Imagen <span x-text="lightboxIndex + 1"></span> de <span x-text="slides.length"></span>
            </div>
        </div>
        
        <!-- Navbar Overlay -->
        <header class="absolute top-0 left-0 right-0 z-10 max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <a href="/" class="glass-panel text-slate-800 hover:bg-white px-4 py-2 rounded-xl transition flex items-center gap-2 font-bold text-sm shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver a la página principal
            </a>
            <div class="flex gap-3 items-center">
                @auth
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_gasolineras') || auth()->user()->id === 1)
                    <a href="{{ url('/admin/gasolineras/' . $estacion->Codigo . '/edit') }}" class="text-xs font-bold glass-panel text-emerald-600 px-5 py-2.5 rounded-full hover:bg-emerald-50 transition-all shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        EDITAR
                    </a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="text-xs font-bold glass-panel text-blue-600 px-5 py-2.5 rounded-full hover:bg-blue-50 transition-all shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        PANEL
                    </a>
                @endauth
            </div>
        </header>

        <!-- Titulo en el Slider -->
        <div class="absolute left-0 right-0 z-10 max-w-7xl mx-auto px-6" style="bottom: 24px;">
            <div class="inline-block border border-white/15 p-3.5 md:p-5 rounded-2xl shadow-2xl max-w-xl" style="background-color: rgba(2, 6, 23, 0.75); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);">
                <span class="px-2 py-0.5 bg-blue-500/25 backdrop-blur-md border border-blue-400/30 rounded-full text-[9px] font-bold text-blue-300 tracking-wider uppercase">
                    Estación de Servicio
                </span>
                <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-white drop-shadow-2xl mt-2 mb-1 leading-tight">
                    {{ $estacion->Nombre }}
                </h1>
                <p class="text-xs md:text-sm text-blue-300 font-medium drop-shadow-md flex items-center gap-1.5 mt-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="truncate">{{ $estacion->Direccion }}, {{ $estacion->Poblacion }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Navegación de Pestañas -->
    <div class="sticky top-0 z-40 bg-white/90 backdrop-blur-lg border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-6">
            <nav class="flex gap-8 overflow-x-auto no-scrollbar" aria-label="Tabs">
                <button @click="tab = 'inicio'" :class="tab === 'inicio' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors uppercase tracking-wider outline-none">
                    Inicio
                </button>
                <button @click="tab = 'quienes_somos'" :class="tab === 'quienes_somos' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors uppercase tracking-wider outline-none">
                    Quiénes Somos
                </button>
                <button @click="tab = 'donde_estamos'; setTimeout(() => { window.dispatchEvent(new Event('resize')); initMap(); }, 50)" :class="tab === 'donde_estamos' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors uppercase tracking-wider outline-none">
                    Dónde Estamos
                </button>
                <button @click="tab = 'contacto'; setTimeout(() => { window.dispatchEvent(new Event('resize')); initContactMap(); }, 50)" :class="tab === 'contacto' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors uppercase tracking-wider outline-none">
                    Contacto
                </button>
            </nav>
        </div>
    </div>

    <!-- Contenido Principal -->
    <main class="max-w-7xl mx-auto px-6 py-12">
        
        <!-- Mensajes de sesión -->
        @if (session('success'))
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3">
                <svg class="w-6 h-6 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 flex flex-col gap-2">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm">{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative items-start">
            
            <!-- Area de Contenido (2/3) -->
            <div class="lg:col-span-2 relative min-h-[500px]">
                
                <!-- Pestaña: Inicio -->
                <div x-show="tab === 'inicio'" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-4" class="absolute inset-x-0 top-0">
                    <div class="bg-white p-8 md:p-12 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                        <h2 class="text-3xl font-black text-slate-900 mb-6">Bienvenido a {{ $estacion->Nombre }}</h2>
                        <div class="prose max-w-none text-lg text-slate-600 leading-relaxed">
                            @if($contenido && $contenido->texto_inicio)
                                {!! $contenido->texto_inicio !!}
                            @else
                                <p>Encuentra los mejores precios y el mejor servicio en nuestra estación. Abierto para ofrecerte la máxima calidad en carburantes y servicios adicionales para tu vehículo.</p>
                            @endif
                        </div>
                        
                        @if($contenido && $contenido->servicios && count($contenido->servicios) > 0)
                        <div class="mt-10">
                            <h3 class="text-sm font-bold text-blue-600 uppercase tracking-widest mb-4">Servicios Disponibles</h3>
                            <div class="flex flex-wrap gap-3">
                                @foreach($contenido->servicios as $servicio)
                                    <span class="px-5 py-2 bg-blue-50 border border-blue-100 rounded-full text-sm font-medium text-blue-800 shadow-sm">{{ $servicio }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Pestaña: Quiénes Somos -->
                <div x-show="tab === 'quienes_somos'" style="display: none;" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-4" class="absolute inset-x-0 top-0">
                    <div class="bg-white p-8 md:p-12 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                        <h2 class="text-3xl font-black text-slate-900 mb-6">Sobre Nosotros</h2>
                        <div class="prose max-w-none text-lg text-slate-600 leading-relaxed">
                            @if($contenido && $contenido->quienes_somos)
                                {!! $contenido->quienes_somos !!}
                            @else
                                <p>Información no disponible por el momento. Trabajamos día a día para brindarte el mejor servicio.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pestaña: Dónde Estamos -->
                <div x-show="tab === 'donde_estamos'" style="display: none;" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-4" class="absolute inset-x-0 top-0">
                    <div class="bg-white p-8 md:p-12 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                            <h2 class="text-3xl font-black text-slate-900">Ubicación</h2>
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $lat }},{{ $lng }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 transition-all transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                Cómo llegar en GPS
                            </a>
                        </div>
                        
                        @if($contenido && $contenido->donde_estamos_texto)
                        <div class="prose max-w-none text-lg text-slate-600 leading-relaxed mb-8">
                            {!! $contenido->donde_estamos_texto !!}
                        </div>
                        @endif

                        <!-- Contenedor del Mapa -->
                        <div id="map" class="w-full h-[400px] rounded-2xl border border-slate-200 shadow-inner z-0"></div>
                        <script>
                            let map;
                            const initMap = () => {
                                if(!map) {
                                    map = L.map('map').setView([{{ $lat }}, {{ $lng }}], 15);
                                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
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

                                    L.marker([{{ $lat }}, {{ $lng }}], { icon: customIcon }).addTo(map)
                                        .bindPopup('<b class="text-slate-900">{{ $estacion->Nombre }}</b><br><span class="text-slate-600">{{ $estacion->Direccion }}</span>').openPopup();
                                }
                                setTimeout(() => { map.invalidateSize(); }, 300);
                            };

                            let contactMap;
                            const initContactMap = () => {
                                if(!contactMap) {
                                    contactMap = L.map('map-contacto', {
                                        zoomControl: true,
                                        attributionControl: false
                                    }).setView([{{ $lat }}, {{ $lng }}], 15);
                                    
                                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        maxZoom: 19,
                                        tileSize: 512,
                                        zoomOffset: -1,
                                        detectRetina: true
                                    }).addTo(contactMap);
                                    
                                    const markerColor = '#ef4444';
                                    const markerHtml = `<svg xmlns="http://www.w3.org/2000/svg" fill="${markerColor}" width="36" height="36" viewBox="0 0 24 24" style="stroke: #dbeafe; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg>`;
                                    const customIcon = L.divIcon({
                                        html: markerHtml,
                                        className: '',
                                        iconSize: [36, 36],
                                        iconAnchor: [18, 36]
                                    });

                                    L.marker([{{ $lat }}, {{ $lng }}], { icon: customIcon }).addTo(contactMap)
                                        .bindPopup('<b class="text-slate-900">{{ $estacion->Nombre }}</b><br><span class="text-slate-600">{{ $estacion->Direccion }}</span>').openPopup();
                                }
                                setTimeout(() => { contactMap.invalidateSize(); }, 300);
                            };
                        </script>
                    </div>
                </div>

                <!-- Pestaña: Contacto -->
                <div x-show="tab === 'contacto'" 
                     style="display: none;" 
                     x-transition:enter="transition ease-out duration-300 delay-100" 
                     x-transition:enter-start="opacity-0 transform translate-y-4" 
                     x-transition:enter-end="opacity-100 transform translate-y-0" 
                     x-transition:leave="transition ease-in duration-200" 
                     x-transition:leave-start="opacity-100 transform translate-y-0" 
                     x-transition:leave-end="opacity-0 transform translate-y-4" 
                     class="absolute inset-x-0 top-0">
                     
                    <div class="bg-white p-8 md:p-12 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                        <h2 class="text-3xl font-black text-slate-900 mb-2">Contacta con Nosotros</h2>
                        <p class="text-slate-500 mb-8 text-lg">Rellena el siguiente formulario y nos pondremos en contacto contigo lo antes posible.</p>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                            <!-- Form (Col 1-7) -->
                            <form action="{{ route('estacion.contacto', $estacion->Codigo) }}" method="POST" class="lg:col-span-7 space-y-6">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Tu Nombre</label>
                                        <input type="text" name="nombre" required class="w-full bg-slate-50 border border-slate-200/60 rounded-xl px-4 py-3 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-sm" placeholder="Ej. Juan Pérez">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Tu Correo Electrónico</label>
                                        <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200/60 rounded-xl px-4 py-3 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-sm" placeholder="ejemplo@correo.com">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Mensaje</label>
                                    <textarea name="mensaje" rows="5" required class="w-full bg-slate-50 border border-slate-200/60 rounded-xl px-4 py-3 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-sm resize-none" placeholder="¿En qué podemos ayudarte?"></textarea>
                                </div>
                                <button type="submit" class="w-full md:w-auto px-8 py-3.5 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/30 uppercase tracking-wider">
                                    Enviar Mensaje
                                </button>
                            </form>

                            <!-- Sidebar (Col 8-12) -->
                            <div class="lg:col-span-5 bg-slate-50 border border-slate-100 p-6 rounded-2xl space-y-6">
                                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-2">Datos de la Estación</h3>
                                
                                @php
                                    $addressText = ($contenido && $contenido->donde_estamos_texto) 
                                        ? $contenido->donde_estamos_texto 
                                        : ($estacion->Direccion . ' — ' . $estacion->Poblacion . ' (' . $estacion->Provincia . ')');
                                @endphp

                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Dirección</span>
                                        <span class="text-xs font-medium text-slate-700">{{ $addressText }}</span>
                                    </div>
                                </div>

                                @if($contenido && $contenido->contacto_telefono)
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Teléfono</span>
                                            <a href="tel:{{ $contenido->contacto_telefono }}" class="text-xs font-medium text-blue-600 hover:underline">{{ $contenido->contacto_telefono }}</a>
                                        </div>
                                    </div>
                                @endif

                                @if($contenido && $contenido->contacto_email)
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Email</span>
                                            <a href="mailto:{{ $contenido->contacto_email }}" class="text-xs font-medium text-blue-600 hover:underline break-all block">{{ $contenido->contacto_email }}</a>
                                        </div>
                                    </div>
                                @endif

                                <div class="pt-4 border-t border-slate-200/60">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ubicación en Mapa</span>
                                    <div class="relative rounded-xl overflow-hidden border border-slate-100 shadow-inner w-full h-48 map-container shrink-0">
                                        <div id="map-contacto" class="w-full h-full z-0"></div>
                                        
                                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $lat }},{{ $lng }}" 
                                           target="_blank" 
                                           class="absolute bottom-2 right-2 bg-blue-600 text-white font-bold text-[9px] px-3 py-1.5 rounded-lg shadow-md hover:bg-blue-700 transition duration-200 z-10 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                            </svg>
                                            Cómo llegar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Area de Sidebar (1/3) -->
            <div class="space-y-6 w-full max-w-md mx-auto lg:max-w-none">
                <!-- Panel de Precios -->
                <div class="bg-white p-8 rounded-[2rem] sticky top-32 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 border-t-4 border-t-blue-500">
                    <h4 class="text-xs font-black text-blue-600 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Precios Hoy
                    </h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-slate-500 font-bold uppercase text-xs">Diesel A</span>
                            <span class="text-3xl font-black text-slate-900">{{ number_format($estacion->diesel ?? 0, 3) }}<span class="text-xl text-slate-400 ml-1">€</span></span>
                        </div>
                        <div class="flex justify-between items-center p-5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-slate-500 font-bold uppercase text-xs">Sin Plomo 95</span>
                            <span class="text-3xl font-black text-blue-600">{{ number_format($estacion->gasolina95 ?? 0, 3) }}<span class="text-xl text-blue-300 ml-1">€</span></span>
                        </div>
                    </div>

                    @if($contenido && ($contenido->horario || $contenido->contacto_telefono || $contenido->contacto_email))
                    <div class="mt-8 pt-6 border-t border-slate-100 space-y-5">
                        @if($contenido->horario)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 rounded-lg shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase mb-1">Horario</span>
                                <span class="text-sm font-medium text-slate-800 leading-snug block">{{ $contenido->horario }}</span>
                            </div>
                        </div>
                        @endif

                        @if($contenido->contacto_telefono)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 rounded-lg shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase mb-1">Teléfono</span>
                                <a href="tel:{{ $contenido->contacto_telefono }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">{{ $contenido->contacto_telefono }}</a>
                            </div>
                        </div>
                        @endif
                        
                        @if($contenido->contacto_email)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 rounded-lg shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</span>
                                <a href="mailto:{{ $contenido->contacto_email }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors break-all block">{{ $contenido->contacto_email }}</a>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <a href="/" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs rounded-xl transition-all duration-300 shadow-lg shadow-blue-600/20 hover:shadow-xl hover:shadow-blue-600/35 transform hover:-translate-y-0.5 flex items-center justify-center gap-2 uppercase tracking-widest">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver a la página principal
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Public Footer -->
    <footer class="bg-slate-900 text-slate-300 mt-16 relative z-10 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 py-12">
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
                                <p class="text-[7px] uppercase tracking-[0.2em] text-blue-400 font-extrabold">Estación de Servicio</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        Tu red de confianza para repostar al mejor precio con servicios de alta calidad. Precios actualizados en tiempo real y atención al cliente premium en carretera.
                    </p>
                    <div class="pt-2">
                        <a href="/" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-400 hover:text-blue-300 transition duration-150 uppercase tracking-wider">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Volver a la Red de Gasolineras
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div>
                    <h4 class="text-xs font-extrabold text-white uppercase tracking-widest mb-4">Esta Estación</h4>
                    <ul class="space-y-2.5">
                        <li>
                            <a href="#" @click.prevent="tab = 'inicio'; window.scrollTo({top: 0, behavior: 'smooth'})" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Inicio</a>
                        </li>
                        <li>
                            <a href="#" @click.prevent="tab = 'quienes_somos'; window.scrollTo({top: 0, behavior: 'smooth'})" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Quiénes Somos</a>
                        </li>
                        <li>
                            <a href="#" @click.prevent="tab = 'donde_estamos'; window.scrollTo({top: 0, behavior: 'smooth'}); setTimeout(() => { window.dispatchEvent(new Event('resize')); initMap(); }, 50)" class="text-xs text-slate-400 hover:text-white transition-colors font-medium">Dónde Estamos</a>
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

</body>
</html>
