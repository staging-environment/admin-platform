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
            ->columns(1)
            ->schema([
                \Filament\Schemas\Components\Section::make('Ficha del Empleado')
                    ->columnSpanFull()
                    ->schema([
                        // Datos Personales
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

                        \Filament\Schemas\Components\Html::make('<hr class="border-gray-200 dark:border-white/10 my-4" />')
                            ->columnSpan('full'),

                        // Contacto y Dirección
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

                        \Filament\Schemas\Components\Html::make('<hr class="border-gray-200 dark:border-white/10 my-4" />')
                            ->columnSpan('full'),

                        // Discapacidad / Incapacidad
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
                    ])
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
