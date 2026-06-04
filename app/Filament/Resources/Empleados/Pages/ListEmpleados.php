<?php

namespace App\Filament\Resources\Empleados\Pages;

use App\Filament\Resources\Empleados\EmpleadoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmpleados extends ListRecords
{
    protected static string $resource = EmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('importFromVirtus')
                ->label('Importar desde Virtus')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Importar Empleados de Virtus')
                ->modalDescription('Esto importará los empleados desde la base de datos de Virtusgesnet que no existan todavía.')
                ->action(function () {
                    $expendedores = \Illuminate\Support\Facades\DB::connection('virtusgesnet')
                        ->table('expendedores')
                        ->where('EnBaja', 0) // maybe only active ones?
                        ->get();

                    $count = 0;
                    foreach ($expendedores as $exp) {
                        // Split full name into nombre and apellidos
                        $parts = explode(' ', trim($exp->Nombre));
                        $nombre = array_shift($parts);
                        $apellidos = count($parts) > 0 ? implode(' ', $parts) : null;
                        
                        // Generate dummy DNI since virtus doesn't have it
                        $dummyDni = 'VIRTUS-' . str_pad($exp->Codigo, 4, '0', STR_PAD_LEFT);

                        \App\Models\Empleado::updateOrCreate(
                            ['virtus_codigo' => $exp->Codigo],
                            [
                                'nombre' => $nombre,
                                'apellidos' => $apellidos,
                                'dni' => $dummyDni,
                                'telefono_principal' => $exp->Telefono ?? $exp->Movil,
                                'telefono_secundario' => ($exp->Telefono && $exp->Movil) ? $exp->Movil : null,
                                'direccion' => $exp->Domicilio,
                                'localidad' => $exp->Poblacion,
                                'provincia' => $exp->Provincia,
                                'codigo_postal' => $exp->DP,
                                'email' => $exp->Email,
                            ]
                        );
                        $count++;
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Importación completada')
                        ->body("Se han importado $count empleados nuevos.")
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
