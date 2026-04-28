<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Dashboard - VirtusGesNet Admin')); ?>

            </h2>
            <div class="text-sm text-gray-600">
                Usuario: <?php echo e(auth()->user()->name); ?> | Rol: <?php echo e(auth()->user()->getRoleNames()->first() ?? 'Sin rol'); ?>

            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Error Message -->
            <?php if(isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <strong>Error:</strong> <?php echo e($error); ?>

                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Tablas Admin Corp</dt>
                                    <dd class="text-lg font-medium text-gray-900"><?php echo e(count($tablesAdmin)); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Tablas VirtusGesNet</dt>
                                    <dd class="text-lg font-medium text-gray-900"><?php echo e(count($tablesVirtus)); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Permisos</dt>
                                    <dd class="text-lg font-medium text-gray-900"><?php echo e(auth()->user()->getAllPermissions()->count()); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Estado</dt>
                                    <dd class="text-lg font-medium text-green-600">Conectado</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Tables Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Administracion Corporativa Tables -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Administración Corporativa</h3>
                            <span class="text-sm text-gray-500"><?php echo e(count($tablesAdmin)); ?> tablas</span>
                        </div>

                        <div class="mb-4">
                            <input type="text" id="search-admin" placeholder="Buscar tablas..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <div id="tables-admin-list" class="space-y-2">
                                <?php $__currentLoopData = $tablesAdmin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="table-item flex justify-between items-center p-3 bg-gray-50 rounded hover:bg-gray-100 cursor-pointer"
                                         data-table="<?php echo e($table); ?>" data-db="admin">
                                        <span class="font-medium"><?php echo e($table); ?></span>
                                        <button class="query-table-btn text-blue-600 hover:text-blue-800 text-sm"
                                                data-table="<?php echo e($table); ?>" data-db="admin">
                                            Consultar
                                        </button>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VirtusGesNet Tables -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">VirtusGesNet</h3>
                            <span class="text-sm text-gray-500"><?php echo e(count($tablesVirtus)); ?> tablas</span>
                        </div>

                        <div class="mb-4">
                            <input type="text" id="search-virtus" placeholder="Buscar tablas..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <div id="tables-virtus-list" class="space-y-2">
                                <?php $__currentLoopData = $tablesVirtus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="table-item flex justify-between items-center p-3 bg-gray-50 rounded hover:bg-gray-100 cursor-pointer"
                                         data-table="<?php echo e($table); ?>" data-db="virtus">
                                        <span class="font-medium"><?php echo e($table); ?></span>
                                        <button class="query-table-btn text-green-600 hover:text-green-800 text-sm"
                                                data-table="<?php echo e($table); ?>" data-db="virtus">
                                            Consultar
                                        </button>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Query Results Modal -->
            <div id="query-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900" id="modal-title">Consultando tabla...</h3>
                            <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div id="query-results" class="max-h-96 overflow-y-auto">
                            <div class="text-center py-8">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                                <p class="mt-4 text-gray-600">Cargando datos...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            console.log('JS CARGADO 🔥');

            // Search functionality
            document.getElementById('search-admin').addEventListener('input', function(e) {
                filterTables('tables-admin-list', e.target.value);
            });

            document.getElementById('search-virtus').addEventListener('input', function(e) {
                filterTables('tables-virtus-list', e.target.value);
            });

            function filterTables(containerId, searchTerm) {
                const container = document.getElementById(containerId);
                const items = container.querySelectorAll('.table-item');

                items.forEach(item => {
                    const tableName = item.querySelector('span').textContent.toLowerCase();
                    item.style.display = tableName.includes(searchTerm.toLowerCase()) ? 'flex' : 'none';
                });
            }

            // BOTONES CONSULTAR
            document.querySelectorAll('.query-table-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    console.log('CLICK 🔥');

                    const table = this.getAttribute('data-table');
                    const db = this.getAttribute('data-db');
                    openQueryModal(table, db);
                });
            });

            function openQueryModal(table, db) {
                console.log('FETCH 🔥', table, db);

                const modal = document.getElementById('query-modal');
                const title = document.getElementById('modal-title');
                const results = document.getElementById('query-results');

                const dbName = db === 'admin' ? 'administracioncorporativa' : 'virtusgesnet';
                const displayName = db === 'admin' ? 'Admin Corp' : 'VirtusGesNet';

                title.textContent = `Consultando tabla: ${table} (${displayName})`;

                results.innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-4 text-gray-600">Cargando datos...</p>
            </div>
        `;

                modal.classList.remove('hidden');

                 window.axios.post('/api/data/query', {
                         database: dbName,
                         table: table,
                         limit: 50
                     })
                     .then(response => displayQueryResults(response.data, table))
                     .catch(error => {
                         console.error('Error fetching data:', error);
                         results.innerHTML = `
                 <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                     Error: ${error.response?.data?.message || error.message}
                 </div>
             `;
                     });
            }

            function displayQueryResults(responseData, tableName) {
                const results = document.getElementById('query-results');

                // Extract the actual data array from the response
                const tableData = responseData.data?.data || [];
                const total = responseData.data?.total || 0;

                if (responseData.success && tableData.length > 0) {
                    let html = `
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Mostrando ${tableData.length} de ${total} registros de "${tableName}"
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
            `;

                    if (tableData.length > 0) {
                        Object.keys(tableData[0]).forEach(key => {
                            html += `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">${key}</th>`;
                        });
                    }

                    html += `</tr></thead><tbody class="bg-white divide-y divide-gray-200">`;

                    tableData.forEach(row => {
                        html += '<tr>';
                        Object.values(row).forEach(value => {
                            html += `<td class="px-6 py-4 text-sm">${value ?? 'NULL'}</td>`;
                        });
                        html += '</tr>';
                    });

                    html += `</tbody></table></div>`;
                    results.innerHTML = html;

                } else {
                    results.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-gray-600">Sin datos en "${tableName}"</p>
                </div>
            `;
                }
            }

            // Cerrar modal
            document.getElementById('close-modal').addEventListener('click', function() {
                document.getElementById('query-modal').classList.add('hidden');
            });

            document.getElementById('query-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });

        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/dashboard.blade.php ENDPATH**/ ?>