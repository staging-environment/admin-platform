<x-filament-panels::page>


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- ESTILOS INLINE DEL DASHBOARD                                       --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <style>
        /* ── Tarjeta Gasoil (Negro) ─────────────────────────────── */
        .market-card-black {
            background: linear-gradient(145deg, #06080f 0%, #0d1117 40%, #111827 100%);
            border: 1px solid rgba(251,191,36,0.12);
            position: relative;
            overflow: hidden;
        }
        .market-card-black::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(251,191,36,0.07) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .market-card-black::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(251,191,36,0.4), transparent);
        }

        /* ── Tarjeta RBOB (Verde) ───────────────────────────────── */
        .market-card-green {
            background: linear-gradient(145deg, #052e16 0%, #064e3b 40%, #065f46 100%);
            border: 1px solid rgba(52,211,153,0.15);
            position: relative;
            overflow: hidden;
        }
        .market-card-green::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(52,211,153,0.10) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .market-card-green::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(52,211,153,0.5), transparent);
        }

        /* ── Animación blink al actualizar precio ───────────────── */
        @keyframes pricePulse {
            0%   { opacity: 1; transform: scale(1); }
            40%  { opacity: 0.55; transform: scale(0.97); }
            100% { opacity: 1; transform: scale(1); }
        }
        .price-blink { animation: pricePulse 0.45s ease-in-out; }

        /* ── Animación blink para precios locales ───────────────── */
        @keyframes localPricePulse {
            0%   { opacity: 1; transform: scale(1); }
            50%  { opacity: 0.55; transform: scale(0.97); }
            100% { opacity: 1; transform: scale(1); }
        }
        .local-price-blink { animation: localPricePulse 0.6s ease-in-out; }

        /* ── Indicador de variación ─────────────────────────────── */
        .change-badge-up   { color: #34d399; }
        .change-badge-down { color: #f87171; }
        .change-badge-null { color: #6b7280; }

        /* ── Filas de estaciones de competencia ─────────────────── */
        .station-row {
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            padding: 5px 7px;
            transition: background 0.15s;
        }
        .station-row:hover { background: rgba(0,0,0,0.04); }
        .station-row.rank-1 {
            background: rgba(0,0,0,0.035);
        }
        .rank-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 19px;
            height: 19px;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 800;
            flex-shrink: 0;
            line-height: 1;
        }

        /* ── Spinner de carga ───────────────────────────────────── */
        .market-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.15);
            border-top-color: rgba(255,255,255,0.7);
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    <div class="space-y-7" wire:poll.30s="loadData">

        @if(auth()->user() && (auth()->user()->hasRole('Admin') || auth()->user()->id === 1 || auth()->user()->email === 'jarodriguezbonilla@gmail.com'))
            <section class="rounded-xl px-4 py-2.5 shadow-md border" 
                     style="background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%); border-color: rgba(99, 102, 241, 0.15); position: relative; overflow: hidden;">
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 text-xs relative z-10">
                    {{-- Left side: Status badge & timestamp --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm">🤖</span>
                            <span class="font-bold text-white">Bot MITECO <span class="text-[10px] font-normal lowercase opacity-60" style="color:#94a3b8; margin-left: 2px;">(comprobación cada 5 min)</span>:</span>
                        </div>
                        
                        @if($mitecoLastUpdate)
                            @if(($mitecoLastUpdate['status'] ?? '') === 'success')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" 
                                      style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
                                    ● ÉXITO
                                </span>
                                @if(isset($mitecoLastUpdate['data']['numRegistro']))
                                    <a href="{{ $mitecoLastUpdate['data']['urlComprobante'] ?? '#' }}" target="_blank" 
                                       class="text-[10px] font-bold text-indigo-400 hover:underline">
                                        Reg: {{ $mitecoLastUpdate['data']['numRegistro'] }} ↗
                                    </a>
                                @endif
                            @elseif(($mitecoLastUpdate['status'] ?? '') === 'skipped')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" 
                                      style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3);"
                                      title="{{ $mitecoLastUpdate['message'] ?? '' }}">
                                    ● OMITIDO (SIN CAMBIOS)
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" 
                                      style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);">
                                    ● FALLIDO
                                </span>
                            @endif
                            
                            <span class="text-gray-400 text-[11px]">
                                Última ejecución: <strong class="text-gray-200">{{ \Carbon\Carbon::parse($mitecoLastUpdate['timestamp'])->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}</strong>
                            </span>
                        @else
                            <span class="text-gray-400">Sin registros de ejecución previos.</span>
                        @endif
                    </div>

                    {{-- Right side: Verified Prices --}}
                    @if($mitecoLastUpdate && isset($mitecoLastUpdate['prices']))
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] border-t lg:border-t-0 lg:border-l border-gray-700 pt-2 lg:pt-0 lg:pl-4">
                            <span class="text-[9px] font-bold uppercase text-indigo-300 tracking-wider">Últimos Precios:</span>
                            @foreach($mitecoLastUpdate['prices'] as $code => $station)
                                <div class="flex items-center gap-1.5 bg-slate-800/40 px-2 py-0.5 rounded border border-slate-700/30">
                                    <span class="text-gray-400 font-semibold" style="font-size: 10px;">{{ str_replace(['E.S. ', ' (Utrera)', ' (Sevilla)', ' (El Cuervo)', ' (Lebrija)'], '', $station['name']) }}:</span>
                                    <span class="text-gray-200 font-mono" title="Gasóleo A">⛽{{ $station['goa'] ? number_format($station['goa'], 3, ',', '') : '—' }}</span>
                                    <span class="text-emerald-400 font-mono" title="Gasolina 95 E5">🟢{{ $station['g95e5'] ? number_format($station['g95e5'], 3, ',', '') : '—' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @php
            $showContactAlerts = auth()->user()->hasRole('Admin') || auth()->user()->id === 1 || auth()->user()->email === 'jarodriguezbonilla@gmail.com' || auth()->user()->can('ver_dashboard');
            $showJobAlerts = auth()->user()->hasRole('Admin') || auth()->user()->id === 1 || auth()->user()->email === 'jarodriguezbonilla@gmail.com' || auth()->user()->can('gestion_recursos_humanos');
            
            $unreadMessages = $showContactAlerts ? \App\Models\ContactoMensaje::with('gasolinera')->where('is_read', false)->orderBy('created_at', 'desc')->get() : collect();
            $unreadApplications = $showJobAlerts ? \App\Models\JobApplication::with('jobOffer')->where('is_read', false)->orderBy('created_at', 'desc')->get() : collect();
        @endphp

        @if($unreadMessages->count() > 0 || $unreadApplications->count() > 0)
            <div class="flex flex-col gap-3">
                {{-- Contact Messages Banners (Red) --}}
                @foreach($unreadMessages as $msg)
                    <div class="bg-red-600 rounded-xl p-3 shadow-md flex flex-col md:flex-row md:items-center justify-between text-white border border-red-700" style="background-color: #dc2626; border-color: rgba(220, 38, 38, 0.4);">
                        <div class="flex items-center gap-3 mb-2.5 md:mb-0">
                            <div class="bg-white/20 p-1.5 rounded-full flex-shrink-0">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-wider text-white leading-tight">
                                    @if($msg->gasolinera)
                                        ¡Nuevo mensaje para {{ $msg->gasolinera->Nombre }}!
                                    @else
                                        ¡Nuevo mensaje de contacto principal!
                                    @endif
                                </h3>
                                <p class="text-[11px] text-red-100 mt-0.5">
                                    De: <strong class="text-white">{{ $msg->nombre }}</strong> ({{ $msg->email }}) · Hace {{ str_replace('hace ', '', $msg->created_at->diffForHumans()) }}
                                </p>
                            </div>
                        </div>
                        
                        <a href="{{ route('filament.admin.resources.contacto-mensajes.view', ['record' => $msg->id]) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-white text-red-600 rounded-lg font-bold shadow hover:bg-gray-50 transition-all text-xs flex-shrink-0" style="color: #dc2626;">
                            LEER MENSAJE
                        </a>
                    </div>
                @endforeach

                {{-- Job Applications Banners (Blue) --}}
                @foreach($unreadApplications as $app)
                    <div class="bg-blue-600 rounded-xl p-3 shadow-md flex flex-col md:flex-row md:items-center justify-between text-white border border-blue-700" style="background-color: #2563eb; border-color: rgba(37, 99, 235, 0.4);">
                        <div class="flex items-center gap-3 mb-2.5 md:mb-0">
                            <div class="bg-white/20 p-1.5 rounded-full flex-shrink-0">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-wider text-white leading-tight">
                                    ¡Nueva inscripción a: {{ $app->jobOffer->title ?? 'Oferta de empleo' }}!
                                </h3>
                                <p class="text-[11px] text-blue-100 mt-0.5">
                                    Candidato: <strong class="text-white">{{ $app->first_name }} {{ $app->last_name }}</strong> ({{ $app->email }}) · Hace {{ str_replace('hace ', '', $app->created_at->diffForHumans()) }}
                                </p>
                            </div>
                        </div>
                        
                        <a href="{{ route('filament.admin.resources.job-applications.view', ['record' => $app->id]) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-white text-blue-600 rounded-lg font-bold shadow hover:bg-gray-50 transition-all text-xs flex-shrink-0" style="color: #2563eb;">
                            VER CANDIDATURA
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- ZONA SUPERIOR: MERCADOS ENERGÉTICOS INTERNACIONALES            --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}

        <section>
            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-1 h-5 rounded-full" style="background:linear-gradient(180deg,#fbbf24,#f59e0b)"></div>
                <h2 class="text-xs font-bold tracking-widest uppercase" style="color:#9ca3af">Mercados Energéticos <span class="text-[10px] font-normal lowercase opacity-60" style="color:#9ca3af; margin-left: 4px;">(actualización cada 3s)</span></h2>
                <span class="ml-auto flex items-center gap-1.5 text-xs" style="color:#6b7280">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                    Actualización en tiempo real · Yahoo Finance
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- ── TARJETA 1: GASOIL BRENT LONDRES (BZ=F) ─────────── --}}
                <div class="market-card-black rounded-2xl px-5 py-3 shadow-2xl" id="card-gasoil">
                    <div class="flex items-center justify-between gap-4">
                        {{-- Izquierda: Título e Icono --}}
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.15)">
                                <svg class="w-4 h-4" style="color:#fbbf24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-white leading-tight">Gasoil Londres (ICE)</h3>
                                <div class="flex items-center gap-1 mt-1 font-mono text-[9px] px-1.5 py-0.5 rounded border" style="background:rgba(255,255,255,0.07); border-color:rgba(251,191,36,0.25); color:#fbbf24; width: fit-content; line-height: 1;">
                                    <span style="font-size: 8px; text-transform: uppercase; opacity: 0.9; color: rgba(255,255,255,0.9);">Actualizado:</span>
                                    <span id="gasoil-updated" class="tabular-nums font-bold">
                                        {{ $gasoilData['updated_at'] ?? '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Derecha: Precio y Variación (Formato Investing) --}}
                        <div class="text-right flex flex-col justify-center items-end">
                            <div class="flex items-baseline justify-end gap-1">
                                <span id="gasoil-price" class="font-black text-white tabular-nums" style="font-size:1.75rem;line-height:1;letter-spacing:-0.02em">
                                    @if($gasoilData['price'])
                                        {{ number_format($gasoilData['price'], 4, '.', ',') }}
                                    @else
                                        <span class="market-spinner" style="width:14px;height:14px;"></span>
                                    @endif
                                </span>
                                <span class="text-xs font-bold" style="color:rgba(255,255,255,0.4)">USD/t</span>
                            </div>

                            <div class="flex items-center justify-end gap-1.5 mt-1 font-bold" id="gasoil-change-row" style="font-size: 0.9rem;">
                                <span id="gasoil-arrow" class="hidden"></span>
                                <span id="gasoil-change" class="text-xs font-semibold opacity-90 tabular-nums {{ $gasoilData['is_up'] === true ? 'change-badge-up' : ($gasoilData['is_up'] === false ? 'change-badge-down' : 'change-badge-null') }}">
                                    @if($gasoilData['change'] !== null)
                                        {{ ($gasoilData['change'] >= 0 ? '+' : '') . number_format($gasoilData['change'], 4, '.', ',') }}
                                    @else ---
                                    @endif
                                </span>
                                <span id="gasoil-pct" class="font-black tabular-nums {{ $gasoilData['is_up'] === true ? 'change-badge-up' : ($gasoilData['is_up'] === false ? 'change-badge-down' : 'change-badge-null') }}">
                                    @if($gasoilData['change_pct'] !== null)
                                        @if($gasoilData['is_up'] === true)
                                            🟢 <strong>+{{ number_format($gasoilData['change_pct'], 2, '.', ',') }}%</strong>
                                        @elseif($gasoilData['is_up'] === false)
                                            🔴 <strong>{{ number_format($gasoilData['change_pct'], 2, '.', ',') }}%</strong>
                                        @else
                                            <strong>0.00%</strong>
                                        @endif
                                    @else (--%)
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── TARJETA 2: GASOLINA RBOB (RB=F) ────────────────── --}}
                <div class="market-card-green rounded-2xl px-5 py-3 shadow-2xl" id="card-rbob">
                    <div class="flex items-center justify-between gap-4">
                        {{-- Izquierda: Título e Icono --}}
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.15)">
                                <svg class="w-4 h-4" style="color:#34d399" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-white leading-tight">Gasolina RBOB (NYMEX)</h3>
                                <div class="flex items-center gap-1 mt-1 font-mono text-[9px] px-1.5 py-0.5 rounded border" style="background:rgba(255,255,255,0.07); border-color:rgba(52,211,153,0.25); color:#34d399; width: fit-content; line-height: 1;">
                                    <span style="font-size: 8px; text-transform: uppercase; opacity: 0.9; color: rgba(255,255,255,0.9);">Actualizado:</span>
                                    <span id="rbob-updated" class="tabular-nums font-bold">
                                        {{ $rbobData['updated_at'] ?? '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Derecha: Precio y Variación (Formato Investing) --}}
                        <div class="text-right flex flex-col justify-center items-end">
                            <div class="flex items-baseline justify-end gap-1">
                                <span id="rbob-price" class="font-black text-white tabular-nums" style="font-size:1.75rem;line-height:1;letter-spacing:-0.02em">
                                    @if($rbobData['price'])
                                        {{ number_format($rbobData['price'], 4, '.', ',') }}
                                    @else
                                        <span class="market-spinner" style="width:14px;height:14px;"></span>
                                    @endif
                                </span>
                                <span class="text-xs font-bold" style="color:rgba(255,255,255,0.4)">USD/gal</span>
                            </div>

                            <div class="flex items-center justify-end gap-1.5 mt-1 font-bold" id="rbob-change-row" style="font-size: 0.9rem;">
                                <span id="rbob-arrow" class="hidden"></span>
                                <span id="rbob-change" class="text-xs font-semibold opacity-90 tabular-nums" style="color:{{ $rbobData['is_up'] === true ? '#6ee7b7' : ($rbobData['is_up'] === false ? '#fca5a5' : 'rgba(52,211,153,0.5)') }}">
                                    @if($rbobData['change'] !== null)
                                        {{ ($rbobData['change'] >= 0 ? '+' : '') . number_format($rbobData['change'], 4, '.', ',') }}
                                    @else ---
                                    @endif
                                </span>
                                <span id="rbob-pct" class="font-black tabular-nums {{ $rbobData['is_up'] === true ? 'change-badge-up' : ($rbobData['is_up'] === false ? 'change-badge-down' : 'change-badge-null') }}">
                                    @if($rbobData['change_pct'] !== null)
                                        @if($rbobData['is_up'] === true)
                                            🟢 <strong>+{{ number_format($rbobData['change_pct'], 2, '.', ',') }}%</strong>
                                        @elseif($rbobData['is_up'] === false)
                                            🔴 <strong>{{ number_format($rbobData['change_pct'], 2, '.', ',') }}%</strong>
                                        @else
                                            <strong>0.00%</strong>
                                        @endif
                                    @else (--%)
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- ZONA INFERIOR: COMPETENCIA LOCAL                               --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}

        <section>
            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-1 h-5 rounded-full" style="background:linear-gradient(180deg,#60a5fa,#3b82f6)"></div>
                <h2 class="text-xs font-bold tracking-widest uppercase" style="color:#9ca3af">Competencia Local <span class="text-[10px] font-normal lowercase opacity-60" style="color:#9ca3af; margin-left: 4px;">(consulta cada 15 min)</span></h2>
                <span class="ml-auto text-xs" style="color:#6b7280">
                    Datos oficiales · Ministerio para la Transición Ecológica (MITECO)
                </span>
            </div>

            <div id="competitors-container">
                @include('filament.pages.competitors-list', ['localityData' => $localityData])
            </div>
        </section>

    </div>{{-- /space-y-7 --}}

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- JAVASCRIPT — AJAX POLLING DE MERCADOS (cada 3 segundos)           --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <script>
    (function () {
        'use strict';

        const MARKETS_URL    = '{{ route("admin.fuel.markets") }}';
        const POLL_INTERVAL  = 3000; // 3 segundos

        /* ─── Helpers de formateo ──────────────────────────────────────── */
        function fmt(value, decimals) {
            if (value === null || value === undefined) return '---';
            var n = parseFloat(value).toFixed(decimals);
            // Separador de miles
            var parts = n.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.join('.');
        }

        function fmtChange(value, decimals) {
            if (value === null || value === undefined) return '---';
            var sign = parseFloat(value) >= 0 ? '+' : '';
            return sign + parseFloat(value).toFixed(decimals);
        }

        /* ─── Actualizar tarjeta genérica ──────────────────────────────── */
        function updateCard(prefix, data, priceDecimals, changeDecimals) {
            if (!data) return;

            var elPrice   = document.getElementById(prefix + '-price');
            var elChange  = document.getElementById(prefix + '-change');
            var elPct     = document.getElementById(prefix + '-pct');
            var elArrow   = document.getElementById(prefix + '-arrow');
            var elUpdated = document.getElementById(prefix + '-updated');

            /* Precio */
            if (elPrice && data.price !== null) {
                // Si es RBOB y viene en centavos (>10), lo dividimos entre 100
                var finalPrice = parseFloat(data.price);
                if (prefix === 'rbob' && finalPrice > 10.0) {
                    finalPrice = finalPrice / 100.0;
                }
                elPrice.textContent = fmt(finalPrice, priceDecimals);
                elPrice.classList.add('price-blink');
                setTimeout(function () { elPrice.classList.remove('price-blink'); }, 500);
            }

            /* Variación absoluta */
            if (elChange && data.change !== null) {
                var finalChange = parseFloat(data.change);
                if (prefix === 'rbob' && Math.abs(finalChange) > 5.0) {
                    finalChange = finalChange / 100.0;
                }
                elChange.textContent = fmtChange(finalChange, changeDecimals);
                var upColor   = prefix === 'gasoil' ? '#34d399' : '#6ee7b7';
                var downColor = prefix === 'gasoil' ? '#f87171' : '#fca5a5';
                var nullColor = prefix === 'gasoil' ? '#6b7280' : 'rgba(52,211,153,0.5)';
                elChange.style.color = data.is_up === true ? upColor : (data.is_up === false ? downColor : nullColor);
            }

            /* Variación % */
            if (elPct && data.change_pct !== null) {
                var pctVal = parseFloat(data.change_pct);
                var upColor2   = prefix === 'gasoil' ? '#34d399' : '#6ee7b7';
                var downColor2 = prefix === 'gasoil' ? '#f87171' : '#fca5a5';
                var nullColor2 = prefix === 'gasoil' ? '#6b7280' : 'rgba(52,211,153,0.5)';

                elPct.style.color = data.is_up === true ? upColor2 : (data.is_up === false ? downColor2 : nullColor2);
                elPct.style.fontWeight = 'bold';

                if (data.is_up === true) {
                    elPct.textContent = '🟢 +' + pctVal.toFixed(2) + '%';
                } else if (data.is_up === false) {
                    elPct.textContent = '🔴 ' + pctVal.toFixed(2) + '%';
                } else {
                    elPct.textContent = '0.00%';
                }
            }

            /* Flecha (Hidden but maintained just in case) */
            if (elArrow) {
                elArrow.textContent = '';
            }

            /* Timestamp */
            if (elUpdated && data.updated_at) {
                elUpdated.textContent = data.updated_at;
            }
        }

        /* ─── Fetch y actualización ─────────────────────────────────────── */
        function fetchMarkets() {
            fetch(MARKETS_URL, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                updateCard('gasoil', data.gasoil, 4, 4);
                updateCard('rbob',   data.rbob,   4, 4);
            })
            .catch(function (err) {
                console.warn('[Dashboard] Error fetching market data:', err);
            });
        }

        /* Ejecutar de inmediato y luego en bucle */
        fetchMarkets();
        setInterval(fetchMarkets, POLL_INTERVAL);

        /* Helpers to build DOM elements programmatically (bypassing Trusted Types CSP restrictions) */
        function createStationRow(rank, station, fuelType) {
            var row = document.createElement('div');
            row.className = 'station-row' + (rank === 0 ? ' rank-1' : '');
            if (fuelType === 'gas95' && rank === 0) {
                row.style.background = 'rgba(22,163,74,0.05)';
            }

            var chip = document.createElement('span');
            chip.className = 'rank-chip text-white';
            var chipBg = '';
            if (fuelType === 'diesel') {
                chipBg = rank === 0 ? '#111827' : (rank === 1 ? '#374151' : (rank === 2 ? '#4b5563' : '#6b7280'));
            } else {
                chipBg = rank === 0 ? '#15803d' : (rank === 1 ? '#16a34a' : (rank === 2 ? '#22c55e' : '#4ade80'));
            }
            chip.style.backgroundColor = chipBg;
            chip.textContent = rank + 1;
            row.appendChild(chip);

            var infoCol = document.createElement('div');
            infoCol.className = 'flex-1 min-w-0';

            var link = document.createElement('a');
            var mapQuery = encodeURIComponent(station.name + ', ' + station.address);
            link.href = 'https://www.google.com/maps/search/?api=1&query=' + mapQuery;
            link.target = '_blank';
            link.className = 'hover:underline block group';
            link.title = 'Ver en Google Maps';

            var nameP = document.createElement('p');
            nameP.className = 'font-bold truncate leading-tight dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400';
            nameP.style.fontSize = '11px';
            nameP.style.color = '#1f2937';
            nameP.textContent = station.name.length > 24 ? station.name.substring(0, 21) + '...' : station.name;

            var addrP = document.createElement('p');
            addrP.className = 'truncate leading-none dark:text-gray-400 mt-0.5';
            addrP.style.fontSize = '9px';
            addrP.style.color = '#6b7280';
            addrP.textContent = station.address.length > 30 ? station.address.substring(0, 27) + '...' : station.address;

            link.appendChild(nameP);
            link.appendChild(addrP);
            infoCol.appendChild(link);
            row.appendChild(infoCol);

            var priceSpan = document.createElement('span');
            priceSpan.className = 'font-black tabular-nums whitespace-nowrap local-price-blink';
            priceSpan.style.fontSize = '12px';
            var priceColor = '';
            if (fuelType === 'diesel') {
                priceColor = rank === 0 ? '#111827' : '#374151';
            } else {
                priceColor = rank === 0 ? '#15803d' : '#16a34a';
            }
            priceSpan.style.color = priceColor;
            
            var formattedPrice = parseFloat(station.price).toFixed(3).replace('.', ',');
            priceSpan.textContent = formattedPrice + ' \u20AC';
            
            row.appendChild(priceSpan);
            return row;
        }

        function createEmptyState() {
            var div = document.createElement('div');
            div.className = 'flex flex-col items-center justify-center py-6 gap-1.5';
            
            var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('class', 'w-6 h-6');
            svg.setAttribute('style', 'color:#d1d5db');
            svg.setAttribute('fill', 'none');
            svg.setAttribute('viewBox', '0 0 24 24');
            svg.setAttribute('stroke', 'currentColor');
            
            var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('stroke-linecap', 'round');
            path.setAttribute('stroke-linejoin', 'round');
            path.setAttribute('stroke-width', '1.5');
            path.setAttribute('d', 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z');
            svg.appendChild(path);
            
            var span = document.createElement('span');
            span.className = 'text-xs';
            span.style.color = '#9ca3af';
            span.textContent = 'Sin datos disponibles';
            
            div.appendChild(svg);
            div.appendChild(span);
            return div;
        }

        const COMPETITORS_URL = '{{ route("admin.competitor.data") }}';
        function fetchCompetitors() {
            fetch(COMPETITORS_URL, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                if (!data || !data.localities) return;
                
                Object.keys(data.localities).forEach(function (key) {
                    var locality = data.localities[key];
                    
                    // Update timestamp
                    var timeEl = document.getElementById('updated-time-' + key);
                    if (timeEl) {
                        var text = '';
                        if (locality.checked_at) {
                            text = 'Última comprobación: ' + locality.checked_at;
                        } else if (locality.updated_at) {
                            text = 'Última comprobación: ' + locality.updated_at;
                        }
                        timeEl.textContent = text;
                    }
                    
                    // Update diesel rows
                    var dieselContainer = document.getElementById('rows-' + key + '-diesel');
                    if (dieselContainer) {
                        dieselContainer.textContent = '';
                        if (locality.diesel && locality.diesel.length > 0) {
                            locality.diesel.forEach(function (station, rank) {
                                dieselContainer.appendChild(createStationRow(rank, station, 'diesel'));
                            });
                        } else {
                            dieselContainer.appendChild(createEmptyState());
                        }
                    }
                    
                    // Update gas95 rows
                    var gas95Container = document.getElementById('rows-' + key + '-gas95');
                    if (gas95Container) {
                        gas95Container.textContent = '';
                        if (locality.gas95 && locality.gas95.length > 0) {
                            locality.gas95.forEach(function (station, rank) {
                                gas95Container.appendChild(createStationRow(rank, station, 'gas95'));
                            });
                        } else {
                            gas95Container.appendChild(createEmptyState());
                        }
                    }
                });

                // Trigger flash on all local prices
                var prices = document.querySelectorAll('.local-price-blink');
                prices.forEach(function (el) {
                    el.classList.add('price-blink');
                    setTimeout(function () { el.classList.remove('price-blink'); }, 500);
                });

                // Trigger flash on all update timestamps to give live system feel
                Object.keys(data.localities).forEach(function (key) {
                    var timeEl = document.getElementById('updated-time-' + key);
                    if (timeEl) {
                        timeEl.classList.add('price-blink');
                        setTimeout(function () { timeEl.classList.remove('price-blink'); }, 500);
                    }
                });
            })
            .catch(function (err) {
                console.warn('[Dashboard] Error fetching competitor data:', err);
            });
        }

        // Ejecutar de inmediato y luego cada 5 minutos (sincronizado con el Ministerio/cron)
        fetchCompetitors();
        setInterval(fetchCompetitors, 300000);

    })();
    </script>

</x-filament-panels::page>
