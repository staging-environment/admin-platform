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
            $strategyBadgeStyle = 'background: rgba(16, 185, 129, 0.25) !important; border: 1px solid #10b981 !important; color: #34d399 !important;';
            $suggestedPrice = round($minPrice - 0.001, 3);
            $diffVsLeader = '-0,001 € vs ' . $minStationName;
            $adviceText = "El mercado mayorista ({$commName}) sube un <strong style=\"color:#34d399\">{$commPctFormatted}</strong> y los competidores en {$locality} están incrementando precios. Existe margen para fijar <strong style=\"color:#fbbf24\">" . number_format($suggestedPrice, 3, ',', '.') . " €</strong> (+margen) manteniéndose como la opción más barata de la zona.";
        } elseif ($isCommDown || $downCount > $upCount) {
            $strategyTitle = 'Liderazgo de Volumen';
            $strategyBadgeStyle = 'background: rgba(59, 130, 246, 0.25) !important; border: 1px solid #3b82f6 !important; color: #60a5fa !important;';
            $suggestedPrice = round($minPrice - 0.002, 3);
            $diffVsLeader = '-0,002 € vs ' . $minStationName;
            $adviceText = "Con materias primas a la baja (<strong style=\"color:#f87171\">{$commPctFormatted}</strong>) o movimientos agresivos de la competencia, se sugiere fijar <strong style=\"color:#fbbf24\">" . number_format($suggestedPrice, 3, ',', '.') . " €</strong> para afianzar el puesto #1 indiscutible y captar el máximo flujo de clientes en {$locality}.";
        } else {
            $strategyTitle = 'Posicionamiento Competitivo';
            $strategyBadgeStyle = 'background: rgba(245, 158, 11, 0.25) !important; border: 1px solid #f59e0b !important; color: #fbbf24 !important;';
            $suggestedPrice = round($minPrice - 0.001, 3);
            $diffVsLeader = '-0,001 € vs ' . $minStationName;
            $adviceText = "Para mantener una posición ventajosa tras los cambios detectados en {$locality}, se sugiere un precio de <strong style=\"color:#fbbf24\">" . number_format($suggestedPrice, 3, ',', '.') . " €</strong> para liderar el ranking local.";
        }
    } else {
        $strategyTitle = 'Sin Datos Suficientes';
        $strategyBadgeStyle = 'background: rgba(156, 163, 175, 0.2) !important; border: 1px solid rgba(156, 163, 175, 0.4) !important; color: #9ca3af !important;';
        $suggestedPrice = null;
        $diffVsLeader = '—';
        $adviceText = "No hay datos suficientes para calcular la recomendación en esta localidad.";
    }
@endphp

