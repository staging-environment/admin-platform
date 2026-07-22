<?php

namespace App\Filament\Resources\Empleados\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class EmpleadoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // BLOQUE 1: Datos Personales del Trabajador
                Section::make('Datos Personales del Trabajador')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                FileUpload::make('foto')
                                    ->label('Foto de perfil')
                                    ->image()
                                    ->directory('empleados/fotos')
                                    ->columnSpan(1),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('nombre')
                                            ->label('Nombre')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('apellidos')
                                            ->label('Apellidos')
                                            ->required()
                                            ->maxLength(255),
                                         Grid::make([
                                             'default' => 1,
                                             'md' => 3,
                                         ])
                                             ->schema([
                                                 TextInput::make('dni')
                                                     ->label('DNI / NIE')
                                                     ->required()
                                                     ->unique(ignoreRecord: true)
                                                     ->maxLength(255),
                                                 DatePicker::make('fecha_caducidad_dni')
                                                     ->label('Fecha de Caducidad DNI')
                                                     ->visible(false),
                                                 DatePicker::make('fecha_nacimiento')
                                                     ->label('Fecha de Nacimiento')
                                                     ->required(),
                                                 Select::make('sexo')
                                                     ->label('Sexo')
                                                     ->options([
                                                         'Masculino' => 'Masculino',
                                                         'Femenino' => 'Femenino',
                                                         'No binario' => 'No binario',
                                                         'Género fluido' => 'Género fluido',
                                                         'Queer' => 'Queer',
                                                     ])
                                                     ->required()
                                                     ->live(),
                                             ])
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpan(2),
                            ]),

                        \Filament\Schemas\Components\Html::make('<hr class="border-gray-200 dark:border-white/10 my-4" />')
                            ->columnSpan('full'),

                        // Contacto y Dirección
                        Grid::make(3)
                            ->schema([
                                TextInput::make('direccion')
                                    ->label('Dirección')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('localidad')
                                    ->label('Localidad')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('codigo_postal')
                                    ->label('Código Postal')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('provincia')
                                    ->label('Provincia')
                                    ->required()
                                    ->maxLength(255),
                                 TextInput::make('telefono_principal')
                                     ->label('Teléfono Principal')
                                     ->required()
                                     ->maxLength(255),
                                 TextInput::make('telefono_secundario')
                                     ->label('Teléfono Secundario')
                                     ->nullable()
                                     ->maxLength(255),
                                 TextInput::make('email')
                                     ->label('Correo Electrónico')
                                     ->email()
                                     ->required()
                                     ->unique(ignoreRecord: true)
                                     ->maxLength(255),
                                 TextInput::make('password')
                                     ->label('Contraseña de acceso')
                                     ->password()
                                     ->autocomplete('new-password')
                                     ->default(fn (string $context) => $context === 'create' ? '1234' : null)
                                     ->required(fn (string $context) => $context === 'create')
                                     ->helperText(fn (string $context) => $context === 'edit' ? 'Dejar en blanco para mantener la contraseña actual.' : null)
                                     ->dehydrated(fn ($state) => filled($state))
                                     ->maxLength(255),
                            ]),
                    ]),

                Section::make('Discapacidad / Incapacidad')
                    ->columnSpanFull()
                    ->visible(false)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('tiene_discapacidad')
                                    ->label('¿Tiene discapacidad?')
                                    ->live()
                                    ->columnSpanFull(),
                                
                                Select::make('tipo_discapacidad')
                                    ->label('Tipo de Discapacidad')
                                    ->multiple()
                                    ->options([
                                        'Física' => 'Física',
                                        'Psíquica' => 'Psíquica',
                                        'Sensorial' => 'Sensorial',
                                    ])
                                    ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                    ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),
                                
                                TextInput::make('porcentaje_discapacidad')
                                    ->label('Porcentaje de Discapacidad')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                    ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),

                                DatePicker::make('fecha_reconocimiento')
                                    ->label('Fecha de reconocimiento')
                                    ->beforeOrEqual('fecha_resolucion_discapacidad')
                                    ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                    ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),

                                DatePicker::make('fecha_resolucion_discapacidad')
                                    ->label('Fecha de resolución')
                                    ->afterOrEqual('fecha_reconocimiento')
                                    ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                    ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),

                                Toggle::make('pertenece_andalucia')
                                    ->label('¿Pertenece a Andalucía?')
                                    ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                    ->default(true)
                                    ->live(),

                                Select::make('comunidad_autonoma')
                                    ->label('Comunidad Autónoma')
                                    ->options([
                                        'Aragón' => 'Aragón',
                                        'Principado de Asturias' => 'Principado de Asturias',
                                        'Illes Balears' => 'Illes Balears',
                                        'Canarias' => 'Canarias',
                                        'Cantabria' => 'Cantabria',
                                        'Castilla y León' => 'Castilla y León',
                                        'Castilla-La Mancha' => 'Castilla-La Mancha',
                                        'Cataluña' => 'Cataluña',
                                        'Comunitat Valenciana' => 'Comunitat Valenciana',
                                        'Extremadura' => 'Extremadura',
                                        'Galicia' => 'Galicia',
                                        'Comunidad de Madrid' => 'Comunidad de Madrid',
                                        'Región de Murcia' => 'Región de Murcia',
                                        'Comunidad Foral de Navarra' => 'Comunidad Foral de Navarra',
                                        'País Vasco' => 'País Vasco',
                                        'La Rioja' => 'La Rioja',
                                        'Ceuta' => 'Ceuta',
                                        'Melilla' => 'Melilla',
                                    ])
                                    ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad') && ! $get('pertenece_andalucia'))
                                    ->required(fn (Get $get) => (bool) $get('tiene_discapacidad') && ! $get('pertenece_andalucia')),

                                Grid::make(3)
                                    ->schema([
                                        FileUpload::make('resolucion_discapacidad')
                                            ->label('Resolución de Discapacidad (Archivo)')
                                            ->directory('empleados/resoluciones')
                                            ->disk('local')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->previewable(false)
                                            ->required(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                            ->hintAction(
                                                \Filament\Actions\Action::make('ver_resolucion')
                                                    ->label('Ver Resolución')
                                                    ->icon('heroicon-o-eye')
                                                    ->color('warning')
                                                    ->visible(fn ($record) => $record && $record->documentos()->where('tipo', 'Resolución Discapacidad')->exists())
                                                    ->modalSubmitAction(false)
                                                    ->modalCancelActionLabel('Cerrar')
                                                    ->modalWidth('7xl')
                                                    ->modalContent(function ($record) {
                                                        $doc = $record->documentos()->where('tipo', 'Resolución Discapacidad')->first();
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
                                                                <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) . "' class='underline text-amber-600 font-bold' target='_blank'>Descargar Archivo</a>
                                                            </div>
                                                        ");
                                                    })
                                            )
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $doc = $record->documentos()->where('tipo', 'Resolución Discapacidad')->first();
                                                    $component->state($doc ? $doc->file_path : null);
                                                }
                                            })
                                            ->dehydrated(false)
                                            ->saveRelationshipsUsing(function ($component, $record, $state) {
                                                if (empty($state)) {
                                                    $record->documentos()->where('tipo', 'Resolución Discapacidad')->delete();
                                                    return;
                                                }

                                                $record->documentos()->updateOrCreate(
                                                    ['tipo' => 'Resolución Discapacidad'],
                                                    [
                                                        'nombre' => 'Resolución de Discapacidad ' . $record->nombre . ' ' . $record->apellidos,
                                                        'file_path' => $state,
                                                    ]
                                                );
                                            }),
                                        FileUpload::make('dictamen_tecnico')
                                            ->label('Dictamen técnico facultativo')
                                            ->directory('empleados/resoluciones')
                                            ->disk('local')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->previewable(false)
                                            ->required(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                            ->hintAction(
                                                \Filament\Actions\Action::make('ver_dictamen')
                                                    ->label('Ver Dictamen')
                                                    ->icon('heroicon-o-eye')
                                                    ->color('warning')
                                                    ->visible(fn ($record) => $record && $record->documentos()->where('tipo', 'Dictamen Técnico')->exists())
                                                    ->modalSubmitAction(false)
                                                    ->modalCancelActionLabel('Cerrar')
                                                    ->modalWidth('7xl')
                                                    ->modalContent(function ($record) {
                                                        $doc = $record->documentos()->where('tipo', 'Dictamen Técnico')->first();
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
                                                                <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) . "' class='underline text-amber-600 font-bold' target='_blank'>Descargar Archivo</a>
                                                            </div>
                                                        ");
                                                    })
                                            )
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $doc = $record->documentos()->where('tipo', 'Dictamen Técnico')->first();
                                                    $component->state($doc ? $doc->file_path : null);
                                                }
                                            })
                                            ->dehydrated(false)
                                            ->saveRelationshipsUsing(function ($component, $record, $state) {
                                                if (empty($state)) {
                                                    $record->documentos()->where('tipo', 'Dictamen Técnico')->delete();
                                                    return;
                                                }

                                                $record->documentos()->updateOrCreate(
                                                    ['tipo' => 'Dictamen Técnico'],
                                                    [
                                                        'nombre' => 'Dictamen Técnico Facultativo ' . $record->nombre . ' ' . $record->apellidos,
                                                        'file_path' => $state,
                                                    ]
                                                );
                                            }),
                                        FileUpload::make('certificado_discapacidad')
                                            ->label('Certificado de discapacidad')
                                            ->directory('empleados/resoluciones')
                                            ->disk('local')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->previewable(false)
                                            ->required(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                            ->hintAction(
                                                \Filament\Actions\Action::make('ver_certificado')
                                                    ->label('Ver Certificado')
                                                    ->icon('heroicon-o-eye')
                                                    ->color('warning')
                                                    ->visible(fn ($record) => $record && $record->documentos()->where('tipo', 'Certificado Discapacidad')->exists())
                                                    ->modalSubmitAction(false)
                                                    ->modalCancelActionLabel('Cerrar')
                                                    ->modalWidth('7xl')
                                                    ->modalContent(function ($record) {
                                                        $doc = $record->documentos()->where('tipo', 'Certificado Discapacidad')->first();
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
                                                                <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) . "' class='underline text-amber-600 font-bold' target='_blank'>Descargar Archivo</a>
                                                            </div>
                                                        ");
                                                    })
                                            )
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $doc = $record->documentos()->where('tipo', 'Certificado Discapacidad')->first();
                                                    $component->state($doc ? $doc->file_path : null);
                                                }
                                            })
                                            ->dehydrated(false)
                                            ->saveRelationshipsUsing(function ($component, $record, $state) {
                                                if (empty($state)) {
                                                    $record->documentos()->where('tipo', 'Certificado Discapacidad')->delete();
                                                    return;
                                                }

                                                $record->documentos()->updateOrCreate(
                                                    ['tipo' => 'Certificado Discapacidad'],
                                                    [
                                                        'nombre' => 'Certificado de Discapacidad ' . $record->nombre . ' ' . $record->apellidos,
                                                        'file_path' => $state,
                                                    ]
                                                );
                                            }),
                                    ])
                                    ->visible(fn (Get $get, $record) => (bool) ($get('tiene_discapacidad') ?? ($record?->tiene_discapacidad ?? false)))
                                    ->columnSpanFull(),

                                Toggle::make('tiene_incapacidad')
                                    ->label('¿Tiene incapacidad?')
                                    ->live()
                                    ->columnSpanFull(),

                                Select::make('tipo_incapacidad')
                                    ->label('Tipo de Incapacidad')
                                    ->multiple()
                                    ->options([
                                        'Físico' => 'Físico',
                                        'Psíquico' => 'Psíquico',
                                    ])
                                    ->visible(fn (Get $get) => (bool) $get('tiene_incapacidad'))
                                    ->required(fn (Get $get) => (bool) $get('tiene_incapacidad'))
                                    ->columnSpanFull(),

                                FileUpload::make('incapacidad_file')
                                    ->label('Adjuntar Documentación de Incapacidad')
                                    ->directory('empleados/documentos')
                                    ->disk('local')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->previewable(false)
                                    ->required(fn (Get $get) => (bool) $get('tiene_incapacidad'))
                                    ->hintAction(
                                        \Filament\Actions\Action::make('ver_incapacidad')
                                            ->label('Ver Incapacidad')
                                            ->icon('heroicon-o-eye')
                                            ->color('warning')
                                            ->visible(fn ($record) => $record && $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->exists())
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('Cerrar')
                                            ->modalWidth('7xl')
                                            ->modalContent(function ($record) {
                                                $doc = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->first();
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
                                                        <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) . "' class='underline text-amber-600 font-bold' target='_blank'>Descargar Archivo</a>
                                                    </div>
                                                ");
                                            })
                                    )
                                    ->afterStateHydrated(function ($component, $record) {
                                        if ($record) {
                                            $doc = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->first();
                                            $component->state($doc ? $doc->file_path : null);
                                        }
                                    })
                                    ->dehydrated(false)
                                    ->saveRelationshipsUsing(function ($component, $record, $state, Get $get) {
                                        if (empty($state)) {
                                            $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->delete();
                                            return;
                                        }

                                        $tipo = 'Incapacidad Física';
                                        $tipoIncapacidad = $get('tipo_incapacidad') ?? [];
                                        if (is_array($tipoIncapacidad) && count($tipoIncapacidad) > 0) {
                                            $first = $tipoIncapacidad[0];
                                            $tipo = $first === 'Psíquico' ? 'Incapacidad Psíquica' : 'Incapacidad Física';
                                        }

                                        $filePath = is_array($state) ? reset($state) : $state;

                                        $record->documentos()->updateOrCreate(
                                            ['tipo' => $tipo],
                                            [
                                                'nombre' => 'Documentación Incapacidad ' . $record->nombre . ' ' . $record->apellidos,
                                                'file_path' => $filePath,
                                            ]
                                        );
                                    })
                                    ->visible(fn (Get $get, $record) => (bool) ($get('tiene_incapacidad') ?? ($record?->tiene_incapacidad ?? false)))
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Formación y Títulos')
                    ->columnSpanFull()
                    ->visible(false)
                    ->schema([
                        FileUpload::make('formacion_files')
                            ->label('Adjuntar Cursos / Formación (Múltiple)')
                            ->multiple()
                            ->directory('empleados/documentos')
                            ->disk('local')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->previewable(false)
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $docs = $record->documentos()->whereIn('tipo', ['Certificados', 'Titulaciones', 'Carnets', 'Otros'])->pluck('file_path')->toArray();
                                    $component->state($docs);
                                }
                            })
                            ->dehydrated(false)
                            ->saveRelationshipsUsing(function ($component, $record, $state) {
                                $state = array_filter((array) $state);
                                $record->documentos()->whereIn('tipo', ['Certificados', 'Titulaciones', 'Carnets', 'Otros'])->whereNotIn('file_path', $state)->delete();
                                foreach ($state as $file_path) {
                                    $record->documentos()->firstOrCreate(
                                        ['file_path' => $file_path],
                                        [
                                            'tipo' => 'Certificados',
                                            'nombre' => basename($file_path),
                                        ]
                                    );
                                }
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
