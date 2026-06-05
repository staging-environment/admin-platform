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
                                                'Otro' => 'Otro',
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
                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('tipo_discapacidad')
                                            ->label('Tipo de Discapacidad')
                                            ->maxLength(255),
                                        Slider::make('porcentaje_discapacidad')
                                            ->label('Porcentaje de Discapacidad')
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->default(0)
                                            ->live(),
                                        TextInput::make('incapacidad')
                                            ->label('Incapacidad')
                                            ->maxLength(255),
                                        FileUpload::make('resolucion_discapacidad')
                                            ->label('Resolución de Discapacidad (Archivo)')
                                            ->directory('empleados/resoluciones')
                                            ->disk('local')
                                            ->acceptedFileTypes(['application/pdf', 'image/*']),
                                    ])
                                    ->columnSpan(1),
                                Placeholder::make('avatar_preview')
                                    ->label('Visualización del Empleado (Progreso)')
                                    ->content(function (Get $get) {
                                        $sexo = $get('sexo');
                                        $porcentaje = $get('porcentaje_discapacidad');
                                        return new HtmlString(self::getAvatarSvg($sexo, $porcentaje));
                                    })
                                    ->columnSpan(1),
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

    public static function getAvatarSvg(?string $sexo, ?int $porcentaje): string
    {
        $porcentaje = $porcentaje ?? 0;
        $sexo = $sexo ?? 'Masculino';

        // Choose color palette
        $bgStart = '#1e1b4b'; // Sleek dark indigo base
        $bgEnd = '#311042';
        
        $skinColor = '#fed7aa'; // Light warm peach
        $hairColor = $sexo === 'Femenino' ? '#f43f5e' : '#f59e0b'; // Rose vs Amber
        
        // Determine state
        if ($porcentaje === 0) {
            $stateTitle = "Estado Base (Óptimo)";
            $stateDesc = $sexo === 'Femenino' ? "Femenino - Aspecto Elegante" : ($sexo === 'Masculino' ? "Masculino - Aspecto Atractivo" : "Otro - Aspecto Radiante");
            
            // Draw State 1 (Base: handsome/beautiful)
            $hairSvg = $sexo === 'Femenino' 
                ? '<path d="M40 22 C30 22 20 32 20 52 C20 67 25 82 25 92 C30 92 35 87 40 77 C45 87 50 92 55 92 C55 82 60 67 60 52 C60 32 50 22 40 22 Z" fill="' . $hairColor . '" />'
                : '<path d="M30 28 C25 18 35 8 50 13 C60 18 58 28 55 33 C52 33 48 31 45 33 Z" fill="' . $hairColor . '" />';
            
            $faceDetails = $sexo === 'Femenino'
                ? '<circle cx="35" cy="48" r="2.5" fill="#0f172a" /><circle cx="45" cy="48" r="2.5" fill="#0f172a" />
                   <path d="M 33 45 Q 35 43 37 45" stroke="#0f172a" stroke-width="1" fill="none" />
                   <path d="M 43 45 Q 45 43 47 45" stroke="#0f172a" stroke-width="1" fill="none" />
                   <path d="M37 57 Q40 60 43 57" stroke="#f43f5e" stroke-width="2" fill="none" stroke-linecap="round" />'
                : '<circle cx="35" cy="48" r="2.5" fill="#0f172a" /><circle cx="45" cy="48" r="2.5" fill="#0f172a" />
                   <path d="M37 57 Q40 61 43 57" stroke="#0f172a" stroke-width="2" fill="none" stroke-linecap="round" />';

            $accessories = $sexo === 'Femenino'
                ? '<path d="M22 48 Q20 51 22 54" stroke="#fbbf24" stroke-width="2.5" fill="none" />
                   <path d="M58 48 Q60 51 58 54" stroke="#fbbf24" stroke-width="2.5" fill="none" />' // Gold earrings
                : '<path d="M 28 43 L 52 43" stroke="#0284c7" stroke-width="2.5" />
                   <path d="M 28 41 L 38 47 L 38 41 Z" fill="#0284c7" />
                   <path d="M 42 41 L 52 47 L 52 41 Z" fill="#0284c7" />'; // Cool sunglasses
        } elseif ($porcentaje <= 33) {
            $stateTitle = "Estado Táctico (1-33%)";
            $stateDesc = "Héroe en Combate (Vendaje y Cicatriz)";
            
            $hairSvg = $sexo === 'Femenino'
                ? '<path d="M40 22 C30 22 20 32 20 52 C20 67 25 82 25 92 C30 92 35 87 40 77 C45 87 50 92 55 92 C55 82 60 67 60 52 C60 32 50 22 40 22 Z" fill="' . $hairColor . '" />'
                : '<path d="M30 28 C25 18 35 8 50 13 C60 18 58 28 55 33 C52 33 48 31 45 33 Z" fill="' . $hairColor . '" />';
            
            $faceDetails = '<circle cx="35" cy="48" r="2.5" fill="#0f172a" />
                            <path d="M42 45 L48 51 M48 45 L42 51" stroke="#ef4444" stroke-width="1.5" />
                            <path d="M37 57 Q40 60 43 57" stroke="#0f172a" stroke-width="2" fill="none" stroke-linecap="round" />'; // Eye scratch
            
            $accessories = '<path d="M23 38 L57 32" stroke="#f8fafc" stroke-width="4" stroke-linecap="round" opacity="0.9"/>
                            <path d="M25 41 L35 39" stroke="#f8fafc" stroke-width="3" stroke-linecap="round" opacity="0.9"/>'; // Headband/bandage
        } elseif ($porcentaje <= 66) {
            $stateTitle = "Cyber-Héroe (34-66%)";
            $stateDesc = "Mejora Cibernética (Visor Láser)";
            
            $hairSvg = $sexo === 'Femenino'
                ? '<path d="M40 22 C30 22 20 32 20 52 C20 67 25 82 25 92 C30 92 35 87 40 77 C45 87 50 92 55 92 C55 82 60 67 60 52 C60 32 50 22 40 22 Z" fill="' . $hairColor . '" />'
                : '<path d="M30 28 C25 18 35 8 50 13 C60 18 58 28 55 33 C52 33 48 31 45 33 Z" fill="' . $hairColor . '" />';
            
            $faceDetails = '<circle cx="35" cy="48" r="2.5" fill="#0f172a" />
                            <path d="M37 57 Q40 60 43 57" stroke="#0f172a" stroke-width="2" fill="none" stroke-linecap="round" />';
            
            $accessories = '<rect x="40" y="42" width="16" height="8" rx="2" fill="#10b981" opacity="0.85" />
                            <line x1="22" y1="46" x2="40" y2="46" stroke="#94a3b8" stroke-width="1.5" />
                            <circle cx="48" cy="46" r="2.5" fill="#f43f5e" />
                            <line x1="48" y1="46" x2="75" y2="46" stroke="#f43f5e" stroke-width="1" stroke-dasharray="1 1" />'; // Laser sight targeting target
        } else {
            $stateTitle = "Héroe Cósmico (67-100%)";
            $stateDesc = "Armadura y Aura de Energía";
            
            $hairSvg = $sexo === 'Femenino'
                ? '<path d="M40 22 C30 22 20 32 20 52 C20 67 25 82 25 92 C30 92 35 87 40 77 C45 87 50 92 55 92 C55 82 60 67 60 52 C60 32 50 22 40 22 Z" fill="#38bdf8" />'
                : '<path d="M30 28 C25 18 35 8 50 13 C60 18 58 28 55 33 C52 33 48 31 45 33 Z" fill="#38bdf8" />';
            
            $faceDetails = '<circle cx="34" cy="48" r="2.5" fill="#38bdf8" />
                            <circle cx="46" cy="48" r="2.5" fill="#38bdf8" />
                            <path d="M37 57 Q40 60 43 57" stroke="#38bdf8" stroke-width="2" fill="none" stroke-linecap="round" />';
            
            $accessories = '<path d="M22 20 L58 20 L65 35 L15 35 Z" fill="url(#cosmicArmor)" opacity="0.9" />
                            <circle cx="40" cy="27" r="4" fill="#f43f5e" />
                            <!-- Cosmic aura -->
                            <circle cx="40" cy="45" r="34" stroke="#38bdf8" stroke-width="1.5" fill="none" opacity="0.5" stroke-dasharray="3 3"/>';
        }

        return <<<HTML
<div class="flex flex-col items-center justify-center p-6 bg-slate-900 rounded-2xl border border-slate-800 shadow-xl max-w-sm mx-auto transition-all duration-300">
    <div class="relative w-48 h-48 mb-4">
        <svg viewBox="0 0 80 80" class="w-full h-full drop-shadow-[0_0_15px_rgba(99,102,241,0.3)]">
            <defs>
                <linearGradient id="avatarBg" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="{$bgStart}" />
                    <stop offset="100%" stop-color="{$bgEnd}" />
                </linearGradient>
                <linearGradient id="cosmicArmor" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#3b82f6" />
                    <stop offset="100%" stop-color="#1d4ed8" />
                </linearGradient>
            </defs>
            
            <!-- Background Circular Badge -->
            <circle cx="40" cy="40" r="38" fill="url(#avatarBg)" stroke="#4f46e5" stroke-width="2" />
            
            <!-- Back Hair (if Femenino) -->
            {$hairSvg}
            
            <!-- Body / Shoulders -->
            <path d="M15 75 C15 65 25 58 40 58 C55 58 65 65 65 75 Z" fill="#312e81" />
            
            <!-- Head / Neck -->
            <rect x="35" y="44" width="10" height="15" fill="{$skinColor}" />
            <circle cx="40" cy="40" r="14" fill="{$skinColor}" />
            
            <!-- Hair Overlay / Bangs -->
            {$hairSvg}
            
            <!-- Face Details -->
            {$faceDetails}
            
            <!-- Special accessories depending on state -->
            {$accessories}
            
        </svg>
    </div>
    <div class="text-center">
        <h4 class="text-md font-bold text-slate-100 mb-1">{$stateTitle}</h4>
        <p class="text-xs text-indigo-400 font-semibold mb-1">{$stateDesc}</p>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
            Discapacidad: {$porcentaje}%
        </span>
    </div>
</div>
HTML;
    }
}

