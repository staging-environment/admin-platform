<?php

namespace App\Filament\Resources\Empleados\Pages;

use App\Filament\Resources\Empleados\EmpleadoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmpleado extends EditRecord
{
    protected static string $resource = EmpleadoResource::class;

    public function content(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(1)
                            ->schema([
                                $this->getFormContentComponent(),
                            ])
                            ->columnSpan(2)
                            ->extraAttributes(['class' => 'ficha-empleado-container']),
                        $this->getRelationManagersContentComponent()
                            ->columnSpan(1)
                            ->extraAttributes(['class' => 'documentos-relation-container max-h-[600px] overflow-y-auto pr-2']),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
