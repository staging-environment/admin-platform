@php
    $localities = [
        'utrera'    => 'Utrera',
        'sevilla'   => 'Sevilla',
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
                <span id="updated-time-{{ $key }}" class="text-xs font-medium tabular-nums" style="color:#9ca3af; font-size: 10px;">
                    @if($ldata['updated_at'])
                        Miteco: {{ $ldata['updated_at'] }}
                    @endif
                    @if(isset($ldata['checked_at']))
                        | Bot: {{ $ldata['checked_at'] }}
                    @endif
                </span>
            </div>

            {{-- Columnas Diesel | Gasolina 95 --}}
            <div class="grid grid-cols-2 divide-x dark:divide-gray-800" style="border-top:1px solid rgba(0,0,0,0.05);divide-color:rgba(0,0,0,0.06)">

                {{-- ── COLUMNA DIESEL (negro/oscuro) ──────────── --}}
                <div class="p-4">
                    <div class="flex items-center gap-1.5 mb-3">
                        <div class="w-2 h-2 rounded-full" style="background:#1f2937"></div>
                        <span class="text-xs font-black uppercase tracking-widest" style="color:#1f2937;font-size:9px">Diesel</span>
                    </div>

                    <div id="rows-{{ $key }}-diesel" class="space-y-1">
                        @if(count($ldata['diesel']) > 0)
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
                                    <span class="font-black tabular-nums whitespace-nowrap local-price-blink"
                                          style="font-size:12px;color:{{ $rank === 0 ? '#111827' : '#374151' }}">
                                        {{ number_format($station['price'], 3, ',', '.') }}&nbsp;€
                                    </span>
                                </div>
                            @endforeach
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

                {{-- ── COLUMNA GASOLINA 95 (verde) ─────────────── --}}
                <div class="p-4">
                    <div class="flex items-center gap-1.5 mb-3">
                        <div class="w-2 h-2 rounded-full" style="background:#16a34a"></div>
                        <span class="text-xs font-black uppercase tracking-widest" style="color:#16a34a;font-size:9px">Gasolina 95</span>
                    </div>

                    <div id="rows-{{ $key }}-gas95" class="space-y-1">
                        @if(count($ldata['gas95']) > 0)
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
                                    <span class="font-black tabular-nums whitespace-nowrap local-price-blink"
                                          style="font-size:12px;color:{{ $rank === 0 ? '#15803d' : '#16a34a' }}">
                                        {{ number_format($station['price'], 3, ',', '.') }}&nbsp;€
                                    </span>
                                </div>
                            @endforeach
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
        </div>
    @endforeach
</div>
