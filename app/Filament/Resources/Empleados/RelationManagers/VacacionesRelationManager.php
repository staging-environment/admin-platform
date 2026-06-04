<?php

namespace App\Filament\Resources\Empleados\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VacacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'vacaciones';

    protected static ?string $title = 'Vacaciones y Permisos';
    protected static ?string $modelLabel = 'Solicitud de Vacación/Permiso';
    protected static ?string $pluralModelLabel = 'Vacaciones y Permisos';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('gestion_vacaciones_empleados');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'Vacaciones' => 'Vacaciones',
                        'Permisos' => 'Permisos retribuidos/asuntos propios',
                    ])
                    ->required(),
                DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required(),
                DatePicker::make('fecha_fin')
                    ->label('Fecha de Fin')
                    ->required(),
                TextInput::make('dias_solicitados')
                    ->label('Días Solicitados')
                    ->numeric()
                    ->required(),
                Select::make('estado')
                    ->label('Estado de Solicitud')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Aceptada' => 'Aceptada',
                        'Rechazada' => 'Rechazada',
                    ])
                    ->default('Pendiente')
                    ->required()
                    ->disabled(fn () => !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Gestor') && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('gestor'))
                    ->dehydrated(true),
                TextInput::make('dias_disponibles')
                    ->label('Días Disponibles Restantes (Opcional)')
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo')
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date(),
                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date(),
                TextColumn::make('dias_solicitados')
                    ->label('Días'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aceptada' => 'success',
                        'Rechazada' => 'danger',
                        'Pendiente' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('dias_disponibles')
                    ->label('Disponibles (Saldo)'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                \Filament\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->estado === 'Pendiente' && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Gestor')))
                    ->action(function ($record) {
                        $record->update(['estado' => 'Aceptada']);
                        
                        // Enviar notificación al empleado
                        if ($record->empleado && $record->empleado->email) {
                            $user = \App\Models\User::where('email', $record->empleado->email)->first();
                            if ($user) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Solicitud Aceptada')
                                    ->body("Tu solicitud de {$record->tipo} ha sido Aceptada.")
                                    ->success()
                                    ->sendToDatabase($user);
                            }
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Solicitud Aprobada')
                            ->success()
                            ->send();
                    }),

                \Filament\Actions\Action::make('denegar')
                    ->label('Denegar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->estado === 'Pendiente' && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Gestor')))
                    ->action(function ($record) {
                        $record->update(['estado' => 'Rechazada']);
                        
                        // Enviar notificación al empleado
                        if ($record->empleado && $record->empleado->email) {
                            $user = \App\Models\User::where('email', $record->empleado->email)->first();
                            if ($user) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Solicitud Rechazada')
                                    ->body("Tu solicitud de {$record->tipo} ha sido Rechazada.")
                                    ->danger()
                                    ->sendToDatabase($user);
                            }
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Solicitud Rechazada')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
