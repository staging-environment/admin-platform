<x-filament-panels::page>
        {{-- Chart.js CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        {{-- Print handler — evita bloqueos CSP en producción (no inline onclick) --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.addEventListener('click', function (e) {
                    if (e.target && e.target.id === 'btn-print-pdf') {
                        window.print();
                    }
                });
            });
        </script>

    <div class="space-y-6">

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- FORMULARIO DE FILTROS                                          --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg no-print">
            <div class="p-6">
                <form wire:submit="generateReport" class="space-y-6">
                    {{ $this->form }}
                    <div class="mt-4 flex justify-end">
                        <x-filament::button type="submit" color="primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="generateReport">
                                <svg class="inline h-4 w-4 mr-1.5 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Generar Informe
                            </span>
                            <span wire:loading wire:target="generateReport" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Generando...
                            </span>
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Error / Aviso --}}
        @if($errorMsg)
            <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4 text-amber-800 text-sm font-medium">
                <svg class="h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                {{ $errorMsg }}
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- RESULTADOS (solo visible si hay datos)                         --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        @if($resultType && $tableData)

            {{-- CABECERA DE RESULTADOS + BOTONES DE EXPORTACIÓN --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 no-print">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        @if($resultType === 'margen_mercaderia')
                            <h2 class="text-base font-bold text-gray-800">
                                📊 Margen Comercial de Mercancía (Grupo 3)
                            </h2>
                        @endif
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ number_format(count($tableData), 0, ',', '.') }} artículos encontrados
                            — Página {{ $tablePage }} de {{ $this->getTableTotalPages() }}
                        </p>
                    </div>

                    {{-- BOTONES EXPORTAR --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-400 font-medium mr-1">Exportar:</span>

                        {{-- CSV --}}
                        <a href="{{ $this->getExportUrl('csv') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                            </svg>
                            CSV
                        </a>

                        {{-- Excel --}}
                        <a href="{{ $this->getExportUrl('excel') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                            </svg>
                            Excel
                        </a>

                        {{-- PDF / Imprimir — id para event listener (CSP-safe), style inline anti-purge --}}
                        <button id="btn-print-pdf" type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-white text-xs font-bold rounded-lg transition-colors shadow-sm"
                                style="background-color:#374151; border:none; cursor:pointer;"
                                onmouseover="this.style.backgroundColor='#1f2937'"
                                onmouseout="this.style.backgroundColor='#374151'">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            PDF / Imprimir
                        </button>
                    </div>
                </div>
            </div>

            {{-- GRÁFICA DE BARRAS HORIZONTALES (Top 20) --}}
            @if($chartData)
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 no-print"
                 wire:ignore
                 x-data="{
                     chartInstance: null,
                     buildChart(data) {
                         if (this.chartInstance) {
                             this.chartInstance.destroy();
                             this.chartInstance = null;
                         }
                         const canvas = document.getElementById('informesChartCanvas');
                         if (!canvas || !data) return;
                         this.chartInstance = new Chart(canvas.getContext('2d'), {
                             type: 'bar',
                             data: {
                                 labels: data.labels,
                                 datasets: [{
                                     label: '% Margen',
                                     data: data.margenes,
                                     backgroundColor: data.colors,
                                     borderColor: data.borders,
                                     borderWidth: 2,
                                     borderRadius: 6,
                                 }]
                             },
                             options: {
                                 indexAxis: 'y',
                                 responsive: true,
                                 maintainAspectRatio: false,
                                 plugins: {
                                     legend: { display: false },
                                     tooltip: {
                                         callbacks: {
                                             label: function(ctx) {
                                                 return ' ' + ctx.parsed.x.toFixed(2).replace('.', ',') + '% margen';
                                             }
                                         }
                                     }
                                 },
                                 scales: {
                                     x: {
                                         ticks: { callback: function(v) { return v + '%'; } },
                                         grid: { color: 'rgba(0,0,0,0.04)' }
                                     },
                                     y: { ticks: { font: { size: 11 } } }
                                 }
                             }
                         });
                     }
                 }"
                 x-on:chart-data-ready.window="buildChart($event.detail.chartData)"
                 x-init="
                     // Primer render: si chartData ya existe en el DOM, construir de inmediato
                     const initialData = {{ Js::from($chartData) }};
                     $nextTick(() => buildChart(initialData));
                 "
            >
                <div class="px-6 pt-5 pb-2 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-700">Top 20 artículos por % Margen Comercial</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Precio compra = media ponderada del período · PVP sin IVA</p>
                    </div>
                    <div class="flex items-center gap-3 text-[11px] text-gray-500">
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span> ≥ 40%</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-yellow-400 inline-block"></span> 20–40%</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span> &lt; 20%</span>
                    </div>
                </div>
                <div class="px-6 pb-6" style="height: 520px; position: relative;">
                    <canvas id="informesChartCanvas"></canvas>
                </div>
            </div>
            @endif

            {{-- TABLA DE DATOS --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 print-full-width">

                {{-- Cabecera tabla --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center justify-between print-header">
                    <div>
                        <h3 class="text-sm font-bold text-gray-700">Detalle por artículo</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Mostrando {{ ($tablePage - 1) * $tablePerPage + 1 }}–{{ min($tablePage * $tablePerPage, count($tableData)) }}
                            de {{ number_format(count($tableData), 0, ',', '.') }} artículos
                        </p>
                    </div>
                    {{-- Selector de registros por página --}}
                    <div class="flex items-center gap-2 text-xs text-gray-500 no-print">
                        <span>Mostrar:</span>
                        @foreach([15, 30, 50, 100] as $pp)
                            <button type="button"
                                    wire:click="$set('tablePerPage', {{ $pp }}); $set('tablePage', 1)"
                                    class="px-2 py-1 rounded {{ $tablePerPage === $pp ? 'bg-amber-500 text-white font-bold' : 'bg-gray-100 hover:bg-gray-200 text-gray-600' }} transition-colors">
                                {{ $pp }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-8">#</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Artículo</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Grupo</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">P.Compra</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">PVP c/IVA</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">% IVA</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">PVP s/IVA</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">% Margen</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider no-print">Uds.</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @php $startIndex = ($tablePage - 1) * $tablePerPage; @endphp
                            @foreach($this->getPagedTableData() as $i => $row)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="px-4 py-2.5 text-xs text-gray-400 font-mono">{{ $startIndex + $i + 1 }}</td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-semibold text-gray-800 text-sm leading-tight">{{ $row['descripcion'] }}</div>
                                        <div class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $row['codigo'] }}</div>
                                    </td>
                                    <td class="px-4 py-2.5 text-xs text-gray-500">{{ $row['grupo_nombre'] }}</td>
                                    <td class="px-4 py-2.5 text-right font-mono text-sm text-gray-700">
                                        {{ number_format($row['precio_compra'], 4, ',', '.') }} €
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono text-sm text-gray-700">
                                        {{ number_format($row['pvp_con_iva'], 4, ',', '.') }} €
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-xs text-gray-500">
                                        {{ number_format($row['pct_iva'], 0) }}%
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono text-sm text-gray-700">
                                        {{ number_format($row['pvp_sin_iva'], 4, ',', '.') }} €
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        @php
                                            $m = $row['margen_pct'];
                                            $badge = $m >= 40
                                                ? 'bg-green-100 text-green-800 border-green-200'
                                                : ($m >= 20 ? 'bg-yellow-100 text-yellow-800 border-yellow-200'
                                                            : 'bg-red-100 text-red-800 border-red-200');
                                        @endphp
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-black border {{ $badge }}">
                                            {{ number_format($m, 2, ',', '.') }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-xs text-gray-400 no-print">
                                        {{ number_format($row['unidades_compradas'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- PAGINACIÓN --}}
                @php $totalPages = $this->getTableTotalPages(); @endphp
                @if($totalPages > 1)
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between no-print">
                    <p class="text-xs text-gray-500">
                        Página <strong>{{ $tablePage }}</strong> de <strong>{{ $totalPages }}</strong>
                    </p>
                    <div class="flex items-center gap-1">
                        {{-- Primera --}}
                        <button type="button" wire:click="goToPage(1)" @disabled($tablePage === 1)
                                class="px-2.5 py-1.5 text-xs font-medium rounded-lg border
                                       {{ $tablePage === 1 ? 'text-gray-300 border-gray-100 cursor-not-allowed' : 'text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                            «
                        </button>
                        {{-- Anterior --}}
                        <button type="button" wire:click="prevPage" @disabled($tablePage === 1)
                                class="px-3 py-1.5 text-xs font-medium rounded-lg border
                                       {{ $tablePage === 1 ? 'text-gray-300 border-gray-100 cursor-not-allowed' : 'text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                            ‹ Anterior
                        </button>

                        {{-- Páginas cercanas --}}
                        @php
                            $window  = 2;
                            $start   = max(1, $tablePage - $window);
                            $end     = min($totalPages, $tablePage + $window);
                        @endphp
                        @for($p = $start; $p <= $end; $p++)
                            <button type="button" wire:click="goToPage({{ $p }})"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg border
                                           {{ $p === $tablePage
                                               ? 'bg-amber-500 text-white border-amber-500 shadow-sm'
                                               : 'text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                                {{ $p }}
                            </button>
                        @endfor

                        {{-- Siguiente --}}
                        <button type="button" wire:click="nextPage" @disabled($tablePage === $totalPages)
                                class="px-3 py-1.5 text-xs font-medium rounded-lg border
                                       {{ $tablePage === $totalPages ? 'text-gray-300 border-gray-100 cursor-not-allowed' : 'text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                            Siguiente ›
                        </button>
                        {{-- Última --}}
                        <button type="button" wire:click="goToPage({{ $totalPages }})" @disabled($tablePage === $totalPages)
                                class="px-2.5 py-1.5 text-xs font-medium rounded-lg border
                                       {{ $tablePage === $totalPages ? 'text-gray-300 border-gray-100 cursor-not-allowed' : 'text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                            »
                        </button>
                    </div>
                </div>
                @endif

            </div>{{-- fin tabla --}}

        @endif {{-- fin resultados --}}

    </div>

    {{-- CSS de impresión --}}
    <style>
        @media print {
            .no-print { display: none !important; }
            .fi-sidebar, .fi-topbar, nav, header { display: none !important; }
            .print-full-width { width: 100%; box-shadow: none !important; border: none !important; }
            body { font-size: 10px; }
            th, td { padding: 3px 5px !important; font-size: 10px !important; }
            a[href]::after { content: none !important; }
            .rounded-full { border-radius: 4px !important; }
        }
    </style>

</x-filament-panels::page>
