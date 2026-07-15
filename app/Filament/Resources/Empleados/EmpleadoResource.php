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
                        \Filament\Schemas\Components\Grid::make(4)
                            ->schema([
                                \Filament\Infolists\Components\ImageEntry::make('foto')
                                    ->label('Foto de perfil')
                                    ->circular()
                                    ->columnSpan(1),
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('nombre')
                                            ->label('Nombre')
                                            ->weight(\Filament\Support\Enums\FontWeight::Bold),
                                        \Filament\Infolists\Components\TextEntry::make('apellidos')
                                            ->label('Apellidos')
                                            ->weight(\Filament\Support\Enums\FontWeight::Bold),
                                        \Filament\Infolists\Components\TextEntry::make('dni')
                                            ->label('DNI / NIE')
                                            ->hint(function ($record) {
                                                if (!$record) return null;
                                                return $record->documentos()->where('tipo', 'DNI')->exists() ? 'Ver DNI' : null;
                                            })
                                            ->hintUrl(function ($record) {
                                                if (!$record) return null;
                                                $doc = $record->documentos()->where('tipo', 'DNI')->first();
                                                return $doc ? route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]) : null;
                                            }, shouldOpenInNewTab: true)
                                            ->hintColor('warning'),
                                        \Filament\Infolists\Components\TextEntry::make('fecha_nacimiento')
                                            ->label('Fecha de Nacimiento')
                                            ->date(),
                                    ])
                                    ->columnSpan(3),
                            ]),

                        \Filament\Schemas\Components\Html::make('<hr class="border-gray-200 dark:border-white/10 my-4" />')
                            ->columnSpan('full'),

                        // Contacto y Dirección
                        \Filament\Schemas\Components\Grid::make(4)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('gasolinera.Nombre')
                                    ->label('Ubicación de trabajo')
                                    ->placeholder('No asignada'),
                                \Filament\Infolists\Components\TextEntry::make('puesto')
                                    ->label('Puesto')
                                    ->placeholder('No asignado'),
                                \Filament\Infolists\Components\TextEntry::make('telefono_principal')
                                    ->label('Teléfono Principal'),
                                \Filament\Infolists\Components\TextEntry::make('telefono_secundario')
                                    ->label('Teléfono Secundario')
                                    ->placeholder('No especificado'),
                                \Filament\Infolists\Components\TextEntry::make('direccion')
                                    ->label('Dirección'),
                                \Filament\Infolists\Components\TextEntry::make('localidad')
                                    ->label('Localidad'),
                                \Filament\Infolists\Components\TextEntry::make('codigo_postal')
                                    ->label('Código Postal'),
                                \Filament\Infolists\Components\TextEntry::make('provincia')
                                    ->label('Provincia'),
                                \Filament\Infolists\Components\TextEntry::make('email')
                                    ->label('Correo Electrónico')
                                    ->columnSpan(4),
                                \Filament\Infolists\Components\TextEntry::make('tipo_contrato')
                                    ->label('Tipo de Contrato')
                                    ->hint(function ($record) {
                                        if (!$record) return null;
                                        return $record->documentos()->where('tipo', 'Contratos')->exists() ? 'Ver Contrato' : null;
                                    })
                                    ->hintUrl(function ($record) {
                                        if (!$record) return null;
                                        $doc = $record->documentos()->where('tipo', 'Contratos')->first();
                                        return $doc ? route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]) : null;
                                    }, shouldOpenInNewTab: true)
                                    ->hintColor('warning')
                                    ->columnSpan(2),
                                \Filament\Infolists\Components\TextEntry::make('fecha_vencimiento_contrato')
                                    ->label('Vencimiento de Contrato')
                                    ->date()
                                    ->visible(fn ($record) => $record && $record->tipo_contrato === 'Eventual')
                                    ->placeholder('N/A')
                                    ->columnSpan(2),
                            ]),

                        \Filament\Schemas\Components\Html::make('<hr class="border-gray-200 dark:border-white/10 my-4" />')
                            ->columnSpan('full'),

                        // Discapacidad / Incapacidad
                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('sin_discapacidad_ni_incapacidad')
                                    ->label('Discapacidad / Incapacidad')
                                    ->state('No tiene Discapacidad ni Incapacidad')
                                    ->visible(fn ($record) => $record && !$record->tiene_discapacidad && !$record->tiene_incapacidad)
                                    ->columnSpanFull(),

                                \Filament\Infolists\Components\TextEntry::make('tipo_discapacidad')
                                    ->label('Tipo de Discapacidad')
                                    ->visible(fn ($record) => $record && $record->tiene_discapacidad)
                                    ->placeholder('Ninguna'),
                                \Filament\Infolists\Components\TextEntry::make('porcentaje_discapacidad')
                                    ->label('Porcentaje de Discapacidad')
                                    ->suffix('%')
                                    ->visible(fn ($record) => $record && $record->tiene_discapacidad)
                                    ->placeholder('N/A'),
                                \Filament\Infolists\Components\TextEntry::make('fecha_reconocimiento')
                                    ->label('Fecha de reconocimiento')
                                    ->date('d/m/Y')
                                    ->visible(fn ($record) => $record && $record->tiene_discapacidad)
                                    ->placeholder('Ninguna'),
                                \Filament\Infolists\Components\TextEntry::make('pertenece_andalucia')
                                    ->label('¿Pertenece a Andalucía?')
                                    ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No')
                                    ->visible(fn ($record) => $record && $record->tiene_discapacidad),
                                \Filament\Infolists\Components\TextEntry::make('comunidad_autonoma')
                                    ->label('Comunidad Autónoma')
                                    ->visible(fn ($record) => $record && $record->tiene_discapacidad && !$record->pertenece_andalucia)
                                    ->placeholder('N/A'),
                                \Filament\Infolists\Components\TextEntry::make('tipo_incapacidad')
                                    ->label('Tipo de Incapacidad')
                                    ->visible(fn ($record) => $record && $record->tiene_incapacidad)
                                    ->placeholder('Ninguna'),
                                \Filament\Infolists\Components\TextEntry::make('incapacidad_file')
                                    ->label('Documentación de Incapacidad')
                                    ->visible(fn ($record) => $record && $record->tiene_incapacidad)
                                    ->state(function ($record) {
                                        $doc = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->first();
                                        return $doc ? basename($doc->file_path) : null;
                                    })
                                    ->url(function ($record) {
                                        $doc = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->first();
                                        return $doc ? route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]) : null;
                                    })
                                    ->placeholder('Sin documento adjunto'),
                                \Filament\Infolists\Components\TextEntry::make('resolucion_discapacidad')
                                    ->label('Resolución de Discapacidad')
                                    ->visible(fn ($record) => $record && $record->tiene_discapacidad)
                                    ->state(function ($record) {
                                        $doc = $record->documentos()->where('tipo', 'Resolución Discapacidad')->first();
                                        return $doc ? basename($doc->file_path) : null;
                                    })
                                    ->url(function ($record) {
                                        $doc = $record->documentos()->where('tipo', 'Resolución Discapacidad')->first();
                                        return $doc ? route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]) : null;
                                    })
                                    ->placeholder('Sin documento adjunto'),
                                \Filament\Infolists\Components\TextEntry::make('dictamen_tecnico')
                                    ->label('Dictamen técnico facultativo')
                                    ->visible(fn ($record) => $record && $record->tiene_discapacidad)
                                    ->state(function ($record) {
                                        $doc = $record->documentos()->where('tipo', 'Dictamen Técnico')->first();
                                        return $doc ? basename($doc->file_path) : null;
                                    })
                                    ->url(function ($record) {
                                        $doc = $record->documentos()->where('tipo', 'Dictamen Técnico')->first();
                                        return $doc ? route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]) : null;
                                    })
                                    ->placeholder('Sin documento adjunto'),
                                \Filament\Infolists\Components\TextEntry::make('certificado_discapacidad')
                                    ->label('Certificado de discapacidad')
                                    ->visible(fn ($record) => $record && $record->tiene_discapacidad)
                                    ->state(function ($record) {
                                        $doc = $record->documentos()->where('tipo', 'Certificado Discapacidad')->first();
                                        return $doc ? basename($doc->file_path) : null;
                                    })
                                    ->url(function ($record) {
                                        $doc = $record->documentos()->where('tipo', 'Certificado Discapacidad')->first();
                                        return $doc ? route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]) : null;
                                    })
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
