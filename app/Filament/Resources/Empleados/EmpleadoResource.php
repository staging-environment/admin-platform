<?php

namespace App\Filament\Resources\Empleados;

use App\Filament\Resources\Empleados\Pages\CreateEmpleado;
use App\Filament\Resources\Empleados\Pages\EditEmpleado;
use App\Filament\Resources\Empleados\Pages\ListEmpleados;
use App\Filament\Resources\Empleados\Pages\ViewEmpleado;
use App\Filament\Resources\Empleados\Schemas\EmpleadoForm;
use App\Filament\Resources\Empleados\Tables\EmpleadosTable;
use App\Models\Empleado;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmpleadoResource extends Resource
{
    protected static ?string $model = Empleado::class;

    protected static ?string $slug = 'recursos-humanos';

    protected static ?string $modelLabel = 'Empleado';
    protected static ?string $pluralModelLabel = 'Empleados';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->can('gestion_recursos_humanos');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasRole('Admin') || $user->can('gestion_alta_empleados');
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasRole('Admin') || $user->can('gestion_editar_empleados');
    }

    public static function getNavigationLabel(): string
    {
        return 'Recursos humanos';
    }

    public static function form(Schema $schema): Schema
    {
        return EmpleadoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Datos Personales')
                    ->description('Información básica de identificación')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                \Filament\Infolists\Components\ImageEntry::make('foto')
                                    ->label('Foto de perfil')
                                    ->circular()
                                    ->columnSpan(1),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('nombre')
                                            ->label('Nombre')
                                            ->weight(\Filament\Support\Enums\FontWeight::Bold),
                                        \Filament\Infolists\Components\TextEntry::make('apellidos')
                                            ->label('Apellidos')
                                            ->weight(\Filament\Support\Enums\FontWeight::Bold),
                                        \Filament\Infolists\Components\TextEntry::make('dni')
                                            ->label('DNI / NIE'),
                                        \Filament\Infolists\Components\TextEntry::make('fecha_nacimiento')
                                            ->label('Fecha de Nacimiento')
                                            ->date(),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ]),

                \Filament\Schemas\Components\Section::make('Contacto y Dirección')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('direccion')
                                    ->label('Dirección'),
                                \Filament\Infolists\Components\TextEntry::make('localidad')
                                    ->label('Localidad'),
                                \Filament\Infolists\Components\TextEntry::make('codigo_postal')
                                    ->label('Código Postal'),
                                \Filament\Infolists\Components\TextEntry::make('provincia')
                                    ->label('Provincia'),
                                \Filament\Infolists\Components\TextEntry::make('telefono_principal')
                                    ->label('Teléfono Principal'),
                                \Filament\Infolists\Components\TextEntry::make('telefono_secundario')
                                    ->label('Teléfono Secundario')
                                    ->placeholder('No especificado'),
                                \Filament\Infolists\Components\TextEntry::make('email')
                                    ->label('Correo Electrónico')
                                    ->columnSpan(3),
                            ]),
                    ]),

                \Filament\Schemas\Components\Section::make('Discapacidad / Incapacidad')
                    ->description('Información sobre discapacidad o incapacidades')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('tipo_discapacidad')
                                    ->label('Tipo de Discapacidad')
                                    ->placeholder('Ninguna'),
                                \Filament\Infolists\Components\TextEntry::make('porcentaje_discapacidad')
                                    ->label('Porcentaje de Discapacidad')
                                    ->suffix('%')
                                    ->placeholder('N/A'),
                                \Filament\Infolists\Components\TextEntry::make('incapacidad')
                                    ->label('Incapacidad')
                                    ->placeholder('Ninguna'),
                                \Filament\Infolists\Components\TextEntry::make('resolucion_discapacidad')
                                    ->label('Resolución de Discapacidad')
                                    ->url(fn ($record) => $record->resolucion_discapacidad ? \Illuminate\Support\Facades\Storage::disk('local')->url($record->resolucion_discapacidad) : null)
                                    ->placeholder('Sin documento adjunto'),
                            ]),
                    ]),

                \Filament\Schemas\Components\Section::make('Jornada y Horarios Laborales')
                    ->description('Configuración de horarios y jornada asignada')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('horarios')
                            ->label('')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('tipo_jornada')
                                    ->label('Tipo de Jornada')
                                    ->badge()
                                    ->color('info'),
                                \Filament\Infolists\Components\TextEntry::make('turnos')
                                    ->label('Turnos Asignados')
                                    ->placeholder('Sin turnos específicos'),
                                \Filament\Infolists\Components\TextEntry::make('dias_laborales')
                                    ->label('Días Laborales')
                                    ->badge()
                                    ->separator(','),
                                \Filament\Infolists\Components\TextEntry::make('hora_inicio')
                                    ->label('Hora de Inicio')
                                    ->time('H:i'),
                                \Filament\Infolists\Components\TextEntry::make('hora_fin')
                                    ->label('Hora de Fin')
                                    ->time('H:i'),
                                \Filament\Infolists\Components\TextEntry::make('horarios')
                                    ->label('Detalles del Horario')
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return EmpleadosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentosRelationManager::class,
            RelationManagers\CursosRelationManager::class,
            RelationManagers\NotificacionesRelationManager::class,
            RelationManagers\HorariosRelationManager::class,
            RelationManagers\AusenciasRelationManager::class,
            RelationManagers\VacacionesRelationManager::class,
            RelationManagers\ContratosRelationManager::class,
            RelationManagers\ComentariosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmpleados::route('/'),
            'create' => CreateEmpleado::route('/create'),
            'view' => ViewEmpleado::route('/{record}'),
            'edit' => EditEmpleado::route('/{record}/edit'),
        ];
    }
}
