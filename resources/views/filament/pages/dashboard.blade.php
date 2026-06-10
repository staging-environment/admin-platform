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

    <div class="space-y-7">

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- ZONA SUPERIOR: MERCADOS ENERGÉTICOS INTERNACIONALES            --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}

        <section>
            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-1 h-5 rounded-full" style="background:linear-gradient(180deg,#fbbf24,#f59e0b)"></div>
                <h2 class="text-xs font-bold tracking-widest uppercase" style="color:#9ca3af">Mercados Energéticos</h2>
                <span class="ml-auto flex items-center gap-1.5 text-xs" style="color:#6b7280">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                    Actualización en tiempo real · Yahoo Finance
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- ── TARJETA 1: GASOIL BRENT LONDRES (BZ=F) ─────────── --}}
                <div class="market-card-black rounded-2xl p-6 shadow-2xl" id="card-gasoil">
                    {{-- Cabecera --}}
                    <div class="flex items-start justify-between mb-5">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-xs font-bold tracking-widest uppercase" style="color:#6b7280">Yahoo Finance · BZ=F</span>
                                <span class="text-xs px-1.5 py-0.5 rounded font-bold uppercase tracking-wide" style="background:rgba(251,191,36,0.12);color:#fbbf24;font-size:9px">FUTURES</span>
                            </div>
                            <h3 class="text-xl font-black text-white">Gasoil Londres</h3>
                            <p class="text-xs mt-0.5" style="color:#4b5563">Brent Crude Oil Futures · EUR/bbl</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.15)">
                            <svg class="w-5 h-5" style="color:#fbbf24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Precio principal --}}
                    <div class="mb-4">
                        <div class="flex items-baseline gap-2">
                            <span id="gasoil-price" class="font-black text-white tabular-nums" style="font-size:2.6rem;line-height:1;letter-spacing:-0.02em">
                                @if($gasoilData['price'])
                                    {{ number_format($gasoilData['price'], 2, '.', ',') }}
                                @else
                                    <span class="market-spinner"></span>
                                @endif
                            </span>
                            <span class="text-sm font-semibold" style="color:#6b7280">EUR</span>
                        </div>

                        {{-- Variación --}}
                        <div class="flex items-center gap-2 mt-2" id="gasoil-change-row">
                            <span id="gasoil-arrow" class="text-lg leading-none">
                                @if($gasoilData['is_up'] === true)  <span style="color:#34d399">▲</span>
                                @elseif($gasoilData['is_up'] === false) <span style="color:#f87171">▼</span>
                                @else <span style="color:#6b7280">—</span>
                                @endif
                            </span>
                            <span id="gasoil-change" class="text-base font-bold tabular-nums {{ $gasoilData['is_up'] === true ? 'change-badge-up' : ($gasoilData['is_up'] === false ? 'change-badge-down' : 'change-badge-null') }}">
                                @if($gasoilData['change'] !== null)
                                    {{ ($gasoilData['change'] >= 0 ? '+' : '') . number_format($gasoilData['change'], 2, '.', ',') }}
                                @else ---
                                @endif
                            </span>
                            <span id="gasoil-pct" class="text-sm font-semibold tabular-nums {{ $gasoilData['is_up'] === true ? 'change-badge-up' : ($gasoilData['is_up'] === false ? 'change-badge-down' : 'change-badge-null') }}" style="opacity:0.85">
                                @if($gasoilData['change_pct'] !== null)
                                    ({{ ($gasoilData['change_pct'] >= 0 ? '+' : '') . number_format($gasoilData['change_pct'], 2, '.', ',') }}%)
                                @else (--%)
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Pie --}}
                    <div class="flex items-center justify-between pt-3" style="border-top:1px solid rgba(255,255,255,0.05)">
                        <span class="text-xs" style="color:#4b5563">Última actualización</span>
                        <span id="gasoil-updated" class="text-xs font-mono tabular-nums" style="color:#9ca3af">
                            {{ $gasoilData['updated_at'] ?? '—' }}
                        </span>
                    </div>
                </div>

                {{-- ── TARJETA 2: GASOLINA RBOB (RB=F) ────────────────── --}}
                <div class="market-card-green rounded-2xl p-6 shadow-2xl" id="card-rbob">
                    {{-- Cabecera --}}
                    <div class="flex items-start justify-between mb-5">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-xs font-bold tracking-widest uppercase" style="color:rgba(52,211,153,0.55)">Yahoo Finance · RB=F</span>
                                <span class="text-xs px-1.5 py-0.5 rounded font-bold uppercase tracking-wide" style="background:rgba(52,211,153,0.12);color:#34d399;font-size:9px">FUTURES</span>
                            </div>
                            <h3 class="text-xl font-black text-white">Gasolina RBOB</h3>
                            <p class="text-xs mt-0.5" style="color:rgba(52,211,153,0.4)">Reformulated Gasoline Futures · EUR/gal</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.15)">
                            <svg class="w-5 h-5" style="color:#34d399" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Precio principal --}}
                    <div class="mb-4">
                        <div class="flex items-baseline gap-2">
                            <span id="rbob-price" class="font-black text-white tabular-nums" style="font-size:2.6rem;line-height:1;letter-spacing:-0.02em">
                                @if($rbobData['price'])
                                    {{ number_format($rbobData['price'], 4, '.', ',') }}
                                @else
                                    <span class="market-spinner"></span>
                                @endif
                            </span>
                            <span class="text-sm font-semibold" style="color:rgba(52,211,153,0.4)">EUR</span>
                        </div>

                        {{-- Variación --}}
                        <div class="flex items-center gap-2 mt-2" id="rbob-change-row">
                            <span id="rbob-arrow" class="text-lg leading-none">
                                @if($rbobData['is_up'] === true)  <span style="color:#6ee7b7">▲</span>
                                @elseif($rbobData['is_up'] === false) <span style="color:#fca5a5">▼</span>
                                @else <span style="color:rgba(52,211,153,0.4)">—</span>
                                @endif
                            </span>
                            <span id="rbob-change" class="text-base font-bold tabular-nums" style="color:{{ $rbobData['is_up'] === true ? '#6ee7b7' : ($rbobData['is_up'] === false ? '#fca5a5' : 'rgba(52,211,153,0.5)') }}">
                                @if($rbobData['change'] !== null)
                                    {{ ($rbobData['change'] >= 0 ? '+' : '') . number_format($rbobData['change'], 4, '.', ',') }}
                                @else ---
                                @endif
                            </span>
                            <span id="rbob-pct" class="text-sm font-semibold tabular-nums" style="color:{{ $rbobData['is_up'] === true ? '#6ee7b7' : ($rbobData['is_up'] === false ? '#fca5a5' : 'rgba(52,211,153,0.5)') }};opacity:0.85">
                                @if($rbobData['change_pct'] !== null)
                                    ({{ ($rbobData['change_pct'] >= 0 ? '+' : '') . number_format($rbobData['change_pct'], 2, '.', ',') }}%)
                                @else (--%)
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Pie --}}
                    <div class="flex items-center justify-between pt-3" style="border-top:1px solid rgba(255,255,255,0.06)">
                        <span class="text-xs" style="color:rgba(52,211,153,0.35)">Última actualización</span>
                        <span id="rbob-updated" class="text-xs font-mono tabular-nums" style="color:rgba(52,211,153,0.55)">
                            {{ $rbobData['updated_at'] ?? '—' }}
                        </span>
                    </div>
                </div>

            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- ZONA INFERIOR: COMPETENCIA LOCAL                               --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}

        <section wire:poll.300s="refreshCompetitors">

            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-1 h-5 rounded-full" style="background:linear-gradient(180deg,#60a5fa,#3b82f6)"></div>
                <h2 class="text-xs font-bold tracking-widest uppercase" style="color:#9ca3af">Competencia Local</h2>
                <span class="ml-auto text-xs" style="color:#6b7280">
                    Datos oficiales · Ministerio para la Transición Ecológica (MITECO)
                </span>
            </div>

            @php
                $localities = [
                    'sevilla'   => 'Sevilla',
                    'utrera'    => 'Utrera',
                    'el_cuervo' => 'El Cuervo de Sevilla',
                    'lebrija'   => 'Lebrija',
                ];
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                @foreach($localities as $key => $localityName)
                @php $ldata = $localityData[$key] ?? ['diesel' => [], 'gas95' => [], 'updated_at' => null]; @endphp

                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm overflow-hidden" style="border:1px solid rgba(0,0,0,0.07)">

                    {{-- Cabecera de tarjeta de localidad --}}
                    <div class="flex items-center justify-between px-5 py-3.5" style="background:linear-gradient(90deg,#111827,#1f2937)">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,0.06)">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-white">{{ $localityName }}</h3>
                        </div>
                        @if($ldata['updated_at'])
                        <span class="text-xs font-mono tabular-nums" style="color:#4b5563">
                            {{ $ldata['updated_at'] }}
                        </span>
                        @endif
                    </div>

                    {{-- Columnas Diesel | Gasolina 95 --}}
                    <div class="grid grid-cols-2 divide-x dark:divide-gray-800" style="border-top:1px solid rgba(0,0,0,0.05);divide-color:rgba(0,0,0,0.06)">

                        {{-- ── COLUMNA DIESEL (negro/oscuro) ──────────── --}}
                        <div class="p-4">
                            <div class="flex items-center gap-1.5 mb-3">
                                <div class="w-2 h-2 rounded-full" style="background:#1f2937"></div>
                                <span class="text-xs font-black uppercase tracking-widest" style="color:#1f2937;font-size:9px">Diesel</span>
                            </div>

                            @if(count($ldata['diesel']) > 0)
                            <div class="space-y-1">
                                @foreach($ldata['diesel'] as $rank => $station)
                                <div class="station-row {{ $rank === 0 ? 'rank-1' : '' }}">
                                    <span class="rank-chip text-white"
                                        style="background: {{ $rank === 0 ? '#111827' : ($rank === 1 ? '#374151' : ($rank === 2 ? '#4b5563' : '#6b7280')) }}">
                                        {{ $rank + 1 }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($station['name'] . ', ' . $station['address']) }}" 
                                           target="_blank" 
                                           class="hover:underline block group"
                                           title="Ver en Google Maps">
                                            <p class="font-bold truncate leading-tight dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400" style="font-size:11px;color:#1f2937">
                                                {{ Str::limit($station['name'], 24) }}
                                            </p>
                                            <p class="truncate leading-none dark:text-gray-400 mt-0.5" style="font-size:9px;color:#6b7280">
                                                {{ Str::limit($station['address'], 30) }}
                                            </p>
                                        </a>
                                    </div>
                                    <span class="font-black tabular-nums whitespace-nowrap"
                                        style="font-size:12px;color:{{ $rank === 0 ? '#111827' : '#374151' }}">
                                        {{ number_format($station['price'], 3, ',', '.') }}&nbsp;€
                                    </span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="flex flex-col items-center justify-center py-6 gap-1.5">
                                <svg class="w-6 h-6" style="color:#d1d5db" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                </svg>
                                <span class="text-xs" style="color:#9ca3af">Sin datos disponibles</span>
                            </div>
                            @endif
                        </div>

                        {{-- ── COLUMNA GASOLINA 95 (verde) ─────────────── --}}
                        <div class="p-4">
                            <div class="flex items-center gap-1.5 mb-3">
                                <div class="w-2 h-2 rounded-full" style="background:#16a34a"></div>
                                <span class="text-xs font-black uppercase tracking-widest" style="color:#16a34a;font-size:9px">Gasolina 95</span>
                            </div>

                            @if(count($ldata['gas95']) > 0)
                            <div class="space-y-1">
                                @foreach($ldata['gas95'] as $rank => $station)
                                <div class="station-row {{ $rank === 0 ? 'rank-1' : '' }}" style="{{ $rank === 0 ? 'background:rgba(22,163,74,0.05)' : '' }}">
                                    <span class="rank-chip text-white"
                                        style="background: {{ $rank === 0 ? '#15803d' : ($rank === 1 ? '#16a34a' : ($rank === 2 ? '#22c55e' : '#4ade80')) }}">
                                        {{ $rank + 1 }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($station['name'] . ', ' . $station['address']) }}" 
                                           target="_blank" 
                                           class="hover:underline block group"
                                           title="Ver en Google Maps">
                                            <p class="font-bold truncate leading-tight dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400" style="font-size:11px;color:#1f2937">
                                                {{ Str::limit($station['name'], 24) }}
                                            </p>
                                            <p class="truncate leading-none dark:text-gray-400 mt-0.5" style="font-size:9px;color:#6b7280">
                                                {{ Str::limit($station['address'], 30) }}
                                            </p>
                                        </a>
                                    </div>
                                    <span class="font-black tabular-nums whitespace-nowrap"
                                        style="font-size:12px;color:{{ $rank === 0 ? '#15803d' : '#16a34a' }}">
                                        {{ number_format($station['price'], 3, ',', '.') }}&nbsp;€
                                    </span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="flex flex-col items-center justify-center py-6 gap-1.5">
                                <svg class="w-6 h-6" style="color:#d1d5db" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                </svg>
                                <span class="text-xs" style="color:#9ca3af">Sin datos disponibles</span>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>

                @endforeach
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
        const POLL_INTERVAL  = 1000; // 1 segundo

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
                elPrice.textContent = fmt(data.price, priceDecimals);
                elPrice.classList.add('price-blink');
                setTimeout(function () { elPrice.classList.remove('price-blink'); }, 500);
            }

            /* Variación absoluta */
            if (elChange && data.change !== null) {
                elChange.textContent = fmtChange(data.change, changeDecimals);
                var upColor   = prefix === 'gasoil' ? '#34d399' : '#6ee7b7';
                var downColor = prefix === 'gasoil' ? '#f87171' : '#fca5a5';
                var nullColor = prefix === 'gasoil' ? '#6b7280' : 'rgba(52,211,153,0.5)';
                elChange.style.color = data.is_up === true ? upColor : (data.is_up === false ? downColor : nullColor);
            }

            /* Variación % */
            if (elPct && data.change_pct !== null) {
                var sign = parseFloat(data.change_pct) >= 0 ? '+' : '';
                elPct.textContent = '(' + sign + parseFloat(data.change_pct).toFixed(2) + '%)';
                var upColor2   = prefix === 'gasoil' ? '#34d399' : '#6ee7b7';
                var downColor2 = prefix === 'gasoil' ? '#f87171' : '#fca5a5';
                var nullColor2 = prefix === 'gasoil' ? '#6b7280' : 'rgba(52,211,153,0.5)';
                elPct.style.color = data.is_up === true ? upColor2 : (data.is_up === false ? downColor2 : nullColor2);
            }

            /* Flecha */
            if (elArrow) {
                var arrowUp   = prefix === 'gasoil' ? '#34d399' : '#6ee7b7';
                var arrowDown = prefix === 'gasoil' ? '#f87171' : '#fca5a5';
                var arrowNull = prefix === 'gasoil' ? '#6b7280' : 'rgba(52,211,153,0.4)';
                if (data.is_up === true) {
                    elArrow.innerHTML = '<span style="color:' + arrowUp + '">▲</span>';
                } else if (data.is_up === false) {
                    elArrow.innerHTML = '<span style="color:' + arrowDown + '">▼</span>';
                } else {
                    elArrow.innerHTML = '<span style="color:' + arrowNull + '">—</span>';
                }
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
                updateCard('gasoil', data.gasoil, 2, 2);
                updateCard('rbob',   data.rbob,   4, 4);
            })
            .catch(function (err) {
                console.warn('[Dashboard] Error fetching market data:', err);
            });
        }

        /* Ejecutar de inmediato y luego en bucle */
        fetchMarkets();
        setInterval(fetchMarkets, POLL_INTERVAL);

    })();
    </script>

</x-filament-panels::page>
