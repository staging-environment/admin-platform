@props([
    'alert' => [],
    'gasoilData' => null,
    'rbobData' => null,
])

@php
    $isDiesel = ($alert['fuel_type'] ?? '') === 'diesel';
    $locality = $alert['locality_name'] ?? 'la localidad';

    // Materia prima asociada
    $commData = $isDiesel ? ($gasoilData ?? []) : ($rbobData ?? []);
    $commName = $isDiesel ? 'Gasoil Londres (ICE)' : 'Gasolina RBOB (NYMEX)';
    $commPrice = $commData['price'] ?? null;
    $commPct = $commData['change_pct'] ?? null;
    $isCommUp = ($commData['is_up'] ?? null) === true;
    $isCommDown = ($commData['is_up'] ?? null) === false;

    $commPriceFormatted = $commPrice ? number_format($commPrice, 4, '.', ',') . ($isDiesel ? ' USD/t' : ' USD/gal') : '—';
    $commPctFormatted = $commPct !== null ? (($commPct >= 0 ? '+' : '') . number_format($commPct, 2, '.', ',') . '%') : '0.00%';

    // Competidores
    $stations = $alert['stations'] ?? [];
    $validStations = array_filter($stations, fn($s) => isset($s['price']) && $s['price'] > 0);
    usort($validStations, fn($a, $b) => $a['price'] <=> $b['price']);
    $topCompetitor = !empty($validStations) ? $validStations[0] : null;
    $minPrice = $topCompetitor['price'] ?? 0;
    $minStationName = $topCompetitor['name'] ?? 'Líder local';

    $changedStations = array_filter($stations, fn($s) => !empty($s['is_changed']));
    $upCount = count(array_filter($changedStations, fn($s) => ($s['direction'] ?? '') === 'sube'));
    $downCount = count(array_filter($changedStations, fn($s) => ($s['direction'] ?? '') === 'baja'));

    // Cálculo estratégico indicativo
    if ($minPrice > 0) {
        if ($isCommUp && $upCount >= $downCount) {
            $strategyTitle = 'Optimización de Margen';
            $strategyBadgeStyle = 'background: rgba(16, 185, 129, 0.18); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399;';
            $suggestedPrice = round($minPrice - 0.001, 3);
            $diffVsLeader = '-0,001 € vs ' . $minStationName;
            $adviceText = "El mercado mayorista ({$commName}) sube un <strong>{$commPctFormatted}</strong> y los competidores en {$locality} están incrementando precios. Existe margen para fijar <strong>" . number_format($suggestedPrice, 3, ',', '.') . " €</strong> (+margen) manteniéndose como la opción más barata de la zona.";
        } elseif ($isCommDown || $downCount > $upCount) {
            $strategyTitle = 'Liderazgo de Volumen';
            $strategyBadgeStyle = 'background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.4); color: #60a5fa;';
            $suggestedPrice = round($minPrice - 0.002, 3);
            $diffVsLeader = '-0,002 € vs ' . $minStationName;
            $adviceText = "Con materias primas a la baja ({$commPctFormatted}) o movimientos agresivos de la competencia, se sugiere fijar <strong>" . number_format($suggestedPrice, 3, ',', '.') . " €</strong> para afianzar el puesto #1 indiscutible y captar el máximo flujo de clientes en {$locality}.";
        } else {
            $strategyTitle = 'Posicionamiento Competitivo';
            $strategyBadgeStyle = 'background: rgba(245, 158, 11, 0.18); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24;';
            $suggestedPrice = round($minPrice - 0.001, 3);
            $diffVsLeader = '-0,001 € vs ' . $minStationName;
            $adviceText = "Para mantener una posición ventajosa tras los cambios detectados en {$locality}, se sugiere un precio de <strong>" . number_format($suggestedPrice, 3, ',', '.') . " €</strong> para liderar el ranking local.";
        }
    } else {
        $strategyTitle = 'Sin Datos Suficientes';
        $strategyBadgeStyle = 'background: rgba(156, 163, 175, 0.2); border: 1px solid rgba(156, 163, 175, 0.4); color: #9ca3af;';
        $suggestedPrice = null;
        $diffVsLeader = '—';
        $adviceText = "No hay datos suficientes para calcular la recomendación en esta localidad.";
    }
