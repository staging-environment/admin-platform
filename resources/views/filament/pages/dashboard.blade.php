<x-filament-panels::page>
    <style>
        .fi-header { display: none !important; }
    </style>

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

    <div class="space-y-7" style="margin-top: -2.25rem !important;">

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
                <div class="market-card-black rounded-2xl p-4.5 shadow-2xl" id="card-gasoil">
                    {{-- Cabecera --}}
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold tracking-widest uppercase" style="color:#6b7280; font-size: 10px;">Yahoo Finance · BZ=F</span>
                                <span class="text-xs px-1.5 py-0.5 rounded font-bold uppercase tracking-wide" style="background:rgba(251,191,36,0.12);color:#fbbf24;font-size:8px">FUTURES</span>
                            </div>
                            <h3 class="text-lg font-black text-white leading-tight">Gasoil Londres</h3>
                            <p class="text-xs mt-0.5" style="color:#4b5563; font-size: 10px;">Brent Crude Oil Futures · EUR/bbl</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.15)">
                            <svg class="w-4 h-4" style="color:#fbbf24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Precio principal --}}
                    <div class="mb-3">
                        <div class="flex items-baseline gap-2">
                            <span id="gasoil-price" class="font-black text-white tabular-nums" style="font-size:1.95rem;line-height:1;letter-spacing:-0.02em">
                                @if($gasoilData['price'])
                                    {{ number_format($gasoilData['price'], 4, '.', ',') }}
                                @else
                                    <span class="market-spinner" style="width:16px;height:16px;"></span>
                                @endif
                            </span>
                            <span class="text-xs font-semibold" style="color:#6b7280">EUR</span>
                        </div>

                        {{-- Variación --}}
                        <div class="flex items-center gap-1.5 mt-1" id="gasoil-change-row">
                            <span id="gasoil-arrow" class="text-sm leading-none">
                                @if($gasoilData['is_up'] === true)  <span style="color:#34d399">▲</span>
                                @elseif($gasoilData['is_up'] === false) <span style="color:#f87171">▼</span>
                                @else <span style="color:#6b7280">—</span>
                                @endif
                            </span>
                            <span id="gasoil-change" class="text-xs font-bold tabular-nums {{ $gasoilData['is_up'] === true ? 'change-badge-up' : ($gasoilData['is_up'] === false ? 'change-badge-down' : 'change-badge-null') }}">
                                @if($gasoilData['change'] !== null)
                                    {{ ($gasoilData['change'] >= 0 ? '+' : '') . number_format($gasoilData['change'], 4, '.', ',') }}
                                @else ---
                                @endif
                            </span>
                            <span id="gasoil-pct" class="text-xs font-semibold tabular-nums {{ $gasoilData['is_up'] === true ? 'change-badge-up' : ($gasoilData['is_up'] === false ? 'change-badge-down' : 'change-badge-null') }}" style="opacity:0.85">
                                @if($gasoilData['change_pct'] !== null)
                                    ({{ ($gasoilData['change_pct'] >= 0 ? '+' : '') . number_format($gasoilData['change_pct'], 2, '.', ',') }}%)
                                @else (--%)
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Pie --}}
                    <div class="flex items-center justify-between pt-2" style="border-top:1px solid rgba(255,255,255,0.05)">
                        <span class="text-xs" style="color:#4b5563; font-size: 10px;">Última actualización</span>
                        <span id="gasoil-updated" class="text-xs font-mono tabular-nums" style="color:#9ca3af; font-size: 10px;">
                            {{ $gasoilData['updated_at'] ?? '—' }}
                        </span>
                    </div>
                </div>

                {{-- ── TARJETA 2: GASOLINA RBOB (RB=F) ────────────────── --}}
                <div class="market-card-green rounded-2xl p-4.5 shadow-2xl" id="card-rbob">
                    {{-- Cabecera --}}
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold tracking-widest uppercase" style="color:rgba(52,211,153,0.55); font-size: 10px;">Yahoo Finance · RB=F</span>
                                <span class="text-xs px-1.5 py-0.5 rounded font-bold uppercase tracking-wide" style="background:rgba(52,211,153,0.12);color:#34d399;font-size:8px">FUTURES</span>
                            </div>
                            <h3 class="text-lg font-black text-white leading-tight">Gasolina RBOB</h3>
                            <p class="text-xs mt-0.5" style="color:rgba(52,211,153,0.4); font-size: 10px;">Reformulated Gasoline Futures · EUR/gal</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.15)">
                            <svg class="w-4 h-4" style="color:#34d399" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Precio principal --}}
                    <div class="mb-3">
                        <div class="flex items-baseline gap-2">
                            <span id="rbob-price" class="font-black text-white tabular-nums" style="font-size:1.95rem;line-height:1;letter-spacing:-0.02em">
                                @if($rbobData['price'])
                                    {{ number_format($rbobData['price'], 4, '.', ',') }}
                                @else
                                    <span class="market-spinner" style="width:16px;height:16px;"></span>
                                @endif
                            </span>
                            <span class="text-xs font-semibold" style="color:rgba(52,211,153,0.4)">EUR</span>
                        </div>

                        {{-- Variación --}}
                        <div class="flex items-center gap-1.5 mt-1" id="rbob-change-row">
                            <span id="rbob-arrow" class="text-sm leading-none">
                                @if($rbobData['is_up'] === true)  <span style="color:#6ee7b7">▲</span>
                                @elseif($rbobData['is_up'] === false) <span style="color:#fca5a5">▼</span>
                                @else <span style="color:rgba(52,211,153,0.4)">—</span>
                                @endif
                            </span>
                            <span id="rbob-change" class="text-xs font-bold tabular-nums" style="color:{{ $rbobData['is_up'] === true ? '#6ee7b7' : ($rbobData['is_up'] === false ? '#fca5a5' : 'rgba(52,211,153,0.5)') }}">
                                @if($rbobData['change'] !== null)
                                    {{ ($rbobData['change'] >= 0 ? '+' : '') . number_format($rbobData['change'], 4, '.', ',') }}
                                @else ---
                                @endif
                            </span>
                            <span id="rbob-pct" class="text-xs font-semibold tabular-nums" style="color:{{ $rbobData['is_up'] === true ? '#6ee7b7' : ($rbobData['is_up'] === false ? '#fca5a5' : 'rgba(52,211,153,0.5)') }};opacity:0.85">
                                @if($rbobData['change_pct'] !== null)
                                    ({{ ($rbobData['change_pct'] >= 0 ? '+' : '') . number_format($rbobData['change_pct'], 2, '.', ',') }}%)
                                @else (--%)
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Pie --}}
                    <div class="flex items-center justify-between pt-2" style="border-top:1px solid rgba(255,255,255,0.06)">
                        <span class="text-xs" style="color:rgba(52,211,153,0.35); font-size: 10px;">Última actualización</span>
                        <span id="rbob-updated" class="text-xs font-mono tabular-nums" style="color:rgba(52,211,153,0.55); font-size: 10px;">
                            {{ $rbobData['updated_at'] ?? '—' }}
                        </span>
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
                <h2 class="text-xs font-bold tracking-widest uppercase" style="color:#9ca3af">Competencia Local</h2>
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
                var arrowUpColor   = prefix === 'gasoil' ? '#34d399' : '#6ee7b7';
                var arrowDownColor = prefix === 'gasoil' ? '#f87171' : '#fca5a5';
                var arrowNullColor = prefix === 'gasoil' ? '#6b7280' : 'rgba(52,211,153,0.4)';
                if (data.is_up === true) {
                    elArrow.style.color = arrowUpColor;
                    elArrow.textContent = '▲';
                } else if (data.is_up === false) {
                    elArrow.style.color = arrowDownColor;
                    elArrow.textContent = '▼';
                } else {
                    elArrow.style.color = arrowNullColor;
                    elArrow.textContent = '—';
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
                        timeEl.textContent = locality.updated_at ? 'Actualizado: ' + locality.updated_at : '';
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

        // Ejecutar de inmediato y luego cada 1 hora (sincronizado con el Ministerio/cron)
        fetchCompetitors();
        setInterval(fetchCompetitors, 3600000);

    })();
    </script>

</x-filament-panels::page>
