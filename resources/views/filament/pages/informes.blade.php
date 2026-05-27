<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">
                        Compras
                    </h3>

                    <p class="mt-2 text-sm text-gray-600">
                        Tablas candidatas relacionadas con compras, proveedores, entradas o recepciones.
                    </p>

                    <div class="mt-6">
                        @if(count($tableGroups['compras'] ?? []) > 0)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ count($tableGroups['compras']) }} tablas detectadas
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Sin tablas detectadas
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">
                        Ventas
                    </h3>

                    <p class="mt-2 text-sm text-gray-600">
                        Tablas candidatas relacionadas con ventas, facturas, albaranes, tickets o pedidos.
                    </p>

                    <div class="mt-6">
                        @if(count($tableGroups['ventas'] ?? []) > 0)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ count($tableGroups['ventas']) }} tablas detectadas
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Sin tablas detectadas
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">
                        Rentabilidad
                    </h3>

                    <p class="mt-2 text-sm text-gray-600">
                        Cruce futuro entre compras y ventas para detectar márgenes, desviaciones y oportunidades.
                    </p>

                    <div class="mt-6">
                        @if(count($tableGroups['compras'] ?? []) > 0 && count($tableGroups['ventas'] ?? []) > 0)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Base de datos candidata localizada
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Faltan tablas por identificar
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">
                        Ideas iniciales de informes
                    </h3>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800">
                                Evolución mensual de ventas
                            </h4>

                            <p class="mt-2 text-sm text-gray-600">
                                Comparativa de ventas por mes, negocio, cliente o línea de producto.
                            </p>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800">
                                Top clientes y productos
                            </h4>

                            <p class="mt-2 text-sm text-gray-600">
                                Ranking de clientes, artículos o familias con mayor facturación.
                            </p>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800">
                                Control de compras
                            </h4>

                            <p class="mt-2 text-sm text-gray-600">
                                Análisis de compras por proveedor, producto, periodo y variación de precios.
                            </p>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800">
                                Margen estimado
                            </h4>

                            <p class="mt-2 text-sm text-gray-600">
                                Cruce entre precios de compra y ventas para analizar rentabilidad aproximada.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</x-filament-panels::page>
