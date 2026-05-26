<?php

namespace App\Filament\Resources\Gasolineras\Pages;

use App\Filament\Resources\Gasolineras\GasolineraResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGasolinera extends EditRecord
{
    protected static string $resource = GasolineraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('visitar')
                ->label('Visitar zona pública')
                ->icon('heroicon-o-globe-alt')
                ->color('info')
                ->url(fn () => url('/estacion/' . $this->getRecord()->Codigo))
                ->openUrlInNewTab(),
        ];
    }

    public function getFormActionsAlignment(): \Filament\Support\Enums\Alignment|string
    {
        return \Filament\Support\Enums\Alignment::End;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->button()
                ->label('Guardar cambios')
                ->color('success'),
            $this->getCancelFormAction()
                ->button()
                ->label('Cancelar')
                ->color('danger'),
        ];
    }
}
