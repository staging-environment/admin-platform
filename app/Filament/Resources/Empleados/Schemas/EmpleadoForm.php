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
                                                     ->maxLength(255)
                                                     ->dehydrateStateUsing(fn ($state) => strtoupper(str_replace([' ', '-'], '', $state)))
                                                     ->rules([
                                                         function () {
                                                             return function (string $attribute, $value, \Closure $fail) {
                                                                 $clean = strtoupper(str_replace([' ', '-'], '', $value));
                                                                 
                                                                 if (empty($clean)) {
                                                                     return;
                                                                 }

                                                                 if (preg_match('/^[0-9]{8}[A-Z]$/', $clean)) {
                                                                     $number = substr($clean, 0, 8);
                                                                     $letter = substr($clean, -1);
                                                                 } elseif (preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $clean)) {
                                                                     $letter = substr($clean, -1);
                                                                     $start = substr($clean, 0, 1);
                                                                     $mid = substr($clean, 1, 7);
                                                                     
                                                                     $mapping = ['X' => '0', 'Y' => '1', 'Z' => '2'];
                                                                     $number = $mapping[$start] . $mid;
                                                                 } else {
                                                                     $fail('El formato de DNI o NIE introducido no es válido.');
                                                                     return;
                                                                 }

                                                                 $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
                                                                 $calculatedLetter = $letters[intval($number) % 23];

                                                                 if ($letter !== $calculatedLetter) {
                                                                     $fail('El número de DNI o NIE no coincide con la letra de control correspondiente.');
                                                                 }
                                                             };
                                                         }
                                                     ]),
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
                                 TextInput::make('iban')
                                     ->label('Código IBAN')
                                     ->required()
                                     ->placeholder('ES00 0000 0000 0000 0000 0000')
                                     ->dehydrateStateUsing(fn ($state) => strtoupper(str_replace(' ', '', $state)))
                                     ->rules([
                                         function () {
                                             return function (string $attribute, $value, \Closure $fail) {
                                                 $clean = strtoupper(str_replace(' ', '', $value));
                                                 
                                                 if (empty($clean)) {
                                                     return;
                                                 }

                                                 // 1. Length check: between 15 and 34 characters
                                                 $len = strlen($clean);
                                                 if ($len < 15 || $len > 34) {
                                                     $fail('El IBAN debe tener entre 15 y 34 caracteres.');
                                                     return;
                                                 }

                                                 // 2. Validate format: starts with two uppercase letters followed by digits and letters
                                                 if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $clean)) {
                                                     $fail('El formato del IBAN no es válido.');
                                                     return;
                                                 }

                                                 // 3. Mod-97 validation
                                                 $moved = substr($clean, 4) . substr($clean, 0, 4);
                                                 $numeric = '';
                                                 foreach (str_split($moved) as $char) {
                                                     if (ctype_alpha($char)) {
                                                         $numeric .= ord($char) - ord('A') + 10;
                                                     } else {
                                                         $numeric .= $char;
                                                     }
                                                 }

                                                 // Compute modulo 97 of a very large number string
                                                 $remainder = 0;
                                                 foreach (str_split($numeric, 7) as $chunk) {
                                                     $remainder = intval($remainder . $chunk) % 97;
                                                 }

                                                 if ($remainder !== 1) {
                                                     $fail('El código IBAN introducido no es válido (dígito de control incorrecto).');
                                                 }
                                             };
                                         }
                                     ]),
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
                                    ->minValue(fn (Get $get) => (bool) $get('tiene_discapacidad') ? 33 : 0)
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
                                            ->markAsRequired()
                                            ->required(fn (Get $get) => (bool) $get('tiene_discapacidad') && empty($get('resolucion_discapacidad')) && empty($get('dictamen_tecnico')) && empty($get('certificado_discapacidad')))
                                            ->validationMessages([
                                                'required' => 'Debe adjuntar al menos uno de los tres archivos de discapacidad.',
                                            ])
                                            ->directory('empleados/resoluciones')
                                            ->disk('local')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->previewable(false)
                                            ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
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
                                                    $record->actualizarAlertas();
                                                    return;
                                                }

                                                $record->documentos()->updateOrCreate(
                                                    ['tipo' => 'Resolución Discapacidad'],
                                                    [
                                                        'nombre' => 'Resolución de Discapacidad ' . $record->nombre . ' ' . $record->apellidos,
                                                        'file_path' => $state,
                                                    ]
                                                );
                                                $record->actualizarAlertas();
                                            }),
                                        FileUpload::make('dictamen_tecnico')
                                            ->label('Dictamen técnico facultativo')
                                            ->markAsRequired()
                                            ->required(fn (Get $get) => (bool) $get('tiene_discapacidad') && empty($get('resolucion_discapacidad')) && empty($get('dictamen_tecnico')) && empty($get('certificado_discapacidad')))
                                            ->validationMessages([
                                                'required' => 'Debe adjuntar al menos uno de los tres archivos de discapacidad.',
                                            ])
                                            ->directory('empleados/resoluciones')
                                            ->disk('local')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->previewable(false)
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
                                                    $record->actualizarAlertas();
                                                    return;
                                                }

                                                $record->documentos()->updateOrCreate(
                                                    ['tipo' => 'Dictamen Técnico'],
                                                    [
                                                        'nombre' => 'Dictamen Técnico Facultativo ' . $record->nombre . ' ' . $record->apellidos,
                                                        'file_path' => $state,
                                                    ]
                                                );
                                                $record->actualizarAlertas();
                                            }),
                                        FileUpload::make('certificado_discapacidad')
                                            ->label('Certificado de discapacidad')
                                            ->markAsRequired()
                                            ->required(fn (Get $get) => (bool) $get('tiene_discapacidad') && empty($get('resolucion_discapacidad')) && empty($get('dictamen_tecnico')) && empty($get('certificado_discapacidad')))
                                            ->validationMessages([
                                                'required' => 'Debe adjuntar al menos uno de los tres archivos de discapacidad.',
                                            ])
                                            ->directory('empleados/resoluciones')
                                            ->disk('local')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->previewable(false)
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
                                                    $record->actualizarAlertas();
                                                    return;
                                                }

                                                $record->documentos()->updateOrCreate(
                                                    ['tipo' => 'Certificado Discapacidad'],
                                                    [
                                                        'nombre' => 'Certificado de Discapacidad ' . $record->nombre . ' ' . $record->apellidos,
                                                        'file_path' => $state,
                                                    ]
                                                );
                                                $record->actualizarAlertas();
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

                                Repeater::make('incapacidad_archivos')
                                    ->label(new \Illuminate\Support\HtmlString('Adjuntar Documentación de Incapacidad (Múltiples archivos) <span class="text-red-600 font-bold">*</span>'))
                                    ->schema([
                                        FileUpload::make('file_path')
                                            ->label('Archivo')
                                            ->directory('empleados/documentos')
                                            ->disk('local')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->previewable(false)
                                            ->required(),
                                        TextInput::make('comentario')
                                            ->label('Comentario / Descripción')
                                            ->placeholder('Ej: Informe de resolución médica 2026')
                                            ->nullable(),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Añadir otro archivo')
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
                                                $docs = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->get();
                                                if ($docs->isEmpty()) return new \Illuminate\Support\HtmlString("<p class='text-gray-500 p-4 text-center'>No hay documentación de incapacidad adjunta.</p>");

                                                $html = "<div class='space-y-6 p-2'>";
                                                foreach ($docs as $index => $doc) {
                                                    $url = route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]);
                                                    $extension = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));

                                                    $html .= "<div class='border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 shadow-sm space-y-3'>";
                                                    $html .= "<div class='flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-gray-100 dark:border-gray-700'>";
                                                    $html .= "<span class='font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2'><svg class='w-4 h-4 text-amber-500' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'/></svg> Archivo #" . ($index + 1) . "</span>";
                                                    if ($doc->comentario) {
                                                        $html .= "<div class='text-xs font-semibold text-gray-700 dark:text-gray-300 bg-amber-50 dark:bg-amber-950/30 px-3 py-1 rounded-lg border border-amber-200 dark:border-amber-800'>💬 <strong>Comentario:</strong> " . e($doc->comentario) . "</div>";
                                                    }
                                                    $html .= "</div>";

                                                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                                                        $html .= "<div class='flex justify-center bg-gray-50 dark:bg-gray-900 border rounded-lg p-2 overflow-auto' style='max-height: 50vh;'><img src='{$url}' class='object-contain rounded' style='max-height: 45vh;' /></div>";
                                                    } elseif ($extension === 'pdf') {
                                                        $html .= "<iframe src='{$url}' class='w-full border rounded-lg' style='height: 450px;'></iframe>";
                                                    } else {
                                                        $html .= "<div class='p-4 text-center'><a href='{$url}' target='_blank' class='text-indigo-600 font-bold underline'>Descargar Archivo (" . strtoupper($extension) . ")</a></div>";
                                                    }
                                                    $html .= "</div>";
                                                }
                                                $html .= "</div>";

                                                return new \Illuminate\Support\HtmlString($html);
                                            })
                                    )
                                    ->afterStateHydrated(function ($component, $record) {
                                        if ($record) {
                                            $docs = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->get();
                                            $state = [];
                                            foreach ($docs as $d) {
                                                $state[] = [
                                                    'file_path' => $d->file_path,
                                                    'comentario' => $d->comentario,
                                                ];
                                            }
                                            $component->state($state);
                                        }
                                    })
                                    ->dehydrated(false)
                                    ->saveRelationshipsUsing(function ($component, $record, $state, Get $get) {
                                        $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->delete();
                                        if (empty($state) || !is_array($state)) {
                                            return;
                                        }

                                        foreach ($state as $item) {
                                            if (!empty($item['file_path'])) {
                                                $tipo = 'Incapacidad Física';
                                                $tipoIncapacidad = $get('tipo_incapacidad') ?? [];
                                                if (is_array($tipoIncapacidad) && count($tipoIncapacidad) > 0) {
                                                    $first = $tipoIncapacidad[0];
                                                    $tipo = $first === 'Psíquico' ? 'Incapacidad Psíquica' : 'Incapacidad Física';
                                                }

                                                $record->documentos()->create([
                                                    'tipo' => $tipo,
                                                    'nombre' => 'Documentación Incapacidad ' . $record->nombre . ' ' . $record->apellidos,
                                                    'file_path' => $item['file_path'],
                                                    'comentario' => $item['comentario'] ?? null,
                                                ]);
                                            }
                                        }
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
