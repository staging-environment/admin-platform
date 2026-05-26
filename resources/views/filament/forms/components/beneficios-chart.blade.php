<div class="mt-4">
    @if($getRecord())
        @livewire(\App\Filament\Widgets\BeneficiosChart::class, ['record' => $getRecord()])
    @else
        <div class="p-4 bg-gray-50 border rounded-lg text-center text-gray-500">
            Guarda la gasolinera para ver la gráfica de beneficios.
        </div>
    @endif
</div>
