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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                Resumen mensual de ventas
                            </h3>

                            <p class="mt-1 text-sm text-gray-600">
                                Basado en la tabla facturasyticketsdeventa usando FechaYHora e ImporteTotal.
                            </p>
                        </div>

                        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col gap-3 xl:flex-row xl:items-end">
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">
                                    Año
                                </label>

                                <input
                                    type="number"
                                    id="year"
                                    name="year"
                                    value="{{ $selectedYear }}"
                                    min="2000"
                                    max="{{ date('Y') + 1 }}"
                                    class="mt-1 w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700">
                                    Tipo
                                </label>

                                <select
                                    id="document_type"
                                    name="document_type"
                                    class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="all" @selected(($selectedDocumentType ?? 'all') === 'all')>
                                        Todos
                                    </option>

                                    <option value="invoices" @selected(($selectedDocumentType ?? 'all') === 'invoices')>
                                        Facturas
                                    </option>

                                    <option value="tickets" @selected(($selectedDocumentType ?? 'all') === 'tickets')>
                                        Tickets
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="start_month" class="block text-sm font-medium text-gray-700">
                                    Mes desde
                                </label>

                                <select
                                    id="start_month"
                                    name="start_month"
                                    class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">
                                        Todos
                                    </option>

                                    @foreach($months as $monthNumber => $monthName)
                                        <option value="{{ $monthNumber }}" @selected(($selectedStartMonth ?? null) === $monthNumber)>
                                            {{ $monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="end_month" class="block text-sm font-medium text-gray-700">
                                    Mes hasta
                                </label>

                                <select
                                    id="end_month"
                                    name="end_month"
                                    class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">
                                        Todos
                                    </option>

                                    @foreach($months as $monthNumber => $monthName)
                                        <option value="{{ $monthNumber }}" @selected(($selectedEndMonth ?? null) === $monthNumber)>
                                            {{ $monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="station_code" class="block text-sm font-medium text-gray-700">
                                    Estación
                                </label>

                                <select
                                    id="station_code"
                                    name="station_code"
                                    class="mt-1 max-w-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">
                                        Todas
                                    </option>

                                    @foreach($stations as $station)
                                        <option value="{{ $station['code'] }}" @selected(($selectedStationCode ?? null) === $station['code'])>
                                            {{ $station['name'] }}
                                            @if(!empty($station['town']))
                                                - {{ $station['town'] }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                            >
                                Filtrar
                            </button>
                        </form>
                    </div>

                    @if(count($monthlySales) > 0)
                        <div class="mb-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-800">
                                            Evolución mensual
                                        </h4>

                                        <p class="text-xs text-gray-500">
                                            Total vendido por mes
                                        </p>
                                    </div>
                                </div>

                                <div class="h-80">
                                    <canvas id="monthlySalesChart"></canvas>
                                </div>
                            </div>

                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-800 mb-4">
                                    Resumen del año {{ $selectedYear }}
                                </h4>

                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-gray-500">
                                            Total vendido
                                        </p>

                                        <p class="mt-1 text-2xl font-bold text-gray-900">
                                            {{ number_format(collect($monthlySales)->sum('total_amount'), 2, ',', '.') }} €
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-gray-500">
                                            Documentos
                                        </p>

                                        <p class="mt-1 text-2xl font-bold text-gray-900">
                                            {{ number_format(collect($monthlySales)->sum('documents_count'), 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-gray-500">
                                            IVA
                                        </p>

                                        <p class="mt-1 text-2xl font-bold text-gray-900">
                                            {{ number_format(collect($monthlySales)->sum('tax_amount'), 2, ',', '.') }} €
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-gray-500">
                                            Ticket medio aproximado
                                        </p>

                                        <p class="mt-1 text-2xl font-bold text-gray-900">
                                            @php
                                                $totalDocuments = collect($monthlySales)->sum('documents_count');
                                                $totalAmount = collect($monthlySales)->sum('total_amount');
                                                $averageTicket = $totalDocuments > 0 ? $totalAmount / $totalDocuments : 0;
                                            @endphp

                                            {{ number_format($averageTicket, 2, ',', '.') }} €
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mes
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Documentos
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bruto
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descuentos / cargos
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        IVA
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($monthlySales as $month)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ $month['month_name'] }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 text-right">
                                            {{ number_format($month['documents_count'], 0, ',', '.') }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 text-right">
                                            {{ number_format($month['gross_amount'], 2, ',', '.') }} €
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 text-right">
                                            {{ number_format($month['discounts_and_charges_amount'], 2, ',', '.') }} €
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 text-right">
                                            {{ number_format($month['tax_amount'], 2, ',', '.') }} €
                                        </td>

                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                            {{ number_format($month['total_amount'], 2, ',', '.') }} €
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                                <tfoot class="bg-gray-50">
                                <tr>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                        Total año {{ $selectedYear }}
                                    </td>

                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                        {{ number_format(collect($monthlySales)->sum('documents_count'), 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                        {{ number_format(collect($monthlySales)->sum('gross_amount'), 2, ',', '.') }} €
                                    </td>

                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                        {{ number_format(collect($monthlySales)->sum('discounts_and_charges_amount'), 2, ',', '.') }} €
                                    </td>

                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                        {{ number_format(collect($monthlySales)->sum('tax_amount'), 2, ',', '.') }} €
                                    </td>

                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                        {{ number_format(collect($monthlySales)->sum('total_amount'), 2, ',', '.') }} €
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded">
                            No hay ventas localizadas para el año {{ $selectedYear }} con los filtros seleccionados.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @if(count($monthlySales) > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const monthlySalesData = @json($monthlySales);

                const labels = monthlySalesData.map(function (item) {
                    return item.month_name;
                });

                const totals = monthlySalesData.map(function (item) {
                    return item.total_amount;
                });

                const grossAmounts = monthlySalesData.map(function (item) {
                    return item.gross_amount;
                });

                const taxAmounts = monthlySalesData.map(function (item) {
                    return item.tax_amount;
                });

                const canvas = document.getElementById('monthlySalesChart');

                if (!canvas || typeof Chart === 'undefined') {
                    return;
                }

                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                type: 'bar',
                                label: 'Total vendido',
                                data: totals,
                                backgroundColor: 'rgba(37, 99, 235, 0.75)',
                                borderColor: 'rgba(37, 99, 235, 1)',
                                borderWidth: 1,
                                borderRadius: 6,
                            },
                            {
                                type: 'line',
                                label: 'Bruto',
                                data: grossAmounts,
                                borderColor: 'rgba(16, 185, 129, 1)',
                                backgroundColor: 'rgba(16, 185, 129, 0.15)',
                                borderWidth: 2,
                                tension: 0.35,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            },
                            {
                                type: 'line',
                                label: 'IVA',
                                data: taxAmounts,
                                borderColor: 'rgba(245, 158, 11, 1)',
                                backgroundColor: 'rgba(245, 158, 11, 0.15)',
                                borderWidth: 2,
                                tension: 0.35,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const value = context.parsed.y || 0;

                                        return context.dataset.label + ': ' + new Intl.NumberFormat('es-ES', {
                                            style: 'currency',
                                            currency: 'EUR',
                                        }).format(value);
                                    },
                                },
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return new Intl.NumberFormat('es-ES', {
                                            style: 'currency',
                                            currency: 'EUR',
                                            maximumFractionDigits: 0,
                                        }).format(value);
                                    },
                                },
                            },
                        },
                    },
                });
            });
        </script>
    @endif
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

            function fetchWeather(lat, lon, cityName) {
                fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`)
                    .then(res => res.json())
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
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching weather data:', err);
                    });
            }

            function reverseGeocode(lat, lon, callback) {
                fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=es`)
                    .then(res => res.json())
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
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.latitude && data.longitude) {
                            const cityName = data.city ? `${data.city}, ${data.country_name || 'España'}` : 'Ubicación actual';
                            fetchWeather(data.latitude, data.longitude, cityName);
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

            if (savedLat && savedLon && savedName) {
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
