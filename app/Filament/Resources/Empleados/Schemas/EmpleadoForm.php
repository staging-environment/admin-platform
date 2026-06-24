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
                                TextInput::make('tipo_discapacidad')
                                    ->label('Tipo de Discapacidad')
                                    ->maxLength(255),
                                Slider::make('porcentaje_discapacidad')
                                    ->label(fn (Get $get) => 'Porcentaje de Discapacidad: ' . round($get('porcentaje_discapacidad') ?? 0) . '%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(0)
                                    ->step(1)
                                    ->live(),
                                TextInput::make('incapacidad')
                                    ->label('Incapacidad')
                                    ->maxLength(255),
                                FileUpload::make('resolucion_discapacidad')
                                    ->label('Resolución de Discapacidad (Archivo)')
                                    ->directory('empleados/resoluciones')
                                    ->disk('local')
                                    ->acceptedFileTypes(['application/pdf', 'image/*']),
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
