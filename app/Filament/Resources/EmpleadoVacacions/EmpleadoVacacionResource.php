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
        return $user->hasRole('Admin') 
            || $user->hasRole('admin') 
            || $user->hasRole('Gestor') 
            || $user->hasRole('gestor') 
            || $user->can('aprobacion_vacaciones')
            || $user->can('aprobacion_vacaciones_bajas')
            || $user->can('solicitar_ver_vacaciones')
            || $user->can('gestion_recursos_humanos');
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
                    ->date()
                    ->sortable(),
                    
                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date()
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
                // Optionally filter by status
            ])
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
                    ->visible(fn ($record) => $record->estado === 'Pendiente')
                    ->action(function ($record) {
                        $record->update(['estado' => 'Rechazada']);
                        
                        self::notificarEmpleado($record, 'Rechazada');
                        
                        Notification::make()
                            ->title('Solicitud Rechazada')
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
    
    protected static function notificarEmpleado($record, $estado)
    {
        if ($record->empleado && $record->empleado->email) {
            $user = User::where('email', $record->empleado->email)->first();
            if ($user) {
                $mensaje = "Tu solicitud de {$record->tipo} (del " . \Carbon\Carbon::parse($record->fecha_inicio)->format('d/m/Y') . " al " . \Carbon\Carbon::parse($record->fecha_fin)->format('d/m/Y') . ") ha sido {$estado}.";
                
                Notification::make()
                    ->title("Solicitud de {$record->tipo} {$estado}")
                    ->body($mensaje)
                    ->icon($estado === 'Aceptada' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->iconColor($estado === 'Aceptada' ? 'success' : 'danger')
                    ->sendToDatabase($user);
            }
        }
    }
}
