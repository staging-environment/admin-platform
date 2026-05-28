<x-filament-panels::page>
    @php
        $groupedTables = $this->getTablesData();
    @endphp

    <div class="space-y-6">
        {{-- Search filter at the top --}}
        <div class="flex items-center gap-3 w-full md:w-auto p-4 bg-white dark:bg-white/5 rounded-2xl border border-gray-200 dark:border-white/10 shadow-sm">
            <div class="relative grow max-w-md">
                <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                    <x-filament::input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar tablas por nombre..."
                    />
                </x-filament::input.wrapper>
            </div>
            <div class="text-xs text-gray-400 dark:text-gray-500 font-mono">
                Total de tablas encontradas: 
                <span class="font-bold text-primary-600 dark:text-primary-400">
                    {{ collect($groupedTables)->flatten()->count() }}
                </span>
            </div>
        </div>

        {{-- Grid of Groups --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($groupedTables as $groupName => $tables)
                <div class="bg-white dark:bg-white/5 rounded-2xl border border-gray-200 dark:border-white/10 shadow-sm overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-primary-500"></span>
                                {{ $groupName }}
                            </h3>
                            <span class="text-xs font-mono font-bold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-white/10 px-2 py-0.5 rounded-lg">
                                {{ count($tables) }}
                            </span>
                        </div>
                        <div class="p-5 flex flex-wrap gap-2 max-h-[250px] overflow-y-auto">
                            @if(count($tables) === 0)
                                <p class="text-xs text-gray-400 italic">No hay tablas coincidentes en este grupo.</p>
                            @else
                                @foreach($tables as $tableName)
                                    <button
                                        onclick="window.open('{{ route('db.preview', ['connection' => 'sii', 'table' => $tableName]) }}', 'sii_table_{{ $tableName }}', 'width=1100,height=750,resizable=yes,scrollbars=yes')"
                                        class="px-2.5 py-1 text-xs font-medium bg-gray-100 dark:bg-white/5 hover:bg-primary-50 dark:hover:bg-primary-950/20 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg border border-gray-200/50 dark:border-white/10 transition-colors duration-150 text-left truncate max-w-full font-mono cursor-pointer"
                                        title="{{ $tableName }}"
                                    >
                                        {{ $tableName }}
                                    </button>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
