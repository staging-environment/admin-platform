<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

// Widget reservado para futuras gráficas de beneficios por gasolinera.
class BeneficiosChart extends ChartWidget
{
    public ?\App\Models\Gasolinera $record = null;

    protected ?string $heading = 'Beneficios';

    protected function getData(): array
    {
        return [];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
