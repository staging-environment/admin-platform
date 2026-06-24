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
            ->components([
                Section::make('Datos Personales')
                    ->description('Información básica de identificación')
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
                    ]),

                Section::make('Contacto y Dirección')
                    ->schema([
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
                    ]),

                Section::make('Discapacidad / Incapacidad')
                    ->description('Información opcional sobre discapacidad o incapacidades')
                    ->schema([
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

                Section::make('Horario Laboral Inicial')
                    ->description('Configurar la jornada y horario inicial del empleado')
                    ->visible(fn ($context) => $context === 'create')
                    ->schema([
                        Repeater::make('horarios')
                            ->relationship('horarios')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('tipo_jornada')
                                            ->label('Tipo de Jornada')
                                            ->options([
                                                'Completa' => 'Jornada Completa',
                                                'Parcial' => 'Jornada Parcial',
                                                'Reducida' => 'Jornada Reducida',
                                                'Otros' => 'Otros',
                                            ])
                                            ->required(),
                                        TextInput::make('turnos')
                                            ->label('Turnos Asignados (Opcional)')
                                            ->placeholder('Ej. Mañana, Tarde, Rotativo...')
                                            ->maxLength(255),
                                    ]),
                                CheckboxList::make('dias_laborales')
                                    ->label('Días Laborales')
                                    ->options([
                                        'Lunes' => 'Lunes',
                                        'Martes' => 'Martes',
                                        'Miércoles' => 'Miércoles',
                                        'Jueves' => 'Jueves',
                                        'Viernes' => 'Viernes',
                                        'Sábado' => 'Sábado',
                                        'Domingo' => 'Domingo',
                                    ])
                                    ->columns(7)
                                    ->columnSpan('full')
                                    ->required(),
                                Grid::make(2)
                                    ->schema([
                                        TimePicker::make('hora_inicio')
                                            ->label('Hora de Inicio')
                                            ->required(),
                                        TimePicker::make('hora_fin')
                                            ->label('Hora de Fin')
                                            ->required(),
                                    ]),
                                Textarea::make('horarios')
                                    ->label('Detalles del Horario')
                                    ->placeholder('Ej. Lunes a Viernes de 9:00 a 18:00...')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpan('full'),
                                FileUpload::make('calendario_laboral_path')
                                    ->label('Calendario Laboral (PDF/Imagen)')
                                    ->directory('empleados/calendarios')
                                    ->disk('local')
                                    ->columnSpan('full'),
                            ])
                            ->columnSpan('full')
                            ->label('Horarios')
                            ->createItemButtonLabel('Añadir horario/turno')
                    ]),
            ]);
    }
}

