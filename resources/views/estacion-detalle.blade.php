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
<body class="bg-slate-50 text-slate-800 min-h-screen font-sans antialiased" x-data="{ tab: 'inicio' }">

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

    <!-- Slider Header -->
    <div class="relative w-full h-[50vh] min-h-[400px] overflow-hidden bg-slate-900" x-data="{ activeSlide: 0, slides: {{ json_encode($sliderImages) }} }" x-init="setInterval(() => { activeSlide = activeSlide === slides.length - 1 ? 0 : activeSlide + 1 }, 5000)">
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
            <a href="/" class="glass-panel text-slate-800 hover:bg-white px-4 py-2 rounded-xl transition flex items-center gap-2 font-bold text-sm shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>
            <div class="flex gap-3">
                @auth
                    @role('admin')
                    <a href="{{ url('/admin/gasolineras/' . $estacion->Codigo . '/edit') }}" class="text-xs font-bold glass-panel text-emerald-600 px-5 py-2.5 rounded-full hover:bg-emerald-50 transition-all shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        EDITAR
                    </a>
                    @endrole
                    <a href="{{ url('/dashboard') }}" class="text-xs font-bold glass-panel text-blue-600 px-5 py-2.5 rounded-full hover:bg-blue-50 transition-all shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        PANEL
                    </a>
                @endauth
            </div>
        </header>

        <!-- Titulo en el Slider -->
        <div class="absolute bottom-12 left-0 right-0 z-10 max-w-7xl mx-auto px-6">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-white drop-shadow-2xl mb-4 leading-tight">{{ $estacion->Nombre }}</h1>
            <p class="text-lg md:text-2xl text-blue-300 font-medium drop-shadow-md flex items-center gap-2">
                <svg class="w-6 h-6 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="truncate">{{ $estacion->Direccion }}, {{ $estacion->Poblacion }}</span>
            </p>
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
                <button @click="tab = 'contacto'" :class="tab === 'contacto' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors uppercase tracking-wider outline-none">
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
                                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                                        attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
                                    }).addTo(map);
                                    L.marker([{{ $lat }}, {{ $lng }}]).addTo(map)
                                        .bindPopup('<b class="text-slate-900">{{ $estacion->Nombre }}</b><br><span class="text-slate-600">{{ $estacion->Direccion }}</span>').openPopup();
                                }
                                setTimeout(() => { map.invalidateSize(); }, 300);
                            };
                        </script>
                    </div>
                </div>

                <!-- Pestaña: Contacto -->
                <div x-show="tab === 'contacto'" style="display: none;" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-4" class="absolute inset-x-0 top-0">
                    <div class="bg-white p-8 md:p-12 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                        <h2 class="text-3xl font-black text-slate-900 mb-2">Contacta con Nosotros</h2>
                        <p class="text-slate-500 mb-8 text-lg">Rellena el siguiente formulario y nos pondremos en contacto contigo lo antes posible.</p>
                        
                        <form action="{{ route('estacion.contacto', $estacion->Codigo) }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Tu Nombre</label>
                                    <input type="text" name="nombre" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" placeholder="Ej. Juan Pérez">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Tu Correo Electrónico</label>
                                    <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" placeholder="ejemplo@correo.com">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Mensaje</label>
                                <textarea name="mensaje" rows="5" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none resize-none" placeholder="¿En qué podemos ayudarte?"></textarea>
                            </div>
                            <button type="submit" class="w-full md:w-auto px-8 py-4 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/30">
                                Enviar Mensaje
                            </button>
                        </form>
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
                </div>
            </div>

        </div>
    </main>

</body>
</html>
