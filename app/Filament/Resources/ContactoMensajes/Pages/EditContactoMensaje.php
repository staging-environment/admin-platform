<?php

namespace App\Filament\Resources\ContactoMensajes\Pages;

use App\Filament\Resources\ContactoMensajes\ContactoMensajeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

use App\Filament\Traits\HasMenuBreadcrumbs;
class EditContactoMensaje extends EditRecord
{
    use HasMenuBreadcrumbs;

    protected static string $resource = ContactoMensajeResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (!$this->record->is_read) {
            $this->record->update(['is_read' => true]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
