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
                Section::make('Ficha del Empleado')
                    ->columnSpanFull()
                    ->schema([
                        // Datos Personales
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
                                        TextInput::make('dni')
                                            ->label('DNI / NIE')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        DatePicker::make('fecha_nacimiento')
                                            ->label('Fecha de Nacimiento')
                                            ->required(),
                                        Select::make('gasolinera_codigo')
                                            ->label('Ubicación de trabajo')
                                            ->options(function () {
                                                return \App\Models\Gasolinera::pluck('Nombre', 'Codigo')->toArray();
                                            })
                                            ->placeholder('Selecciona la ubicación de trabajo')
                                            ->required(),
                                        TextInput::make('puesto')
                                            ->label('Puesto')
                                            ->placeholder('Ej: Expendedor, Encargado...')
                                            ->maxLength(255),
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
                                    ->columnSpan(2),
                            ]),

                        \Filament\Schemas\Components\Html::make('<hr class="border-gray-200 dark:border-white/10 my-4" />')
                            ->columnSpan('full'),

                        // Información Personal del Empleado
                        Section::make('Información Personal del Empleado')
                            ->schema([
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
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->label('Correo Electrónico')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                    ]),

                                \Filament\Schemas\Components\Html::make('<hr class="border-gray-200 dark:border-white/10 my-4" />')
                                    ->columnSpan('full'),

                                // Discapacidad / Incapacidad
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

                                        TextInput::make('incapacidad')
                                            ->label('Incapacidad')
                                            ->maxLength(255)
                                            ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad')),

                                        DatePicker::make('fecha_resolucion_discapacidad')
                                            ->label('Fecha de resolución')
                                            ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                            ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),

                                        Toggle::make('pertenece_andalucia')
                                            ->label('¿Pertenece a Andalucía?')
                                            ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                            ->default(false),

                                        FileUpload::make('resolucion_discapacidad')
                                            ->label('Resolución de Discapacidad (Archivo)')
                                            ->directory('empleados/resoluciones')
                                            ->disk('local')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // Información de Contratación
                        Grid::make(2)
                            ->schema([
                                Select::make('tipo_contrato')
                                    ->label('Tipo de Contrato')
                                    ->options([
                                        'Eventual' => 'Eventual',
                                        'Indefinido' => 'Indefinido',
                                    ])
                                    ->required()
                                    ->live(),

                                DatePicker::make('fecha_vencimiento_contrato')
                                    ->label('Fecha de vencimiento')
                                    ->visible(fn (Get $get) => $get('tipo_contrato') === 'Eventual')
                                    ->required(fn (Get $get) => $get('tipo_contrato') === 'Eventual'),
                            ]),
                    ]),

                Section::make('Documentación Inicial')
                    ->description('Adjuntar documentos iniciales del empleado')
                    ->visible(fn ($context) => $context === 'create')
                    ->schema([
                        Repeater::make('documentos')
                            ->relationship('documentos')
                            ->schema([
                                Select::make('tipo')
                                    ->label('Tipo de Documento')
                                    ->options([
                                        'DNI' => 'DNI / NIE',
                                        'Certificados' => 'Certificados',
                                        'Contratos' => 'Contratos',
                                        'Titulaciones' => 'Titulaciones',
                                        'Carnets' => 'Carnets',
                                        'Otros' => 'Otros documentos',
                                    ])
                                    ->required(),
                                TextInput::make('nombre')
                                    ->label('Nombre del Documento')
                                    ->required(),
                                FileUpload::make('file_path')
                                    ->label('Archivo')
                                    ->directory('empleados/documentos')
                                    ->disk('local')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->label('Documentos')
                            ->createItemButtonLabel('Añadir otro documento'),
                    ]),
            ]);
    }
}
