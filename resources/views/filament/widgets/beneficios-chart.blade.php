@php
    use Filament\Widgets\View\Components\ChartWidgetComponent;
    use Illuminate\View\ComponentAttributeBag;

    $color = $this->getColor();
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $isCollapsible = $this->isCollapsible();
    $type = $this->getType();
    $maxHeight = $this->getMaxHeight();
    $hasMaxHeight = filled($maxHeight) && $maxHeight !== '100%';
@endphp

<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section
        :description="$description"
        :heading="$heading"
        :collapsible="$isCollapsible"
    >
        <x-slot name="afterHeader">
            <x-filament::input.wrapper class="fi-wi-chart-filter">
                <x-filament::input.select wire:model.live="filter">
                    <option value="6">6 meses</option>
                    <option value="12">12 meses</option>
                    <option value="year">Año {{ date('Y') }}</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </x-slot>

        <!-- Segmented control for business area -->
        <div class="mb-3">
            <div class="grid grid-cols-3 gap-1 p-1 bg-gray-100 dark:bg-gray-800/80 rounded-lg text-xs font-medium">
                <button
                    type="button"
                    wire:click="setTipoNegocio('total')"
                    class="py-1 px-1 rounded-md transition-all text-center {{ $tipoNegocio === 'total' ? 'bg-white dark:bg-gray-700 text-primary-600 dark:text-primary-400 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }}"
                >
                    Todo
                </button>
                <button
                    type="button"
                    wire:click="setTipoNegocio('combustible')"
                    class="py-1 px-1 rounded-md transition-all text-center {{ $tipoNegocio === 'combustible' ? 'bg-white dark:bg-gray-700 text-primary-600 dark:text-primary-400 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }}"
                >
                    Combustible
                </button>
                <button
                    type="button"
                    wire:click="setTipoNegocio('tienda')"
                    class="py-1 px-1 rounded-md transition-all text-center {{ $tipoNegocio === 'tienda' ? 'bg-white dark:bg-gray-700 text-primary-600 dark:text-primary-400 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }}"
                >
                    Tienda
                </button>
            </div>
        </div>

        <div
            @if ($pollingInterval = $this->getPollingInterval())
                wire:poll.{{ $pollingInterval }}="updateChartData"
            @endif
        >
            <div
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                data-chart-type="{{ $type }}"
                x-data="chart({
                            cachedData: @js($this->getCachedData()),
                            options: @js($this->getOptions()),
                            type: @js($type),
                        })"
                {{
                    (new ComponentAttributeBag)
                        ->color(ChartWidgetComponent::class, $color)
                        ->class([
                            'fi-wi-chart-canvas-ctn',
                            'fi-wi-chart-canvas-ctn-no-aspect-ratio' => $hasMaxHeight,
                        ])
                }}
            >
                <canvas
                    x-ref="canvas"
                    @style([
                        'width: 100%',
                        'height: 100%; max-height: 100%' => ! $hasMaxHeight,
                        "max-height: {$maxHeight}" => $hasMaxHeight,
                    ])
                ></canvas>

                <span
                    x-ref="backgroundColorElement"
                    class="fi-wi-chart-bg-color"
                ></span>

                <span
                    x-ref="borderColorElement"
                    class="fi-wi-chart-border-color"
                ></span>

                <span
                    x-ref="gridColorElement"
                    class="fi-wi-chart-grid-color"
                ></span>

                <span
                    x-ref="textColorElement"
                    class="fi-wi-chart-text-color"
                ></span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
