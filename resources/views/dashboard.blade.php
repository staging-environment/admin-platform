<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Comparador de Precios de Competencia
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Análisis comparativo de precios de combustibles frente a competidores de la zona en tiempo real.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- WIDGET: PRECIOS DE FUTUROS DE COMBUSTIBLE (Investing.com ref.) --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            @if(!empty($futuresPrices))
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/60 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span class="text-xs font-extrabold text-gray-600 uppercase tracking-wider">Futuros de Combustible</span>
                        <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200">Mercado NY · cache 30min</span>
                    </div>
                    <span class="text-[10px] text-gray-400">Fuente: Yahoo Finance</span>
                </div>
                <div class="grid grid-cols-2 divide-x divide-gray-100">
                    @foreach($futuresPrices as $symbol => $future)
                    <div class="px-5 py-4 flex items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-base">{{ $future['icono'] }}</span>
                                <span class="text-xs font-bold text-gray-700">{{ $future['nombre'] }}</span>
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5 font-mono">{{ $symbol }} · {{ $future['unidad'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-black text-gray-800 font-mono leading-tight">
                                {{ number_format($future['precio'], 4, ',', '.') }}
                                <span class="text-xs font-normal text-gray-400">{{ $future['currency'] }}</span>
                            </div>
                            <div class="text-xs font-bold mt-0.5 {{ $future['positivo'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $future['positivo'] ? '+' : '' }}{{ number_format($future['cambio'], 4, ',', '.') }}
                                ({{ $future['positivo'] ? '+' : '' }}{{ number_format($future['cambioPct'], 2, ',', '.') }}%)
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            {{-- FIN WIDGET FUTUROS --}}

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


            {{-- Locality & Fuel Selector with Search --}}
            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-200 p-6">
                <form id="filterForm" method="GET" action="{{ route('dashboard') }}" class="w-full flex flex-col gap-6">
                    {{-- Row 1: Locality and Fuel (Centered side-by-side) --}}
                    <div class="flex flex-row gap-4 sm:gap-6 justify-center items-center">
                        {{-- Locality Selector --}}
                        <div class="flex flex-col gap-1 w-1/2 max-w-xs">
                            <label for="locality" class="text-xs sm:text-sm font-black uppercase tracking-wider text-blue-600 text-center block w-full">Localidad</label>
                            <select name="locality" id="locality" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 text-sm sm:text-base px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 font-extrabold text-slate-700 cursor-pointer text-center">
                                @foreach($localityMapping as $key => $loc)
                                    <option value="{{ $key }}" @selected($selectedLocality === $key)>{{ $loc['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sort By Selector --}}
                        <div class="flex flex-col gap-1 w-1/2 max-w-xs">
                            <label for="sort_by" class="text-xs sm:text-sm font-black uppercase tracking-wider text-blue-600 text-center block w-full">Combustible</label>
                            <select name="sort_by" id="sort_by" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 text-sm sm:text-base px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 font-extrabold text-slate-700 cursor-pointer text-center">
                                <option value="diesel" @selected($sortBy === 'diesel')>Diésel A</option>
                                <option value="gas95" @selected($sortBy === 'gas95')>Gasolina 95 E5</option>
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: Search Name (Large text input field) --}}
                    <div class="flex flex-col gap-1 w-full max-w-xl mx-auto">
                        <label for="search_name" class="text-sm sm:text-base font-black uppercase tracking-wider text-blue-600 text-center block w-full">Buscar por nombre de gasolinera</label>
                        <div class="relative w-full">
                            <input type="text" name="search_name" id="search_name" value="{{ $searchName }}" placeholder="Ej. Repsol, Cepsa, Petroprix..." class="w-full rounded-xl border-gray-200 text-base sm:text-lg pl-12 pr-4 py-3.5 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 font-extrabold text-slate-700 placeholder-slate-400 shadow-sm text-center">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </form>
            </div>


            {{-- Own Station Realtime Prices vs Locality --}}
            <div class="bg-gradient-to-br from-slate-50 to-blue-50 border border-slate-200/80 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Precios de Nuestra Estación (Tiempo Real)</h4>
                        <p class="text-xs text-slate-500">Datos obtenidos localmente de la base de datos de surtidores.</p>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-100/60 px-3 py-1 rounded-full border border-blue-200">
                        {{ $ourStationName }}
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl p-4 border border-slate-200 flex items-center justify-between shadow-xs">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Diésel A</span>
                            <span class="text-3xl font-black text-slate-800 font-mono">
                                {{ $ownDiesel ? number_format($ownDiesel, 3, ',', '.') . ' €' : '---' }}
                            </span>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-slate-200 flex items-center justify-between shadow-xs">
                        <div>
                            <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider block">Gasolina 95 E5</span>
                            <span class="text-3xl font-black text-blue-600 font-mono">
                                {{ $ownGas95 ? number_format($ownGas95, 3, ',', '.') . ' €' : '---' }}
                            </span>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    </div>
                </div>
            </div>

            {{-- Competitor Leaderboard List --}}
            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-base font-extrabold text-slate-800">
                        Listado de Gasolineras en {{ $localityMapping[$selectedLocality]['name'] }} (<span id="station-counter-badge">{{ count($filteredStations) }}</span>)
                    </h3>
                    <span class="text-xs text-slate-500">Ordenado de menor a mayor precio</span>
                </div>

                <div class="divide-y divide-gray-150">
                    <div id="no-stations-found" class="py-12 text-center text-gray-400 text-sm" style="display: none;">
                        No se encontraron gasolineras con ese nombre en esta localidad.
                    </div>

                    @forelse($filteredStations as $index => $station)
                        <div class="station-item p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 transition duration-150 {{ $station['is_ours'] ? 'bg-blue-50/70 border-l-4 border-blue-500' : 'hover:bg-slate-50/50' }}" data-name="{{ $station['name'] }}" data-address="{{ $station['address'] }} {{ $station['postal_code'] }} {{ $station['locality_name'] }}">
                            {{-- Station Info & Rank --}}
                            <div class="flex items-center gap-4 min-w-0 flex-1">
                                {{-- Rank Badge --}}
                                <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shadow-sm
                                    @if($index === 0) bg-amber-100 text-amber-800 border border-amber-200
                                    @elseif($index === 1) bg-slate-100 text-slate-700 border border-slate-200
                                    @elseif($index === 2) bg-amber-50 text-amber-700 border border-amber-100
                                    @else bg-gray-50 text-gray-500 border border-gray-100 @endif">
                                    #{{ $index + 1 }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-extrabold text-slate-800 uppercase tracking-tight truncate max-w-xs md:max-w-md">
                                            {{ $station['name'] }}
                                        </h4>
                                        @if($station['is_ours'])
                                            <span class="text-[9px] uppercase tracking-wider font-extrabold text-blue-600 bg-blue-100 px-2.5 py-0.5 rounded-full border border-blue-200 animate-pulse">
                                                Nuestra Estación
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-0.5">
                                        <p class="text-xs text-slate-500" title="{{ $station['address'] }} @if($station['postal_code'] || $station['locality_name']) ({{ $station['postal_code'] }} {{ $station['locality_name'] }}) @endif">
                                            {{ $station['address'] }}@if($station['postal_code'] || $station['locality_name']) <span class="text-slate-400">({{ $station['postal_code'] }} {{ $station['locality_name'] }})</span>@endif
                                        </p>
                                        @if($station['latitude'] && $station['longitude'])
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ $station['latitude'] }},{{ $station['longitude'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-0.5 text-[10px] font-extrabold uppercase tracking-wider text-blue-500 hover:text-blue-700 bg-blue-50/50 hover:bg-blue-50 px-2 py-0.5 rounded border border-blue-100/60 transition shadow-2xs" title="Ver ubicación en Google Maps">
                                                <svg class="h-3 w-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>Mapa</span>
                                            </a>
                                        @endif
                                    </div>
                                    @if($station['horario'])
                                        <div class="flex items-center gap-1 mt-1 text-[10px] font-medium text-slate-400">
                                            <svg class="h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Horario: {{ $station['horario'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Fuel Prices --}}
                            <div class="flex items-center gap-6 shrink-0 w-full md:w-auto justify-between md:justify-end border-t border-slate-100 pt-3 md:pt-0 md:border-0">
                                <div class="flex items-center gap-6">
                                    {{-- Diesel Price --}}
                                    <div class="text-right {{ $sortBy === 'diesel' ? 'bg-amber-50/60 p-2 rounded-lg border border-amber-100/50' : '' }}">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Diésel A</span>
                                        <span class="font-mono text-base font-black {{ $sortBy === 'diesel' ? 'text-amber-700' : 'text-slate-700' }}">
                                            {{ $station['diesel'] > 0 ? number_format($station['diesel'], 3, ',', '.') . ' €' : '---' }}
                                        </span>
                                    </div>

                                    {{-- Gasoline 95 Price --}}
                                    <div class="text-right {{ $sortBy === 'gas95' ? 'bg-blue-50/60 p-2 rounded-lg border border-blue-100/50' : '' }}">
                                        <span class="text-[9px] font-bold text-blue-400 uppercase tracking-wider block">SP 95 E5</span>
                                        <span class="font-mono text-base font-black {{ $sortBy === 'gas95' ? 'text-blue-600 font-extrabold' : 'text-slate-600' }}">
                                            {{ $station['gas95'] > 0 ? number_format($station['gas95'], 3, ',', '.') . ' €' : '---' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-400 text-sm">
                            No se encontraron gasolineras en la localidad de {{ $localityMapping[$selectedLocality]['name'] }}.
                        </div>
                    @endforelse
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('search_name');
                    const stationItems = document.querySelectorAll('.station-item');
                    const noResults = document.getElementById('no-stations-found');
                    
                    if (searchInput) {
                        // Normalize and filter function
                        const filterStations = function() {
                            const query = searchInput.value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                            let visibleCount = 0;
                            
                            stationItems.forEach(function(item) {
                                const name = (item.getAttribute('data-name') || '').toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                                const address = (item.getAttribute('data-address') || '').toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                                
                                if (name.includes(query) || address.includes(query)) {
                                    item.style.setProperty('display', 'flex', 'important');
                                    visibleCount++;
                                } else {
                                    item.style.setProperty('display', 'none', 'important');
                                }
                            });
                            
                            if (noResults) {
                                if (visibleCount === 0) {
                                    noResults.style.display = 'block';
                                } else {
                                    noResults.style.display = 'none';
                                }
                            }
                            
                            const counter = document.getElementById('station-counter-badge');
                            if (counter) {
                                counter.textContent = visibleCount;
                            }
                        };

                        // Apply filter on input
                        searchInput.addEventListener('input', filterStations);

                        // Also run it once on load to filter if there's an initial searchName value from backend
                        if (searchInput.value.trim() !== '') {
                            filterStations();
                        }
                    }
                });
            </script>
</x-app-layout>