@endphp

<!-- Bloque de Sugerencia Estratégica de Precio -->
<div class="p-3.5 bg-gradient-to-br from-slate-900 via-gray-900 to-slate-950 text-white border-b border-gray-800">
    <div class="flex items-center justify-between gap-2 flex-wrap pb-2 mb-2 border-b border-white/10">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md bg-amber-400/20 border border-amber-400/30 flex items-center justify-center flex-shrink-0 text-amber-300 text-xs">
                💡
            </div>
            <div>
                <h4 class="text-xs font-black uppercase tracking-wider text-white leading-tight">
                    Sugerencia Indicativa de Precio
                </h4>
                <p class="text-[9px] text-gray-400">
                    Basado en cotizaci&oacute;n de materia prima y ranking de competencia en {{ $locality }}
                </p>
            </div>
        </div>
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm" style="{{ $strategyBadgeStyle }}">
            {{ $strategyTitle }}
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 my-2.5">
        <!-- Precio Sugerido -->
        <div class="p-2.5 rounded-xl bg-white/5 border border-white/10 flex flex-col justify-center">
            <span class="text-[9px] uppercase tracking-wider text-gray-400 font-bold">Precio Sugerido</span>
            <div class="flex items-baseline gap-1 mt-0.5">
                @if($suggestedPrice)
                    <span class="text-base font-black text-emerald-400 tabular-nums tracking-tight">
                        {{ number_format($suggestedPrice, 3, ',', '.') }} €
                    </span>
                    <span class="text-[9px] text-gray-400 font-semibold">/ litro</span>
                @else
                    <span class="text-xs font-bold text-gray-400">—</span>
                @endif
            </div>
            <span class="text-[9px] text-emerald-300/80 font-medium truncate mt-0.5">
                {{ $diffVsLeader }}
            </span>
        </div>

        <!-- Factor Materia Prima -->
        <div class="p-2.5 rounded-xl bg-white/5 border border-white/10 flex flex-col justify-center">
            <span class="text-[9px] uppercase tracking-wider text-gray-400 font-bold truncate" title="{{ $commName }}">
                Materia Prima ({{ $isDiesel ? 'ICE' : 'RBOB' }})
            </span>
            <div class="flex items-baseline gap-1 mt-0.5">
                <span class="text-xs font-bold text-white tabular-nums">
                    {{ $commPriceFormatted }}
                </span>
                <span class="text-[10px] font-black tabular-nums {{ $isCommUp ? 'text-emerald-400' : ($isCommDown ? 'text-rose-400' : 'text-gray-300') }}">
                    {{ $commPctFormatted }}
                </span>
            </div>
            <span class="text-[9px] text-gray-400 font-medium truncate mt-0.5">
                {{ $isCommUp ? '🟢 Mercado al alza' : ($isCommDown ? '🔴 Mercado a la baja' : '⚪ Estable') }}
            </span>
        </div>

        <!-- Factor Líder Local -->
        <div class="p-2.5 rounded-xl bg-white/5 border border-white/10 flex flex-col justify-center">
            <span class="text-[9px] uppercase tracking-wider text-gray-400 font-bold">L&iacute;der Local (TOP 1)</span>
            <div class="flex items-baseline gap-1 mt-0.5">
                <span class="text-xs font-bold text-white tabular-nums">
                    {{ number_format($minPrice, 3, ',', '.') }} €
                </span>
                <span class="text-[9px] text-gray-400 font-semibold">/ litro</span>
            </div>
            <span class="text-[9px] text-gray-300 font-medium truncate mt-0.5" title="{{ $minStationName }}">
                {{ $minStationName }}
            </span>
        </div>
    </div>

    <!-- Recomendación descriptiva -->
    <div class="text-[10px] text-gray-300 leading-relaxed bg-black/30 p-2 rounded-lg border border-white/5 flex items-start gap-1.5">
        <span class="text-amber-400 flex-shrink-0 mt-0.5">🎯</span>
        <span>{!! $adviceText !!}</span>
    </div>
</div>