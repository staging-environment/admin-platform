<x-filament-panels::page>
    @php
        $items = $this->getItems();
        $allItemsSerialized = collect($items)->map(fn($item) => $item['path'] . '|' . $item['type'])->toArray();
        $allSelected = count($allItemsSerialized) > 0 && collect($allItemsSerialized)->every(fn($i) => in_array($i, $selectedItems));
    @endphp
    <div class="space-y-6">
        {{-- Toolbar: Disk switcher, Search input, View selector --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- Tabs for Disks --}}
            <div class="flex items-center gap-1.5 p-1.5 bg-gray-100 dark:bg-white/5 rounded-xl border border-gray-200/50 dark:border-white/10 shrink-0">
                @foreach($this->getDisks() as $diskKey => $diskName)
                    <button
                        wire:click="selectDisk('{{ $diskKey }}')"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 {{ $selectedDisk === $diskKey ? 'bg-white dark:bg-white/10 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                    >
                        {{ $diskName }}
                    </button>
                @endforeach
            </div>

            {{-- Search and View Mode Switcher --}}
            <div class="flex items-center gap-3 w-full md:w-auto">
                {{-- Search input --}}
                <div class="relative grow md:w-64">
                    <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                        <x-filament::input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Buscar en este directorio..."
                        />
                    </x-filament::input.wrapper>
                </div>

                {{-- Select All Button --}}
                @if (!empty($items))
                    <x-filament::button
                        wire:click="toggleSelectAll"
                        color="gray"
                        size="sm"
                        icon="heroicon-o-check-circle"
                        class="shrink-0"
                    >
                        {{ $allSelected ? 'Desmarcar todos' : 'Seleccionar todos' }}
                    </x-filament::button>
                @endif

                {{-- View Mode Toggle --}}
                <div class="flex items-center p-1 bg-gray-100 dark:bg-white/5 rounded-xl border border-gray-200/50 dark:border-white/10 shrink-0">
                    <button
                        wire:click="$set('viewMode', 'grid')"
                        class="p-2 rounded-lg transition-all duration-150 {{ $viewMode === 'grid' ? 'bg-white dark:bg-white/10 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}"
                        title="Vista de cuadrícula"
                    >
                        <x-heroicon-o-squares-2x2 class="w-4 h-4" />
                    </button>
                    <button
                        wire:click="$set('viewMode', 'list')"
                        class="p-2 rounded-lg transition-all duration-150 {{ $viewMode === 'list' ? 'bg-white dark:bg-white/10 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}"
                        title="Vista de lista"
                    >
                        <x-heroicon-o-list-bullet class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        {{-- Breadcrumbs navigation bar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200/50 dark:border-white/5 shadow-sm">
            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 overflow-x-auto py-1">
                <x-heroicon-m-folder class="w-4 h-4 text-gray-400 shrink-0" />
                @foreach($this->getExplorerBreadcrumbs() as $index => $breadcrumb)
                    @if($index > 0)
                        <x-heroicon-m-chevron-right class="w-3.5 h-3.5 text-gray-300 dark:text-white/10 shrink-0" />
                    @endif
                    @if($loop->last)
                        <span class="font-bold text-gray-900 dark:text-white truncate max-w-[200px]">
                            {{ $breadcrumb['label'] }}
                        </span>
                    @else
                        <button
                            wire:click="goToPath('{{ addslashes($breadcrumb['path']) }}')"
                            class="hover:text-primary-600 dark:hover:text-primary-400 transition font-medium focus:outline-none focus:underline"
                        >
                            {{ $breadcrumb['label'] }}
                        </button>
                    @endif
                @endforeach
            </div>
            <div class="text-xs text-gray-400 dark:text-gray-500 font-mono shrink-0">
                Disco: <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $selectedDisk }}</span>
            </div>
        </div>

        {{-- Bulk Actions Bar --}}
        @if (count($selectedItems) > 0)
            <div class="flex items-center justify-between p-3 bg-danger-50 dark:bg-danger-950/20 rounded-xl border border-danger-200/50 dark:border-danger-500/10 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-danger-700 dark:text-danger-400 font-mono">
                        {{ count($selectedItems) }} {{ count($selectedItems) === 1 ? 'elemento seleccionado' : 'elementos seleccionados' }}
                    </span>
                    <button
                        wire:click="$set('selectedItems', [])"
                        class="text-[10px] font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline"
                    >
                        Desmarcar todos
                    </button>
                </div>
                <div>
                    <x-filament::button
                        wire:click="mountAction('deleteSelected')"
                        color="danger"
                        size="sm"
                        icon="heroicon-o-trash"
                    >
                        Eliminar seleccionados
                    </x-filament::button>
                </div>
            </div>
        @endif

        {{-- Items display container --}}

        <div class="relative">
            @if (empty($items))
                <div class="flex flex-col items-center justify-center py-24 px-4 bg-white dark:bg-white/5 rounded-2xl border border-dashed border-gray-200 dark:border-white/10 shadow-sm text-center">
                    <div class="p-4 rounded-full bg-gray-50 dark:bg-white/5 text-gray-400 dark:text-gray-500 mb-4 animate-pulse">
                        <x-heroicon-o-folder-open class="w-12 h-12" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Este directorio está vacío</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm">
                        @if($search)
                            No se encontraron elementos que coincidan con la búsqueda "{{ $search }}".
                        @else
                            No hay archivos ni carpetas en esta ubicación.
                        @endif
                    </p>
                    @if($search)
                        <button
                            wire:click="$set('search', '')"
                            class="mt-4 text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline"
                        >
                            Limpiar búsqueda
                        </button>
                    @endif
                </div>
            @else
                @if ($viewMode === 'grid')
                    {{-- Grid View with modern card design and overlays --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach ($items as $item)
                            @php
                                $itemSerialized = $item['path'] . '|' . $item['type'];
                                $isSelected = in_array($itemSerialized, $selectedItems);
                            @endphp
                            <div
                                class="group relative flex flex-col items-center justify-between p-4 rounded-2xl {{ $isSelected ? 'bg-primary-500/5 dark:bg-primary-500/10 border-primary-500/50' : 'bg-white dark:bg-white/5 border-gray-200 dark:border-white/10' }} shadow-sm hover:shadow-md hover:border-primary-500 dark:hover:border-primary-400/50 hover:bg-gray-50/50 dark:hover:bg-white/10 transition-all duration-200 cursor-pointer"
                                @if ($item['type'] === 'folder')
                                    wire:click="goToPath('{{ addslashes($item['path']) }}')"
                                @else
                                    wire:click="mountAction('previewFile', { path: '{{ addslashes($item['path']) }}' })"
                                @endif
                            >
                                {{-- Checkbox Selection --}}
                                <div class="absolute top-3 left-3 z-10 {{ $isSelected ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100' }} transition-opacity duration-200" onclick="event.stopPropagation();">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedItems"
                                        value="{{ $itemSerialized }}"
                                        class="rounded border-gray-300 dark:border-white/10 text-primary-600 shadow-sm focus:ring-primary-500 focus:ring-offset-0 dark:bg-white/5 dark:checked:bg-primary-500 w-4 h-4 cursor-pointer focus:ring-1 transition-all duration-150"
                                    />
                                </div>

                                <div class="flex flex-col items-center text-center space-y-3 w-full">
                                    <div class="relative p-1 rounded-xl bg-gray-50 dark:bg-white/5 group-hover:bg-primary-50 dark:group-hover:bg-primary-950/20 transition-colors duration-200 w-16 h-16 flex items-center justify-center overflow-hidden border border-gray-200/50 dark:border-white/10 pointer-events-none">
                                        @if (isset($item['url']) && $item['url'])
                                            <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover rounded-lg shadow-sm" />
                                        @else
                                            <x-dynamic-component :component="$item['icon']" class="w-10 h-10 {{ $item['color'] }}" />
                                        @endif
                                    </div>

                                    <div class="w-full space-y-0.5">
                                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition" title="{{ $item['name'] }}">
                                            {{ $item['name'] }}
                                        </h4>
                                        <p class="text-[10px] text-gray-400 font-mono">
                                            @if ($item['type'] === 'folder')
                                                Carpeta
                                            @else
                                                {{ $item['size'] }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Quick Actions on Hover --}}
                                <div class="w-full flex items-center justify-center gap-1.5 mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200" onclick="event.stopPropagation();">
                                    @if ($item['type'] === 'file')
                                        <button
                                            wire:click="mountAction('previewFile', { path: '{{ addslashes($item['path']) }}' })"
                                            class="p-1.5 rounded-lg bg-gray-100 dark:bg-white/10 hover:bg-primary-600 dark:hover:bg-primary-500 hover:text-white text-gray-500 dark:text-gray-400 transition-colors"
                                            title="Previsualizar"
                                        >
                                            <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                        </button>
                                        <button
                                            wire:click="downloadFile('{{ addslashes($item['path']) }}')"
                                            class="p-1.5 rounded-lg bg-gray-100 dark:bg-white/10 hover:bg-primary-600 dark:hover:bg-primary-500 hover:text-white text-gray-500 dark:text-gray-400 transition-colors"
                                            title="Descargar"
                                        >
                                            <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" />
                                        </button>
                                    @endif

                                    <button
                                        wire:click="mountAction('deleteItem', { path: '{{ addslashes($item['path']) }}', type: '{{ $item['type'] }}' })"
                                        class="p-1.5 rounded-lg bg-gray-100 dark:bg-white/10 hover:bg-danger-600 dark:hover:bg-danger-500 hover:text-white text-gray-500 dark:text-gray-400 transition-colors"
                                        title="Eliminar"
                                    >
                                        <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- List View - Table design --}}
                    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-white/10 shadow-sm bg-white dark:bg-white/5">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">
                                        <input
                                            type="checkbox"
                                            wire:click="toggleSelectAll"
                                            @if($allSelected) checked @endif
                                            class="rounded border-gray-300 dark:border-white/10 text-primary-600 shadow-sm focus:ring-primary-500 focus:ring-offset-0 dark:bg-white/5 dark:checked:bg-primary-500 w-4 h-4 cursor-pointer focus:ring-1 transition-all duration-150"
                                        />
                                    </th>
                                    <th class="p-4">Nombre</th>
                                    <th class="p-4 hidden sm:table-cell">Última Modificación</th>
                                    <th class="p-4">Tamaño</th>
                                    <th class="p-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                                @foreach ($items as $item)
                                    @php
                                        $itemSerialized = $item['path'] . '|' . $item['type'];
                                        $isSelected = in_array($itemSerialized, $selectedItems);
                                    @endphp
                                    <tr
                                        class="{{ $isSelected ? 'bg-primary-500/5 dark:bg-primary-500/10' : '' }} hover:bg-gray-50/50 dark:hover:bg-white/10 transition-colors duration-150 text-sm cursor-pointer group"
                                        @if ($item['type'] === 'folder')
                                            wire:click="goToPath('{{ addslashes($item['path']) }}')"
                                        @else
                                            wire:click="mountAction('previewFile', { path: '{{ addslashes($item['path']) }}' })"
                                        @endif
                                    >
                                        <td class="p-4 w-12 text-center" onclick="event.stopPropagation();">
                                            <input
                                                type="checkbox"
                                                wire:model.live="selectedItems"
                                                value="{{ $itemSerialized }}"
                                                class="rounded border-gray-300 dark:border-white/10 text-primary-600 shadow-sm focus:ring-primary-500 focus:ring-offset-0 dark:bg-white/5 dark:checked:bg-primary-500 w-4 h-4 cursor-pointer focus:ring-1 transition-all duration-150"
                                            />
                                        </td>
                                        <td class="p-4 font-medium text-gray-900 dark:text-white flex items-center gap-3">
                                            @if (isset($item['url']) && $item['url'])
                                                <div class="w-7 h-7 rounded bg-gray-50 dark:bg-white/5 overflow-hidden border border-gray-200/30 dark:border-white/5 shrink-0 flex items-center justify-center pointer-events-none">
                                                    <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover" />
                                                </div>
                                            @else
                                                <x-dynamic-component :component="$item['icon']" class="w-5 h-5 {{ $item['color'] }} shrink-0 pointer-events-none" />
                                            @endif
                                            <span class="truncate max-w-[200px] sm:max-w-md group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" title="{{ $item['name'] }}">
                                                {{ $item['name'] }}
                                            </span>
                                        </td>
                                        <td class="p-4 hidden sm:table-cell text-gray-500 dark:text-gray-400 text-xs font-mono">
                                            {{ $item['last_modified'] }}
                                        </td>
                                        <td class="p-4 text-gray-500 dark:text-gray-400 text-xs font-mono">
                                            @if ($item['type'] === 'folder')
                                                <span class="text-gray-400">--</span>
                                            @else
                                                {{ $item['size'] }}
                                            @endif
                                        </td>
                                        <td class="p-4 text-right" onclick="event.stopPropagation();">
                                            <div class="inline-flex items-center gap-1.5">
                                                @if ($item['type'] === 'file')
                                                    <button
                                                        wire:click="mountAction('previewFile', { path: '{{ addslashes($item['path']) }}' })"
                                                        class="p-1.5 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-950/20 text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                                                        title="Previsualizar"
                                                    >
                                                        <x-heroicon-o-eye class="w-4 h-4" />
                                                    </button>
                                                    <button
                                                        wire:click="downloadFile('{{ addslashes($item['path']) }}')"
                                                        class="p-1.5 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-950/20 text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                                                        title="Descargar"
                                                    >
                                                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                                    </button>
                                                @endif
                                                <button
                                                    wire:click="mountAction('deleteItem', { path: '{{ addslashes($item['path']) }}', type: '{{ $item['type'] }}' })"
                                                    class="p-1.5 rounded-lg hover:bg-danger-50 dark:hover:bg-danger-950/20 text-gray-500 dark:text-gray-400 hover:text-danger-600 dark:hover:text-danger-400 transition-colors"
                                                    title="Eliminar"
                                                >
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
