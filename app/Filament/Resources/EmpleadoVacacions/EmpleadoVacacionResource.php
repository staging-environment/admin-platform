<?php

namespace App\Filament\Resources\EmpleadoVacacions;

use App\Filament\Resources\EmpleadoVacacions\Pages\ManageEmpleadoVacacions;
use App\Models\EmpleadoVacacion;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class EmpleadoVacacionResource extends Resource
{
    protected static ?string $model = EmpleadoVacacion::class;

    protected static ?string $slug = 'solicitudes-vacaciones';
    protected static ?string $modelLabel = 'Solicitud de Vacación/Permiso';
    protected static ?string $pluralModelLabel = 'Solicitudes de Vacaciones';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static string|\UnitEnum|null $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $user->unsetRelation('roles')->unsetRelation('permissions');

        return $user->can('aprobacion_vacaciones_bajas');
    }

    public static function getNavigationLabel(): string
    {
        return 'Solicitudes de Vacaciones';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Form configuration
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('empleado.nombre')
                    ->label('Nombre Empleado')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->empleado ? $record->empleado->nombre . ' ' . $record->empleado->apellidos : 'N/A'),
                
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),
                    
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('dias_solicitados')
                    ->label('Días'),

                TextColumn::make('comentario_empleado')
                    ->label('Detalle')
                    ->limit(30),
                    
                TextColumn::make('estado')
                    ->label('Estado de Validación')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aceptada', 'Aceptadas', 'Aprobada' => 'success',
                        'Rechazada', 'Denegada', 'Denegadas' => 'danger',
                        'Pendiente' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado de Validación')
                    ->options([
                        'Pendiente' => 'Nuevas (Pendientes)',
                        'Aceptada' => 'Aprobadas',
                        'Rechazada' => 'Denegadas',
                    ])
                    ->default('Pendiente'),
                \Filament\Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo de Solicitud')
                    ->options([
                        'Vacaciones' => 'Vacaciones',
                        'Permisos' => 'Permiso Retribuido',
                    ]),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->recordActions([
                Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->estado === 'Pendiente')
                    ->action(function ($record) {
                        $record->update(['estado' => 'Aceptada']);
                        
                        self::notificarEmpleado($record, 'Aceptada');
                        
                        Notification::make()
                            ->title('Solicitud Aprobada')
                            ->success()
                            ->send();
                    }),

                Action::make('denegar')
                    ->label('Denegar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Denegar Solicitud de Vacaciones')
                    ->modalDescription('¿Estás seguro de que deseas denegar esta solicitud? Puedes indicar opcionalmente el motivo a continuación:')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('comentario_aprobador')
                            ->label('Motivo de la denegación (Opcional)')
                            ->placeholder('Escribe aquí el motivo o explicación...')
                            ->rows(3),
                    ])
                    ->visible(fn ($record) => $record->estado === 'Pendiente')
                    ->action(function ($record, array $data) {
                        $motivo = $data['comentario_aprobador'] ?? null;

                        $record->update([
                            'estado' => 'Rechazada',
                            'comentario_aprobador' => $motivo,
                        ]);
                        
                        self::notificarEmpleado($record, 'Rechazada', $motivo);
                        
                        Notification::make()
                            ->title('Solicitud Denegada')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmpleadoVacacions::route('/'),
        ];
    }
    
    protected static function notificarEmpleado($record, $estado, $comentario = null)
    {
        if ($record->empleado && $record->empleado->email) {
            $user = User::where('email', $record->empleado->email)->first();
            if ($user) {
                $mensaje = "Tu solicitud de {$record->tipo} (del " . \Carbon\Carbon::parse($record->fecha_inicio)->format('d/m/Y') . " al " . \Carbon\Carbon::parse($record->fecha_fin)->format('d/m/Y') . ") ha sido " . ($estado === 'Aceptada' ? 'aprobada' : 'denegada') . ".";
                if ($comentario) {
                    $mensaje .= " Motivo: {$comentario}";
                }
                
                Notification::make()
                    ->title("Solicitud de {$record->tipo} " . ($estado === 'Aceptada' ? 'Aprobada' : 'Denegada'))
                    ->body($mensaje)
                    ->icon($estado === 'Aceptada' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->iconColor($estado === 'Aceptada' ? 'success' : 'danger')
                    ->sendToDatabase($user);
            }
        }
    }
}