<!-- Bloque de Sugerencia Estratégica de Precio -->
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; color: #ffffff !important; border-bottom: 1px solid #334155 !important; padding: 14px 16px !important;">
    <div style="display: flex !important; align-items: center !important; justify-content: space-between !important; gap: 8px !important; flex-wrap: wrap !important; padding-bottom: 10px !important; margin-bottom: 10px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;">
        <div style="display: flex !important; align-items: center !important; gap: 8px !important;">
            <div style="width: 26px !important; height: 26px !important; border-radius: 8px !important; background: rgba(245, 158, 11, 0.2) !important; border: 1px solid rgba(245, 158, 11, 0.4) !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; font-size: 13px !important;">
                💡
            </div>
            <div>
                <h4 style="font-size: 12px !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; color: #ffffff !important; line-height: 1.2 !important; margin: 0 !important;">
                    Sugerencia Indicativa de Precio
                </h4>
                <p style="font-size: 10px !important; color: #94a3b8 !important; margin: 2px 0 0 0 !important; line-height: 1 !important;">
                    Basado en cotizaci&oacute;n de materia prima y ranking de competencia en {{ $locality }}
                </p>
            </div>
        </div>
        <span style="padding: 3px 10px !important; border-radius: 9999px !important; font-size: 10px !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.04em !important; {{ $strategyBadgeStyle }}">
            {{ $strategyTitle }}
        </span>
    </div>

    <div style="display: grid !important; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) !important; gap: 10px !important; margin: 10px 0 !important;">
        <!-- Precio Sugerido -->
        <div style="padding: 10px 12px !important; border-radius: 10px !important; background: rgba(255, 255, 255, 0.06) !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; display: flex !important; flex-direction: column !important; justify-content: center !important;">
            <span style="font-size: 9px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; color: #94a3b8 !important; font-weight: 800 !important;">
                Precio Sugerido
            </span>
            <div style="display: flex !important; align-items: baseline !important; gap: 4px !important; margin-top: 3px !important;">
                @if($suggestedPrice)
                    <span style="font-size: 18px !important; font-weight: 900 !important; color: #34d399 !important; font-variant-numeric: tabular-nums !important; letter-spacing: -0.02em !important; line-height: 1 !important;">
                        {{ number_format($suggestedPrice, 3, ',', '.') }}&nbsp;&euro;
                    </span>
                    <span style="font-size: 10px !important; color: #94a3b8 !important; font-weight: 700 !important;">/ litro</span>
                @else
                    <span style="font-size: 13px !important; font-weight: 800 !important; color: #94a3b8 !important;">—</span>
                @endif
            </div>
            <span style="font-size: 10px !important; color: #6ee7b7 !important; font-weight: 700 !important; margin-top: 3px !important; overflow: hidden !important; text-overflow: ellipsis !important; white-space: nowrap !important;">
                {{ $diffVsLeader }}
            </span>
        </div>

        <!-- Factor Materia Prima -->
        <div style="padding: 10px 12px !important; border-radius: 10px !important; background: rgba(255, 255, 255, 0.06) !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; display: flex !important; flex-direction: column !important; justify-content: center !important;">
            <span style="font-size: 9px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; color: #94a3b8 !important; font-weight: 800 !important; overflow: hidden !important; text-overflow: ellipsis !important; white-space: nowrap !important;" title="{{ $commName }}">
                Materia Prima ({{ $isDiesel ? 'ICE' : 'RBOB' }})
            </span>
            <div style="display: flex !important; align-items: baseline !important; gap: 6px !important; margin-top: 3px !important;">
                <span style="font-size: 13px !important; font-weight: 900 !important; color: #ffffff !important; font-variant-numeric: tabular-nums !important; line-height: 1 !important;">
                    {{ $commPriceFormatted }}
                </span>
                <span style="font-size: 11px !important; font-weight: 900 !important; font-variant-numeric: tabular-nums !important; color: {{ $isCommUp ? '#34d399' : ($isCommDown ? '#f87171' : '#94a3b8') }} !important;">
                    {{ $commPctFormatted }}
                </span>
            </div>
            <span style="font-size: 10px !important; color: #cbd5e1 !important; font-weight: 600 !important; margin-top: 3px !important; overflow: hidden !important; text-overflow: ellipsis !important; white-space: nowrap !important;">
                {{ $isCommUp ? '🟢 Mercado al alza' : ($isCommDown ? '🔴 Mercado a la baja' : '⚪ Mercado estable') }}
            </span>
        </div>

        <!-- Factor Líder Local -->
        <div style="padding: 10px 12px !important; border-radius: 10px !important; background: rgba(255, 255, 255, 0.06) !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; display: flex !important; flex-direction: column !important; justify-content: center !important;">
            <span style="font-size: 9px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; color: #94a3b8 !important; font-weight: 800 !important;">
                L&iacute;der Local (TOP 1)
            </span>
            <div style="display: flex !important; align-items: baseline !important; gap: 4px !important; margin-top: 3px !important;">
                @if($minPrice > 0)
                    <span style="font-size: 15px !important; font-weight: 900 !important; color: #f8fafc !important; font-variant-numeric: tabular-nums !important; line-height: 1 !important;">
                        {{ number_format($minPrice, 3, ',', '.') }}&nbsp;&euro;
                    </span>
                    <span style="font-size: 10px !important; color: #94a3b8 !important; font-weight: 700 !important;">/ litro</span>
                @else
                    <span style="font-size: 13px !important; font-weight: 800 !important; color: #94a3b8 !important;">—</span>
                @endif
            </div>
            <span style="font-size: 10px !important; color: #e2e8f0 !important; font-weight: 700 !important; margin-top: 3px !important; overflow: hidden !important; text-overflow: ellipsis !important; white-space: nowrap !important;" title="{{ $minStationName }}">
                {{ $minStationName }}
            </span>
        </div>
    </div>

    <!-- Recomendación descriptiva -->
    <div style="margin-top: 8px !important; font-size: 11px !important; color: #e2e8f0 !important; line-height: 1.4 !important; background: rgba(0, 0, 0, 0.35) !important; padding: 8px 12px !important; border-radius: 8px !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; display: flex !important; align-items: flex-start !important; gap: 8px !important;">
        <span style="color: #fbbf24 !important; flex-shrink: 0 !important; margin-top: 1px !important;">🎯</span>
        <div>{!! $adviceText !!}</div>
    </div>
</div>