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
                ->url(fn () => url('/estacion/' . \Illuminate\Support\Str::slug($this->getRecord()->Nombre)))
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

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $contenido = $record->contenido;
        if ($contenido && !empty($contenido->slider_images)) {
            $images = is_string($contenido->slider_images) ? json_decode($contenido->slider_images, true) : $contenido->slider_images;
            if (is_array($images)) {
                foreach ($images as $image) {
                    $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($image);
                    \App\Services\ImageHelper::autoCropImageToRatio($fullPath, 3.5);
                }
            }
        }
    }
}
