<x-filament-panels::page>
        {{-- Chart.js CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        {{-- Print handler — evita bloqueos CSP en producción (no inline onclick) --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.addEventListener('click', function (e) {
                    if (e.target && e.target.closest && e.target.closest('#btn-print-pdf')) {
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
        <div id="print-area" class="space-y-6 w-full">

            {{-- CABECERA DE RESULTADOS + BOTONES DE EXPORTACIÓN --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 no-print">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        @if($resultType === 'margen_mercaderia')
                            <h2 class="text-base font-bold text-gray-800">
                                📊 Margen Comercial de Mercancía (Grupo 3) — PVP Tarifa
                            </h2>
                        @elseif($resultType === 'tienda_margen')
                            <h2 class="text-base font-bold text-gray-800">
                                🛋️ Tienda — Margen Compra vs Venta (Grupo 3)
                            </h2>
                        @elseif($resultType === 'lavado_margen')
                            <h2 class="text-base font-bold text-gray-800">
                                🚐 Lavadero — Margen Compra vs Venta (Grupo 4)
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

            {{-- GRÁFICA DE EVOLUCIÓN HISTÓRICA --}}
            @if($chartEvolucionData)
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 no-print"
                 wire:ignore
                 x-data="{
                     chartEvInstance: null,
                     buildEvChart(data) {
                         if (this.chartEvInstance) {
                             this.chartEvInstance.destroy();
                             this.chartEvInstance = null;
                         }
                         const canvas = document.getElementById('evolucionChartCanvas');
                         if (!canvas || !data || data.length === 0) return;
                         
                         const labels = data.map(d => d.mes);
                         const ingresos = data.map(d => d.ingreso);
                         const costes = data.map(d => d.coste);
                         const beneficios = data.map(d => d.beneficio);
                         const margenes = data.map(d => d.margen_pct);
                         
                         this.chartEvInstance = new Chart(canvas.getContext('2d'), {
                             type: 'bar',
                             data: {
                                 labels: labels,
                                 datasets: [
                                     {
                                         type: 'line',
                                         label: 'Beneficio Neto (€)',
                                         data: beneficios,
                                         borderColor: '#f59e0b', // amber-500
                                         backgroundColor: '#f59e0b',
                                         borderWidth: 3,
                                         tension: 0.3,
                                         yAxisID: 'y'
                                     },
                                     {
                                         type: 'bar',
                                         label: 'Total Facturado (€)',
                                         data: ingresos,
                                         backgroundColor: 'rgba(16, 185, 129, 0.7)', // emerald-500
                                         borderRadius: 4,
                                         yAxisID: 'y'
                                     },
                                     {
                                         type: 'bar',
                                         label: 'Total Comprado (€)',
                                         data: costes,
                                         backgroundColor: 'rgba(59, 130, 246, 0.7)', // blue-500
                                         borderRadius: 4,
                                         yAxisID: 'y'
                                     },
                                     {
                                         type: 'line',
                                         label: '% Margen Real',
                                         data: margenes,
                                         borderColor: '#8b5cf6',
                                         backgroundColor: 'rgba(139,92,246,0.15)',
                                         borderWidth: 2,
                                         tension: 0.3,
                                         pointRadius: 4,
                                         pointBackgroundColor: '#8b5cf6',
                                         yAxisID: 'y1'
                                     }
                                 ]
                             },
                             options: {
                                 responsive: true,
                                 maintainAspectRatio: false,
                                 interaction: {
                                     mode: 'index',
                                     intersect: false,
                                 },
                                 plugins: {
                                     tooltip: {
                                         callbacks: {
                                             label: function(ctx) {
                                                 return ' ' + ctx.dataset.label + ': ' + (ctx.dataset.yAxisID === 'y1' ? ctx.parsed.y.toFixed(1) + ' %' : ctx.parsed.y.toLocaleString('es-ES') + ' €');
                                             }
                                         }
                                     }
                                 },
                                 scales: {
                                     x: {
                                         grid: { display: false }
                                     },
                                     y: {
                                         type: 'linear',
                                         display: true,
                                         position: 'left',
                                         ticks: { callback: function(v) { return v + ' €'; } }
                                     },
                                     y1: {
                                         type: 'linear',
                                         display: true,
                                         position: 'right',
                                         title: { display: true, text: '% Margen', color: '#8b5cf6' },
                                         ticks: { callback: function(v) { return v.toFixed(1) + ' %'; }, color: '#8b5cf6' },
                                         grid: { drawOnChartArea: false }
                                     }
                                 }
                             }
                         });
                     }
                 }"
                 x-on:chart-ev-data-ready.window="buildEvChart($event.detail.chartEvolucionData)"
                 x-init="
                     const self = this;
                     const initialEvData = {{ Js::from($chartEvolucionData) }};
                     const tryBuildEvChart = (attempts) => {
                         if (attempts <= 0) return;
                         if (typeof Chart === 'undefined' || !document.getElementById('evolucionChartCanvas')) {
                             setTimeout(() => tryBuildEvChart(attempts - 1), 100); return;
                         }
                         self.buildEvChart(initialEvData);
                     };
                     $nextTick(() => tryBuildEvChart(20));
                 "
            >
                <div class="px-6 pt-5 pb-2 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-700">
                            📈 Evolución Histórica (Mensual)
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Seguimiento temporal de los Costes, Ingresos y Beneficios reales
                        </p>
                    </div>
                </div>
                <div class="px-6 pb-6" style="height: 350px; position: relative;">
                    <canvas id="evolucionChartCanvas"></canvas>
                </div>
            </div>
            @endif

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
                        <h3 class="text-sm font-bold text-gray-700">
                            @if(in_array($resultType, ['tienda_margen', 'lavado_margen']))
                                Top 20 artículos — % Margen Real (Beneficio / Coste)
                            @else
                                Top 20 artículos por % Margen Comercial
                            @endif
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            @if(in_array($resultType, ['tienda_margen', 'lavado_margen']))
                                Solo artículos con ventas en el período. Compra y Venta = medias ponderadas.
                            @else
                                Precio compra = media ponderada del período · PVP sin IVA
                            @endif
                        </p>
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
                    {{-- Selector de registros por página y Buscador --}}
                    <div class="flex items-center gap-3 text-xs text-gray-500 no-print">
                        {{-- Filtro por Grupo --}}
                        <div>
                            <select wire:model.live="filterGroup" 
                                    class="py-1.5 pl-2.5 pr-8 border border-gray-300 rounded-lg text-xs focus:ring-amber-500 focus:border-amber-500 shadow-sm bg-white">
                                <option value="">Todos los Grupos</option>
                                @foreach($this->getActiveGroups() as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtro por Margen --}}
                        <div>
                            <select wire:model.live="filterMargin" 
                                    class="py-1.5 pl-2.5 pr-8 border border-gray-300 rounded-lg text-xs focus:ring-amber-500 focus:border-amber-500 shadow-sm bg-white">
                                <option value="">Todos los Márgenes</option>
                                <option value="high">🟢 Alto Margen (≥ 40%)</option>
                                <option value="mid">🟡 Margen Medio (20% – 40%)</option>
                                <option value="low">🔴 Bajo Margen (0% – 20%)</option>
                                <option value="negative">💀 Pérdidas (< 0%)</option>
                                <option value="no_sales">⏳ Sin Ventas</option>
                            </select>
                        </div>

                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="searchQuery" 
                                   placeholder="Buscar..." 
                                   class="pl-8 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-amber-500 focus:border-amber-500 w-36 shadow-sm">
                            <svg class="h-4 w-4 text-gray-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div class="flex items-center gap-2">
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
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">

                        {{-- ─── CABECERA ────────────────────────────────────────────── --}}
                        @if(in_array($resultType, ['tienda_margen', 'lavado_margen']))
                        @php
                            $sCol = $sortColumn;
                            $sDir = $sortDirection;
                            $ico  = fn($col) => $sCol === $col ? ($sDir === 'asc' ? ' ↑' : ' ↓') : ' ↕';
                            $thS  = 'cursor-pointer select-none hover:bg-gray-100 transition-colors';
                        @endphp
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-8">#</th>
                                <th wire:click="sortBy('descripcion')" class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    Artículo<span class="text-gray-400 font-normal">{{ $ico('descripcion') }}</span>
                                </th>
                                <th wire:click="sortBy('precio_compra')" class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    P.Compra s/IVA<span class="font-normal">{{ $ico('precio_compra') }}</span>
                                </th>
                                <th wire:click="sortBy('pct_iva_compra')" class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    % IVA Compra<span class="font-normal">{{ $ico('pct_iva_compra') }}</span>
                                </th>
                                <th wire:click="sortBy('precio_compra_con_iva')" class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    P.Compra c/IVA<span class="font-normal">{{ $ico('precio_compra_con_iva') }}</span>
                                </th>
                                <th wire:click="sortBy('fecha_ultima_compra')" class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    Últ. Compra<span class="font-normal">{{ $ico('fecha_ultima_compra') }}</span>
                                </th>
                                <th wire:click="sortBy('precio_venta_sin_iva')" class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    PVP s/IVA<span class="font-normal">{{ $ico('precio_venta_sin_iva') }}</span>
                                </th>
                                <th wire:click="sortBy('pct_iva')" class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    % IVA<span class="font-normal">{{ $ico('pct_iva') }}</span>
                                </th>
                                <th wire:click="sortBy('precio_venta')" class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    PVP c/IVA<span class="font-normal">{{ $ico('precio_venta') }}</span>
                                </th>
                                <th wire:click="sortBy('uds_compradas')" class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider border-l-2 border-gray-100 {{ $thS }}">
                                    Uds. Compradas<span class="font-normal">{{ $ico('uds_compradas') }}</span>
                                </th>
                                <th wire:click="sortBy('uds_vendidas')" class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    Uds. Vendidas<span class="font-normal">{{ $ico('uds_vendidas') }}</span>
                                </th>
                                <th wire:click="sortBy('total_comprado')" class="px-3 py-3 text-right text-xs font-bold text-blue-600 uppercase tracking-wider border-l-2 border-blue-100 {{ $thS }}">
                                    Total Comprado<span class="font-normal">{{ $ico('total_comprado') }}</span>
                                </th>
                                <th wire:click="sortBy('total_facturado')" class="px-3 py-3 text-right text-xs font-bold text-emerald-600 uppercase tracking-wider border-l-2 border-emerald-100 {{ $thS }}">
                                    Total Facturado<span class="font-normal">{{ $ico('total_facturado') }}</span>
                                </th>
                                <th wire:click="sortBy('beneficio')" class="px-3 py-3 text-right text-xs font-bold text-amber-600 uppercase tracking-wider border-l-2 border-amber-100 {{ $thS }}">
                                    Beneficio<span class="font-normal">{{ $ico('beneficio') }}</span>
                                </th>
                                <th wire:click="sortBy('margen_pct')" class="px-3 py-3 text-right text-xs font-bold text-amber-600 uppercase tracking-wider {{ $thS }}">
                                    % Margen<span class="font-normal">{{ $ico('margen_pct') }}</span>
                                </th>
                            </tr>
                        </thead>
                        @else
                        @php
                            $sCol = $sCol ?? $sortColumn;
                            $sDir = $sDir ?? $sortDirection;
                            $ico  = $ico ?? fn($col) => $sCol === $col
                                ? ($sDir === 'asc' ? ' ↑' : ' ↓')
                                : ' ↕';
                            $thS  = $thS ?? 'cursor-pointer select-none hover:bg-gray-100 transition-colors';
                        @endphp
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-8">#</th>
                                <th wire:click="sortBy('descripcion')" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    Artículo<span class="text-gray-400 font-normal">{{ $ico('descripcion') }}</span>
                                </th>
                                <th wire:click="sortBy('grupo_nombre')" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    Grupo<span class="text-gray-400 font-normal">{{ $ico('grupo_nombre') }}</span>
                                </th>
                                <th wire:click="sortBy('precio_compra')" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    P.Compra s/IVA<span class="font-normal">{{ $ico('precio_compra') }}</span>
                                </th>
                                <th wire:click="sortBy('pct_iva_compra')" class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    % IVA Compra<span class="font-normal">{{ $ico('pct_iva_compra') }}</span>
                                </th>
                                <th wire:click="sortBy('precio_compra_con_iva')" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    P.Compra c/IVA<span class="font-normal">{{ $ico('precio_compra_con_iva') }}</span>
                                </th>
                                <th wire:click="sortBy('pvp_sin_iva')" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    PVP s/IVA<span class="font-normal">{{ $ico('pvp_sin_iva') }}</span>
                                </th>
                                <th wire:click="sortBy('pct_iva')" class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    % IVA<span class="font-normal">{{ $ico('pct_iva') }}</span>
                                </th>
                                <th wire:click="sortBy('pvp_con_iva')" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    PVP c/IVA<span class="font-normal">{{ $ico('pvp_con_iva') }}</span>
                                </th>
                                <th wire:click="sortBy('margen_pct')" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider {{ $thS }}">
                                    % Margen<span class="font-normal">{{ $ico('margen_pct') }}</span>
                                </th>
                                <th wire:click="sortBy('unidades_compradas')" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider no-print {{ $thS }}">
                                    Uds.<span class="font-normal">{{ $ico('unidades_compradas') }}</span>
                                </th>
                            </tr>
                        </thead>
                        @endif

                        {{-- ─── FILAS ───────────────────────────────────────────────── --}}
                        @php $startIndex = ($tablePage - 1) * $tablePerPage; @endphp
                        <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($this->getPagedTableData() as $i => $row)
                            @if(in_array($resultType, ['tienda_margen', 'lavado_margen']))
                                {{-- ── Fila Margen Simple Compra vs Venta ────────────── --}}
                                @php
                                    $sinV   = $row['sin_ventas'] ?? false;
                                    $mReal  = $row['margen_pct'];
                                    $badgeR = $mReal === null ? 'bg-gray-100 text-gray-400 border-gray-200'
                                            : ($mReal >= 40 ? 'bg-green-100 text-green-800 border-green-200'
                                            : ($mReal >= 20 ? 'bg-yellow-100 text-yellow-800 border-yellow-200'
                                                            : 'bg-red-100 text-red-800 border-red-200'));

                                    $iva = $row['pct_iva'] ?? 0;
                                    $ivaBadge = $iva >= 21 ? 'bg-orange-50 text-orange-700 border-orange-200'
                                              : ($iva >= 10 ? 'bg-yellow-50 text-yellow-800 border-yellow-200'
                                              : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                                @endphp
                                <tr class="hover:bg-gray-50/60 transition-colors {{ $sinV ? 'opacity-50' : '' }}">
                                    <td class="px-3 py-2.5 text-xs text-gray-400 font-mono">{{ $startIndex + $i + 1 }}</td>
                                    <td class="px-3 py-2.5">
                                        <div class="font-semibold text-gray-800 text-sm leading-tight">{{ $row['descripcion'] }}</div>
                                        <div class="text-[11px] text-gray-400 font-mono">{{ $row['codigo'] }}
                                            @if($sinV) <span class="text-amber-500 ml-1">sin ventas</span>@endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-mono text-sm text-gray-600">
                                        {{ number_format($row['precio_compra'], 4, ',', '.') }} €
                                    </td>
                                    <td class="px-3 py-2.5 text-center">
                                        @php
                                            $ivaC = $row['pct_iva_compra'] ?? 0;
                                            $ivaCBadge = $ivaC >= 21 ? 'bg-orange-50 text-orange-700 border-orange-200'
                                                      : ($ivaC >= 10 ? 'bg-yellow-50 text-yellow-800 border-yellow-200'
                                                      : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                                        @endphp
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold border {{ $ivaCBadge }}">
                                            {{ number_format($ivaC, 1, ',', '.') }}%
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-mono text-sm text-gray-600">
                                        {{ number_format($row['precio_compra_con_iva'] ?? $row['precio_compra'], 4, ',', '.') }} €
                                    </td>
                                    <td class="px-3 py-2.5 text-center text-xs text-gray-500 font-medium">
                                        {{ $row['fecha_ultima_compra'] ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-mono text-sm text-gray-600">
                                        @if($row['precio_venta_sin_iva'] !== null)
                                            {{ number_format($row['precio_venta_sin_iva'], 4, ',', '.') }} €
                                        @else <span class="text-gray-300">—</span>@endif
                                    </td>
                                    <td class="px-3 py-2.5 text-center">
                                        @if($row['precio_venta'] !== null)
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold border {{ $ivaBadge }}">
                                                {{ number_format($iva, 1, ',', '.') }}%
                                            </span>
                                        @else <span class="text-gray-300">—</span>@endif
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-mono text-sm text-gray-600">
                                        @if($row['precio_venta'] !== null)
                                            {{ number_format($row['precio_venta'], 4, ',', '.') }} €
                                        @else <span class="text-gray-300">—</span>@endif
                                    </td>
                                    <td class="px-3 py-2.5 text-right text-xs text-gray-500 border-l-2 border-gray-100">
                                        {{ $row['uds_compradas'] > 0 ? number_format($row['uds_compradas'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right text-xs text-gray-500">
                                        {{ $row['uds_vendidas'] > 0 ? number_format($row['uds_vendidas'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-mono text-sm text-blue-700 border-l-2 border-blue-50">
                                        @if($row['total_comprado'] > 0)
                                            {{ number_format($row['total_comprado'], 2, ',', '.') }} €
                                        @else <span class="text-gray-300">—</span>@endif
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-mono text-sm text-emerald-700 border-l-2 border-emerald-50">
                                        @if($row['total_facturado'] > 0)
                                            {{ number_format($row['total_facturado'], 2, ',', '.') }} €
                                        @else <span class="text-gray-300">—</span>@endif
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-mono text-sm border-l-2 border-amber-50">
                                        @if($row['beneficio'] !== null)
                                            <span class="{{ $row['beneficio'] >= 0 ? 'text-emerald-600 font-bold' : 'text-red-600 font-bold' }}">
                                                {{ number_format($row['beneficio'], 2, ',', '.') }} €
                                            </span>
                                        @else <span class="text-gray-300">—</span>@endif
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-black border {{ $badgeR }}">
                                            @if($mReal !== null) {{ number_format($mReal, 2, ',', '.') }}%
                                            @else —@endif
                                        </span>
                                    </td>
                                </tr>
                            @else
                                {{-- ── Fila Margen Mercadería (PVP Tarifa) ─────────── --}}
                                @php
                                    $m = $row['margen_pct'];
                                    $badge = $m >= 40
                                        ? 'bg-green-100 text-green-800 border-green-200'
                                        : ($m >= 20 ? 'bg-yellow-100 text-yellow-800 border-yellow-200'
                                                    : 'bg-red-100 text-red-800 border-red-200');

                                    $ivaM = $row['pct_iva'] ?? 0;
                                    $ivaBadgeM = $ivaM >= 21 ? 'bg-orange-50 text-orange-700 border-orange-200'
                                               : ($ivaM >= 10 ? 'bg-yellow-50 text-yellow-800 border-yellow-200'
                                               : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                                @endphp
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
                                    <td class="px-4 py-2.5 text-center">
                                        @php
                                            $ivaC = $row['pct_iva_compra'] ?? 0;
                                            $ivaCBadge = $ivaC >= 21 ? 'bg-orange-50 text-orange-700 border-orange-200'
                                                      : ($ivaC >= 10 ? 'bg-yellow-50 text-yellow-800 border-yellow-200'
                                                      : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                                        @endphp
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold border {{ $ivaCBadge }}">
                                            {{ number_format($ivaC, 1, ',', '.') }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono text-sm text-gray-700">
                                        {{ number_format($row['precio_compra_con_iva'] ?? $row['precio_compra'], 4, ',', '.') }} €
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono text-sm text-gray-700">
                                        {{ number_format($row['pvp_sin_iva'], 4, ',', '.') }} €
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold border {{ $ivaBadgeM }}">
                                            {{ number_format($ivaM, 1, ',', '.') }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono text-sm text-gray-700">
                                        {{ number_format($row['pvp_con_iva'], 4, ',', '.') }} €
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-black border {{ $badge }}">
                                            {{ number_format($m, 2, ',', '.') }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-xs text-gray-400 no-print">
                                        {{ number_format($row['unidades_compradas'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
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
        </div>
        @endif {{-- fin resultados --}}

    </div>

    {{-- CSS de impresión --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background-color: white !important;
            }

            .no-print, .no-print * { 
                display: none !important; 
                visibility: hidden !important; 
                height: 0 !important; 
            }

            .print-full-width { 
                width: 100% !important; 
                max-width: 100% !important;
                box-shadow: none !important; 
                border: none !important; 
                margin: 0 !important;
            }
            body { font-size: 10px; color: black !important; }
            th, td { padding: 3px 5px !important; font-size: 10px !important; color: black !important; }
            a[href]::after { content: none !important; }
            .rounded-full { border-radius: 4px !important; }
            
            /* Evitar cortes feos en medio de filas */
            tr { page-break-inside: avoid; }
            thead { display: table-header-group; }
        }
    </style>

</x-filament-panels::page>
