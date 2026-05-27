<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Panel de informes basado principalmente en la base de datos VirtusGesNet.
                </p>
            </div>

            <div class="text-sm text-gray-600">
                Tablas VirtusGesNet: {{ count($tables) }}
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
</x-app-layout>
