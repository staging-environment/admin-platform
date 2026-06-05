<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Panel de informes basado principalmente en la base de datos VirtusGesNet.
                </p>
            </div>

            <div class="text-sm text-gray-600 shrink-0 font-medium bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 shadow-sm">
                Tablas VirtusGesNet: <span class="font-bold text-slate-800">{{ count($tables) }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">


            @php
                $unreadMessages = \App\Models\ContactoMensaje::with('gasolinera')->where('is_read', false)->orderBy('created_at', 'desc')->get();
            @endphp
            @if($unreadMessages->count() > 0)
                <div class="flex flex-col gap-4">
                    @foreach($unreadMessages as $msg)
                        <div class="bg-red-600 rounded-lg p-3 shadow flex flex-col md:flex-row md:items-center justify-between text-white border border-red-700" style="background-color: #dc2626;">
                            <div class="flex items-center gap-3 mb-3 md:mb-0">
                                <div class="bg-white/20 p-1.5 rounded-full">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold uppercase tracking-wider text-white">
                                        @if($msg->gasolinera)
                                            ¡NUEVO MENSAJE PARA {{ $msg->gasolinera->Nombre }}!
                                        @else
                                            ¡NUEVO MENSAJE DE CONTACTO PRINCIPAL!
                                        @endif
                                    </h3>
                                    <p class="text-xs text-red-100">
                                        De: {{ $msg->nombre }} ({{ $msg->email }}) - Hace {{ str_replace('hace ', '', $msg->created_at->diffForHumans()) }}
                                    </p>
                                </div>
                            </div>
                            
                            <a href="{{ url('/admin/contacto-mensajes/' . $msg->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-red-600 rounded-md font-bold shadow hover:bg-gray-50 transition-all text-xs" style="color: #dc2626;">
                                LEER MENSAJE
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            @php
                $unreadApplications = \App\Models\JobApplication::with('jobOffer')->where('is_read', false)->orderBy('created_at', 'desc')->get();
            @endphp
            @if($unreadApplications->count() > 0)
                <div class="flex flex-col gap-4">
                    @foreach($unreadApplications as $app)
                        <div class="bg-blue-600 rounded-lg p-3 shadow flex flex-col md:flex-row md:items-center justify-between text-white border border-blue-700" style="background-color: #2563eb;">
                            <div class="flex items-center gap-3 mb-3 md:mb-0">
                                <div class="bg-white/20 p-1.5 rounded-full">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold uppercase tracking-wider text-white">
                                        ¡NUEVA INSCRIPCIÓN A: {{ $app->jobOffer->title ?? 'OFERTA DE EMPLEO' }}!
                                    </h3>
                                    <p class="text-xs text-blue-100">
                                        Candidato: {{ $app->first_name }} {{ $app->last_name }} ({{ $app->email }}) - Hace {{ str_replace('hace ', '', $app->created_at->diffForHumans()) }}
                                    </p>
                                </div>
                            </div>
                            
                            <a href="{{ url('/admin/job-applications/' . $app->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-blue-600 rounded-md font-bold shadow hover:bg-gray-50 transition-all text-xs" style="color: #2563eb;">
                                VER CANDIDATURA
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            @php
                $pendingVacations = [];
                if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Gestor') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('gestor')) {
                    $pendingVacations = \App\Models\EmpleadoVacacion::with('empleado')->where('estado', 'Pendiente')->orderBy('created_at', 'desc')->get();
                }
            @endphp
            @if(count($pendingVacations) > 0)
                <div class="flex flex-col gap-4">
                    @foreach($pendingVacations as $vac)
                        @if($vac->empleado)
                            <div class="bg-amber-600 rounded-lg p-3 shadow flex flex-col md:flex-row md:items-center justify-between text-white border border-amber-700 mb-4" style="background-color: #d97706;">
                                <div class="flex items-center gap-3 mb-3 md:mb-0">
                                    <div class="bg-white/20 p-1.5 rounded-full">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold uppercase tracking-wider text-white">
                                            ¡SOLICITUD DE VACACIONES PENDIENTE!
                                        </h3>
                                        <p class="text-xs text-amber-100">
                                            Empleado: {{ $vac->empleado->nombre }} {{ $vac->empleado->apellidos }} - Tipo: {{ $vac->tipo }} ({{ $vac->dias_solicitados }} días: del {{ \Carbon\Carbon::parse($vac->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($vac->fecha_fin)->format('d/m/Y') }})
                                        </p>
                                    </div>
                                </div>
                                
                                <a href="{{ url('/admin/recursos-humanos/' . $vac->empleado_id . '/edit') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-amber-700 rounded-md font-bold shadow hover:bg-gray-50 transition-all text-xs" style="color: #b45309;">
                                    REVISAR SOLICITUD
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @php
                $empleado = \App\Models\Empleado::where('email', auth()->user()->email)->first();
                $myResolvedVacations = [];
                if ($empleado) {
                    $myResolvedVacations = \App\Models\EmpleadoVacacion::where('empleado_id', $empleado->id)
                        ->whereIn('estado', ['Aceptada', 'Rechazada'])
                        ->orderBy('updated_at', 'desc')
                        ->limit(5)
                        ->get();
                }
            @endphp
            @if(count($myResolvedVacations) > 0)
                <div class="flex flex-col gap-4">
                    @foreach($myResolvedVacations as $vac)
                        @if($vac->estado === 'Aceptada')
                            <div class="bg-green-600 rounded-lg p-3 shadow flex items-center justify-between text-white border border-green-700 mb-4" style="background-color: #16a34a;">
                                <div class="flex items-center gap-3">
                                    <div class="bg-white/20 p-1.5 rounded-full">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold uppercase tracking-wider text-white">
                                            ¡SOLICITUD DE VACACIONES APROBADA!
                                        </h3>
                                        <p class="text-xs text-green-100">
                                            Tu solicitud de {{ $vac->tipo }} de {{ $vac->dias_solicitados }} de días (del {{ \Carbon\Carbon::parse($vac->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($vac->fecha_fin)->format('d/m/Y') }}) ha sido **APROBADA**.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-red-600 rounded-lg p-3 shadow flex items-center justify-between text-white border border-red-700 mb-4" style="background-color: #e11d48;">
                                <div class="flex items-center gap-3">
                                    <div class="bg-white/20 p-1.5 rounded-full">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold uppercase tracking-wider text-white">
                                            ¡SOLICITUD DE VACACIONES RECHAZADA!
                                        </h3>
                                        <p class="text-xs text-red-100">
                                            Tu solicitud de {{ $vac->tipo }} de {{ $vac->dias_solicitados }} de días (del {{ \Carbon\Carbon::parse($vac->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($vac->fecha_fin)->format('d/m/Y') }}) ha sido **RECHAZADA**.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Modern Widgets: Weather & Server Monitor --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Weather Widget --}}
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6 flex flex-col justify-between relative" id="weather-widget">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500 animate-spin" style="animation-duration: 25s;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path>
                                </svg>
                                El Tiempo
                            </h4>
                            <div class="flex items-center gap-1.5 bg-gray-50 hover:bg-amber-50 border border-gray-100 rounded-full px-2.5 py-1 transition-all cursor-pointer select-none" id="weather-loc-badge" title="Haga clic para cambiar de ciudad">
                                <span class="text-xs text-gray-600 font-medium" id="weather-loc">Cargando...</span>
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Search Input container (toggles with weather-loc-badge click) --}}
                        <div id="weather-search-container" class="hidden mb-4 relative">
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <input type="text" id="weather-search-input" placeholder="Buscar ciudad (ej. Sevilla)..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                    <span class="absolute right-3 top-2.5 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </span>
                                </div>
                                <button id="btn-search-close" class="text-xs text-gray-500 hover:text-gray-700 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">
                                    Cerrar
                                </button>
                            </div>
                            <div id="weather-search-suggestions" class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden max-h-48 overflow-y-auto divide-y divide-gray-100">
                                <!-- suggestions go here -->
                            </div>
                        </div>

                        <div class="flex items-center gap-6 py-2">
                            {{-- Weather Icon --}}
                            <div class="shrink-0 p-3 bg-amber-50 rounded-2xl border border-amber-100 flex items-center justify-center" id="weather-icon-container">
                                <svg class="w-12 h-12 text-amber-300 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                            </div>
                            <div class="space-y-0.5">
                                <div class="text-3xl font-bold text-gray-900 leading-none animate-pulse" id="weather-temp">--°C</div>
                                <div class="text-sm font-semibold text-gray-700 animate-pulse" id="weather-desc">Cargando clima...</div>
                                <div class="text-xs text-gray-500" id="weather-wind">Viento: -- km/h</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] text-gray-400">
                        <span>Actualizado desde tu navegador</span>
                        <button onclick="useGPSLocation()" class="hover:text-amber-600 transition flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Usar GPS preciso
                        </button>
                    </div>
                </div>

                {{-- Server Status Widget --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                </svg>
                                Estado del Servidor
                            </h4>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $serverStats['env'] === 'Producción' ? 'bg-red-55 text-red-700 border border-red-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}" style="{{ $serverStats['env'] === 'Producción' ? 'background-color: #fee2e2; color: #b91c1c; border-color: #fca5a5;' : '' }}">
                                {{ $serverStats['env'] }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-xs">
                            {{-- Disk usage --}}
                            <div class="space-y-1">
                                <div class="flex justify-between text-gray-500">
                                    <span>Almacenamiento (SSD)</span>
                                    <span class="font-semibold text-gray-700">{{ $serverStats['disk_used_percent'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $serverStats['disk_used_percent'] }}%"></div>
                                </div>
                                <div class="text-[10px] text-gray-400 font-mono">
                                    Libre: {{ $serverStats['disk_free'] }} / {{ $serverStats['disk_total'] }}
                                </div>
                            </div>

                            {{-- RAM usage --}}
                            <div class="space-y-1">
                                <div class="flex justify-between text-gray-500">
                                    <span>Memoria RAM</span>
                                    <span class="font-semibold text-gray-700">
                                        {{ $serverStats['ram_total'] !== '0 B' ? $serverStats['ram_used_percent'] . '%' : 'N/D' }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $serverStats['ram_used_percent'] }}%"></div>
                                </div>
                                <div class="text-[10px] text-gray-400 font-mono">
                                    @if($serverStats['ram_total'] !== '0 B')
                                        Libre: {{ $serverStats['ram_free'] }} / {{ $serverStats['ram_total'] }}
                                    @else
                                        No disponible en este SO
                                    @endif
                                </div>
                            </div>

                            {{-- CPU load --}}
                            <div class="space-y-1">
                                <div class="text-gray-500">Carga CPU (promedio 1m)</div>
                                <div class="text-lg font-bold text-gray-900 font-mono">
                                    {{ $serverStats['cpu'] }}
                                </div>
                            </div>

                            {{-- Databases connection statuses --}}
                            <div class="space-y-1">
                                <div class="text-gray-500 mb-1">Bases de datos</div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($serverStats['db_connections'] as $connName => $isOnline)
                                        <div class="flex items-center gap-1 bg-gray-50 border border-gray-100 rounded px-1.5 py-0.5 text-[9px] font-bold font-mono">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $isOnline ? 'bg-green-500' : 'bg-red-500 animate-pulse' }}"></span>
                                            {{ $connName }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] text-gray-400 font-mono">
                        <span>PHP {{ $serverStats['php_version'] }}</span>
                        <span>Laravel v{{ app()->version() }}</span>
                    </div>
                </div>
            </div>
            {{-- Radius Selector --}}
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span class="text-sm font-semibold text-gray-800">Comparativa de Precios con la Competencia</span>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <label for="radius" class="text-xs font-bold uppercase tracking-wider text-gray-500">Radio a la redonda:</label>
                    <select name="radius" id="radius" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-xs px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 font-medium text-slate-700 cursor-pointer">
                        <option value="30" @selected($selectedRadius == 30)>30 Kilómetros</option>
                        <option value="50" @selected($selectedRadius == 50)>50 Kilómetros</option>
                        <option value="100" @selected($selectedRadius == 100)>100 Kilómetros</option>
                    </select>
                </form>
            </div>

            {{-- Competitor Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($competitorsData as $id => $orig)
                    <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                    {{ $orig['station_name'] }}
                                </h3>
                                <span class="text-[10px] uppercase tracking-wider font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                    Competidores cercanos
                                </span>
                            </div>

                            <div class="space-y-3">
                                @forelse($orig['competitors'] as $comp)
                                    <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3.5 hover:bg-slate-50 transition duration-150">
                                        <div class="flex justify-between items-start gap-2 mb-2">
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-xs font-bold text-slate-800 truncate uppercase tracking-tight">
                                                    {{ $comp['name'] }}
                                                </h4>
                                                <p class="text-[10px] text-slate-400 truncate mt-0.5" title="{{ $comp['address'] }}">
                                                    {{ $comp['address'] }}
                                                </p>
                                            </div>
                                            <span class="shrink-0 inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 bg-indigo-50/80 px-2 py-0.5 rounded-full">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                {{ number_format($comp['distance'], 2, ',', '.') }} km
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2 text-xs border-t border-slate-100/60 pt-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Diesel:</span>
                                                <span class="font-mono font-black text-slate-700">
                                                    {{ $comp['diesel'] ? number_format($comp['diesel'], 3, ',', '.') . ' €' : '---' }}
                                                </span>
                                            </div>
                                            <div class="flex items-center justify-between pl-3 border-l border-slate-200/60">
                                                <span class="text-blue-500 text-[10px] uppercase font-bold tracking-wider">SP 95:</span>
                                                <span class="font-mono font-black text-blue-600">
                                                    {{ $comp['gas95'] ? number_format($comp['gas95'], 3, ',', '.') . ' €' : '---' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-gray-400 text-xs">
                                        No se encontraron gasolineras competidoras en un radio de {{ $selectedRadius }} km.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tempEl = document.getElementById('weather-temp');
            const descEl = document.getElementById('weather-desc');
            const windEl = document.getElementById('weather-wind');
            const locEl = document.getElementById('weather-loc');
            const iconContainer = document.getElementById('weather-icon-container');

            const searchInput = document.getElementById('weather-search-input');
            const searchContainer = document.getElementById('weather-search-container');
            const locBadge = document.getElementById('weather-loc-badge');
            const closeSearchBtn = document.getElementById('btn-search-close');
            const suggestionsEl = document.getElementById('weather-search-suggestions');

            // Setup search toggle
            locBadge.addEventListener('click', () => {
                searchContainer.classList.toggle('hidden');
                if (!searchContainer.classList.contains('hidden')) {
                    searchInput.focus();
                }
            });

            closeSearchBtn.addEventListener('click', () => {
                searchContainer.classList.add('hidden');
                searchInput.value = '';
                suggestionsEl.classList.add('hidden');
                suggestionsEl.innerHTML = '';
            });

            // Debounced geocoding search
            let debounceTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                const query = searchInput.value.trim();
                if (query.length < 3) {
                    suggestionsEl.classList.add('hidden');
                    suggestionsEl.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(query)}&count=5&language=es&format=json`)
                        .then(res => res.json())
                        .then(data => {
                            suggestionsEl.innerHTML = '';
                            if (data.results && data.results.length > 0) {
                                suggestionsEl.classList.remove('hidden');
                                data.results.forEach(result => {
                                    const item = document.createElement('button');
                                    item.className = 'w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors text-xs text-gray-700 flex flex-col border-b border-gray-100 last:border-0';
                                    
                                    const region = result.admin1 ? `, ${result.admin1}` : '';
                                    const nameText = `${result.name}${region}, ${result.country}`;
                                    
                                    item.innerHTML = `
                                        <span class="font-semibold text-gray-800">${result.name}</span>
                                        <span class="text-[10px] text-gray-500">${result.admin1 || ''} (${result.country})</span>
                                    `;
                                    
                                    item.addEventListener('click', () => {
                                        fetchWeather(result.latitude, result.longitude, nameText);
                                        localStorage.setItem('weather_lat', result.latitude);
                                        localStorage.setItem('weather_lon', result.longitude);
                                        localStorage.setItem('weather_name', nameText);
                                        
                                        searchContainer.classList.add('hidden');
                                        searchInput.value = '';
                                        suggestionsEl.classList.add('hidden');
                                        suggestionsEl.innerHTML = '';
                                    });
                                    suggestionsEl.appendChild(item);
                                });
                            } else {
                                suggestionsEl.classList.remove('hidden');
                                suggestionsEl.innerHTML = '<div class="px-4 py-2 text-gray-500 text-xs">No se encontraron resultados</div>';
                            }
                        })
                        .catch(err => {
                            console.error('Error fetching suggestions:', err);
                        });
                }, 300);
            });

            // Click outside to hide suggestions
            document.addEventListener('click', (e) => {
                if (searchContainer && !searchContainer.contains(e.target) && !locBadge.contains(e.target)) {
                    suggestionsEl.classList.add('hidden');
                }
            });

            const weatherCodeMap = {
                0: { descriptor: 'Cielo Despejado', icon: 'sun' },
                1: { descriptor: 'Principalmente Despejado', icon: 'sun-cloud' },
                2: { descriptor: 'Parcialmente Nublado', icon: 'cloud' },
                3: { descriptor: 'Cubierto', icon: 'cloud' },
                45: { descriptor: 'Niebla', icon: 'fog' },
                48: { descriptor: 'Niebla Escarchada', icon: 'fog' },
                51: { descriptor: 'Llovizna Ligera', icon: 'rain-light' },
                53: { descriptor: 'Llovizna Moderada', icon: 'rain-light' },
                55: { descriptor: 'Llovizna Densa', icon: 'rain-light' },
                61: { descriptor: 'Lluvia Débil', icon: 'rain' },
                63: { descriptor: 'Lluvia Moderada', icon: 'rain' },
                65: { descriptor: 'Lluvia Fuerte', icon: 'rain-heavy' },
                71: { descriptor: 'Nieve Ligera', icon: 'snow' },
                73: { descriptor: 'Nieve Moderada', icon: 'snow' },
                75: { descriptor: 'Nieve Fuerte', icon: 'snow' },
                80: { descriptor: 'Chubascos Ligeros', icon: 'rain-light' },
                81: { descriptor: 'Chubascos Moderados', icon: 'rain' },
                82: { descriptor: 'Chubascos Violentos', icon: 'rain-heavy' },
                95: { descriptor: 'Tormenta Eléctrica', icon: 'thunder' }
            };

            const icons = {
                sun: `<svg class="w-12 h-12 text-amber-500 animate-spin" style="animation-duration: 25s;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>`,
                'sun-cloud': `<svg class="w-12 h-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 20a6 6 0 0112 0v0H3z" stroke="gray"/></svg>`,
                cloud: `<svg class="w-12 h-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>`,
                rain: `<svg class="w-12 h-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/><path d="M9 20l-1 2M12 20l-1 2M15 20l-1 2" stroke-width="2" stroke-linecap="round"/></svg>`,
                'rain-light': `<svg class="w-12 h-12 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/><path d="M10 20l-1 2M14 20l-1 2" stroke-width="2" stroke-linecap="round"/></svg>`,
                'rain-heavy': `<svg class="w-12 h-12 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/><path d="M8 20l-1 2M11 20l-1 2M14 20l-1 2M17 20l-1 2" stroke-width="2" stroke-linecap="round"/></svg>`,
                thunder: `<svg class="w-12 h-12 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>`,
                fog: `<svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M3 9h18M7 15h10"/></svg>`,
                snow: `<svg class="w-12 h-12 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/><path d="M8 20h.01M12 20h.01M16 20h.01" stroke-width="3" stroke-linecap="round"/></svg>`
            };

            function fetchWeather(lat, lon, cityName, isFallback = false) {
                if (isNaN(lat) || isNaN(lon) || lat === null || lon === null) {
                    console.error('Coordenadas inválidas para clima:', lat, lon);
                    if (!isFallback) {
                        loadDefaultOrIPWeather();
                    } else {
                        showWeatherError();
                    }
                    return;
                }

                fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`)
                    .then(res => {
                        if (!res.ok) throw new Error('Response not ok');
                        return res.json();
                    })
                    .then(data => {
                        if (data && data.current_weather) {
                            const weather = data.current_weather;
                            const code = weather.weathercode;
                            const mapping = weatherCodeMap[code] || { descriptor: 'Despejado', icon: 'sun' };
                            
                            tempEl.textContent = weather.temperature.toFixed(1) + '°C';
                            descEl.textContent = mapping.descriptor;
                            windEl.textContent = 'Viento: ' + weather.windspeed + ' km/h';
                            locEl.textContent = cityName;
                            
                            // Remove animations/placeholders
                            tempEl.classList.remove('animate-pulse');
                            descEl.classList.remove('animate-pulse');
                            
                            const iconSvg = icons[mapping.icon] || icons.sun;
                            iconContainer.innerHTML = iconSvg;
                        } else {
                            throw new Error('No current weather data');
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching weather data:', err);
                        if (!isFallback) {
                            console.log('Intentando cargar clima de Sevilla como alternativa...');
                            fetchWeather(37.3891, -5.9845, 'Sevilla, España (Predeterminado)', true);
                        } else {
                            showWeatherError();
                        }
                    });
            }

            function showWeatherError() {
                tempEl.textContent = '--°C';
                descEl.textContent = 'Clima no disponible';
                windEl.textContent = 'Error de conexión';
                tempEl.classList.remove('animate-pulse');
                descEl.classList.remove('animate-pulse');
                iconContainer.innerHTML = `<svg class="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
            }

            function reverseGeocode(lat, lon, callback) {
                fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=es`)
                    .then(res => {
                        if (!res.ok) throw new Error('Geocode response not ok');
                        return res.json();
                    })
                    .then(data => {
                        const cityName = data.city || data.locality || data.principalSubdivision || 'Ubicación GPS';
                        const country = data.countryName ? `, ${data.countryName}` : '';
                        callback(`${cityName}${country}`);
                    })
                    .catch(() => {
                        callback('Ubicación GPS');
                    });
            }

            // Expose GPS function to global window scope so HTML onclick can call it
            window.useGPSLocation = function() {
                locEl.textContent = 'Solicitando GPS...';
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    reverseGeocode(lat, lon, (cityName) => {
                        fetchWeather(lat, lon, cityName);
                        localStorage.setItem('weather_lat', lat);
                        localStorage.setItem('weather_lon', lon);
                        localStorage.setItem('weather_name', cityName);
                    });
                }, err => {
                    console.error('Error getting GPS location:', err);
                    locEl.textContent = 'Error GPS';
                    setTimeout(loadDefaultOrIPWeather, 2000);
                });
            };

            function loadDefaultOrIPWeather() {
                // Get coordinates by IP Geolocation first
                fetch('https://ipapi.co/json/')
                    .then(res => {
                        if (!res.ok) throw new Error('IP lookup response not ok');
                        return res.json();
                    })
                    .then(data => {
                        if (data && data.latitude && data.longitude && !isNaN(parseFloat(data.latitude)) && !isNaN(parseFloat(data.longitude))) {
                            const cityName = data.city ? `${data.city}, ${data.country_name || 'España'}` : 'Ubicación actual';
                            fetchWeather(parseFloat(data.latitude), parseFloat(data.longitude), cityName);
                        } else {
                            // Fallback to Seville
                            fetchWeather(37.3891, -5.9845, 'Sevilla, España (Predeterminado)');
                        }
                    })
                    .catch(() => {
                        // Fallback to Seville if API is blocked or offline
                        fetchWeather(37.3891, -5.9845, 'Sevilla, España (Predeterminado)');
                    });
            }

            // Initialization logic
            const savedLat = localStorage.getItem('weather_lat');
            const savedLon = localStorage.getItem('weather_lon');
            const savedName = localStorage.getItem('weather_name');

            if (savedLat && savedLon && savedName && !isNaN(parseFloat(savedLat)) && !isNaN(parseFloat(savedLon))) {
                fetchWeather(parseFloat(savedLat), parseFloat(savedLon), savedName);
            } else {
                // Try precise GPS automatically first on initial load
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(position => {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        
                        reverseGeocode(lat, lon, (cityName) => {
                            fetchWeather(lat, lon, cityName);
                            localStorage.setItem('weather_lat', lat);
                            localStorage.setItem('weather_lon', lon);
                            localStorage.setItem('weather_name', cityName);
                        });
                    }, err => {
                        console.log('GPS auto-detection declined/failed, falling back to IP.', err);
                        loadDefaultOrIPWeather();
                    }, { timeout: 6000 });
                } else {
                    loadDefaultOrIPWeather();
                }
            }
        });
    </script>
</x-app-layout>
