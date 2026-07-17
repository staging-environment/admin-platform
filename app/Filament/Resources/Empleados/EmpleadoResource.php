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
                // BLOQUE 1: Datos Personales del Trabajador
                \Filament\Schemas\Components\Section::make('Datos Personales del Trabajador')
                    ->columnSpanFull()
                    ->schema([
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
                                            ->extraAttributes(['style' => 'width: fit-content;'])
                                            ->suffixAction(
                                                \Filament\Actions\Action::make('ver_dni')
                                                    ->icon('heroicon-m-eye')
                                                    ->color('warning')
                                                    ->iconButton()
                                                    ->visible(fn ($record) => $record && $record->documentos()->where('tipo', 'DNI')->exists())
                                                    ->modalSubmitAction(false)
                                                    ->modalCancelActionLabel('Cerrar')
                                                    ->modalWidth('7xl')
                                                    ->modalContent(function ($record) {
                                                        $doc = $record->documentos()->where('tipo', 'DNI')->first();
                                                        if (!$doc) return null;
                                                        $url = route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]);
                                                        $extension = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                                                            return new \Illuminate\Support\HtmlString("
                                                                <div class='flex justify-center p-2 bg-gray-50 border rounded-lg overflow-auto' style='max-height: 75vh; min-height: 450px;'>
                                                                    <img src='{$url}' class='object-contain rounded shadow-sm' style='max-height: 70vh;' />
                                                                </div>
                                                            ");
                                                        } elseif ($extension === 'pdf') {
                                                            return new \Illuminate\Support\HtmlString("
                                                                <div class='w-full border rounded-lg overflow-hidden' style='height: 75vh; min-height: 600px;'>
                                                                    <iframe src='{$url}' class='w-full h-full border-none'></iframe>
                                                                </div>
                                                            ");
                                                        }
                                                        return new \Illuminate\Support\HtmlString("
                                                            <div class='text-center p-4'>
                                                                <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) . "' class='underline text-amber-600 font-bold' target='_blank'>Descargar DNI</a>
                                                            </div>
                                                        ");
                                                    })
                                            ),
                                        \Filament\Infolists\Components\TextEntry::make('fecha_nacimiento')
                                            ->label('Fecha de Nacimiento')
                                            ->date('d/m/Y'),
                                        \Filament\Infolists\Components\TextEntry::make('fecha_caducidad_dni')
                                            ->label('Fecha de Caducidad DNI')
                                            ->date('d/m/Y')
                                            ->placeholder('No especificada'),
                                        \Filament\Infolists\Components\TextEntry::make('sexo')
                                            ->label('Sexo')
                                            ->placeholder('No especificado'),
                                    ])
                                    ->columnSpan(3),
                            ]),

                        \Filament\Schemas\Components\Html::make('<hr class="border-gray-200 dark:border-white/10 my-4" />')
                            ->columnSpan('full'),

                        // Contacto y Dirección
                        \Filament\Schemas\Components\Grid::make(4)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('telefono_principal')
                                    ->label('Teléfono'),
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
                                    ->columnSpan(2),
                            ]),
                    ]),

                // BLOQUE 2: Información Laboral y Cargo
                \Filament\Schemas\Components\Section::make('Información Laboral y Cargo')
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(4)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('gasolinera.Nombre')
                                    ->label('Ubicación de trabajo')
                                    ->placeholder('No asignada'),
                                \Filament\Infolists\Components\TextEntry::make('puesto')
                                    ->label('Puesto')
                                    ->placeholder('No asignado'),
                                 \Filament\Infolists\Components\TextEntry::make('tipo_contrato')
                                     ->label('Tipo de Contrato')
                                     ->state(fn ($record) => $record && $record->tipo_contrato === 'Indefinido' ? 'Fijo' : ($record ? $record->tipo_contrato : null))
                                     ->extraAttributes(['style' => 'width: fit-content;'])
                                     ->suffixAction(
                                        \Filament\Actions\Action::make('ver_contrato')
                                            ->icon('heroicon-m-eye')
                                            ->color('warning')
                                            ->iconButton()
                                            ->visible(fn ($record) => $record && $record->documentos()->where('tipo', 'Contratos')->exists())
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('Cerrar')
                                            ->modalWidth('7xl')
                                            ->modalContent(function ($record) {
                                                $doc = $record->documentos()->where('tipo', 'Contratos')->latest('id')->first();
                                                if (!$doc) return null;
                                                $url = route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]);
                                                $extension = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                                                    return new \Illuminate\Support\HtmlString("
                                                        <div class='flex justify-center p-2 bg-gray-50 border rounded-lg overflow-auto' style='max-height: 75vh; min-height: 450px;'>
                                                            <img src='{$url}' class='object-contain rounded shadow-sm' style='max-height: 70vh;' />
                                                        </div>
                                                    ");
                                                } elseif ($extension === 'pdf') {
                                                    return new \Illuminate\Support\HtmlString("
                                                        <div class='w-full border rounded-lg overflow-hidden' style='height: 75vh; min-height: 600px;'>
                                                            <iframe src='{$url}' class='w-full h-full border-none'></iframe>
                                                        </div>
                                                    ");
                                                }
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='text-center p-4'>
                                                        <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) . "' class='underline text-amber-600 font-bold' target='_blank'>Descargar Contrato</a>
                                                    </div>
                                                ");
                                            })
                                    )
                                    ->columnSpan(2),
                                \Filament\Infolists\Components\TextEntry::make('fecha_vencimiento_contrato')
                                    ->label('Vencimiento de Contrato')
                                    ->date('d/m/Y')
                                    ->visible(fn ($record) => $record && $record->tipo_contrato === 'Eventual')
                                    ->placeholder('N/A')
                                    ->columnSpan(2),
                            ]),
                    ]),

                \Filament\Schemas\Components\Section::make('Discapacidad / Incapacidad')
                    ->columnSpanFull()
                    ->visible(false)
                    ->schema([
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
                                    ->state(function ($record) {
                                        if ($record && !empty($record->tipo_discapacidad)) {
                                            return $record->tipo_discapacidad;
                                        }
                                        return $record ? $record->documentos()
                                            ->whereIn('tipo', ['Resolución Discapacidad', 'Dictamen Técnico', 'Certificado Discapacidad'])
                                            ->pluck('tipo')
                                            ->toArray() : [];
                                    })
                                    ->separator(', ')
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
                    ]),

                \Filament\Schemas\Components\Section::make('Formación y Títulos')
                    ->columnSpanFull()
                    ->visible(false)
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(1)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('formacion_list')
                                    ->label('Formación y Títulos')
                                    ->html()
                                    ->state(function ($record) {
                                        $docs = $record->documentos()->whereIn('tipo', ['Certificados', 'Titulaciones', 'Carnets', 'Otros'])->get();
                                        if ($docs->isEmpty()) {
                                            return "<span class='text-gray-500 italic'>No tiene formación registrada</span>";
                                        }

                                        $html = "<ul class='space-y-1.5'>";
                                        foreach ($docs as $doc) {
                                            $url = route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]);
                                            $html .= "<li>
                                                <a href='{$url}' target='_blank' class='inline-flex items-center gap-1.5 text-amber-600 hover:text-amber-700 hover:underline font-medium'>
                                                    <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z'/><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'/></svg>
                                                    {$doc->nombre} ({$doc->tipo})
                                                </a>
                                            </li>";
                                        }
                                        $html .= "</ul>";
                                        return $html;
                                    })
                                    ->columnSpanFull(),
                            ]),
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
