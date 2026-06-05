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
                                Grid::make(1)
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
        $bgStart = '#0f172a'; // Deep slate base
        $bgEnd = '#1e1b4b';
        
        $skinColor = '#fed7aa'; // Light warm peach
        $hairColor = $sexo === 'Femenino' ? '#f43f5e' : '#f59e0b'; // Rose vs Amber
        
        $hairSvg = '';
        $faceDetails = '';
        $accessories = '';
        $bodySvg = '<path d="M15 75 C15 65 25 58 40 58 C55 58 65 65 65 75 Z" fill="#312e81" />'; // Standard shoulders
        
        if ($porcentaje === 0) {
            $stateTitle = "Aspecto Perfecto (0%)";
            $stateDesc = $sexo === 'Femenino' ? "Femenino - Muy Atractiva" : ($sexo === 'Masculino' ? "Masculino - Muy Atractivo" : "Otro - Aspecto Radiante");
            
            // State 1: Symmetrical, clean hair, beautiful eyes, bright smile
            $hairSvg = $sexo === 'Femenino' 
                ? '<path d="M40 16 C25 16 15 28 15 50 C15 68 22 82 22 92 C28 92 34 85 40 75 C46 85 52 92 58 92 C58 82 65 68 65 50 C65 28 55 16 40 16 Z" fill="' . $hairColor . '" />'
                : '<path d="M28 24 C22 14 34 4 52 9 C62 14 60 25 56 30 C52 30 48 28 44 30 Z" fill="' . $hairColor . '" />';
            
            $faceDetails = $sexo === 'Femenino'
                ? '<circle cx="34" cy="46" r="3" fill="#0f172a" />
                   <circle cx="46" cy="46" r="3" fill="#0f172a" />
                   <path d="M 31 42 Q 34 39 37 42" stroke="#0f172a" stroke-width="1.5" fill="none" />
                   <path d="M 43 42 Q 46 39 49 42" stroke="#0f172a" stroke-width="1.5" fill="none" />
                   <path d="M36 56 Q40 60 44 56" stroke="#f43f5e" stroke-width="2.5" fill="none" stroke-linecap="round" />'
                : '<circle cx="34" cy="46" r="3" fill="#0f172a" />
                   <circle cx="46" cy="46" r="3" fill="#0f172a" />
                   <path d="M 31 41 Q 34 39 37 41" stroke="#0f172a" stroke-width="1.5" fill="none" />
                   <path d="M 43 41 Q 46 39 49 41" stroke="#0f172a" stroke-width="1.5" fill="none" />
                   <path d="M35 56 Q40 61 45 56" stroke="#0f172a" stroke-width="2.5" fill="none" stroke-linecap="round" />';
                   
            $accessories = $sexo === 'Femenino'
                ? '<circle cx="19" cy="50" r="2" fill="#fbbf24" /><circle cx="61" cy="50" r="2" fill="#fbbf24" />' // Cute earrings
                : '<path d="M 28 42 L 52 42" stroke="#0284c7" stroke-width="2" />
                   <path d="M 28 40 L 36 45 L 36 40 Z" fill="#0284c7" />
                   <path d="M 44 40 L 52 45 L 52 40 Z" fill="#0284c7" />'; // Cool glasses
        } elseif ($porcentaje <= 33) {
            $stateTitle = "Afectación Leve (1-33%)";
            $stateDesc = "Pequeño Vendaje y Cara Goofy";
            
            // State 2: Slightly messy hair, small head bandage, one eye lower, crooked mouth
            $hairSvg = $sexo === 'Femenino' 
                ? '<path d="M40 16 C25 16 15 28 15 50 C15 68 22 82 22 92 Z" fill="' . $hairColor . '" />'
                : '<path d="M26 24 C20 15 32 6 48 10 C58 15 54 28 50 32 Z" fill="' . $hairColor . '" />';
            
            $faceDetails = '<circle cx="34" cy="45" r="2.5" fill="#0f172a" />
                            <circle cx="47" cy="48" r="1.5" fill="#0f172a" /> <!-- Smaller/asymmetric eye -->
                            <path d="M 31 41 Q 34 38 37 41" stroke="#0f172a" stroke-width="1.5" fill="none" />
                            <path d="M 44 45 Q 47 43 50 45" stroke="#0f172a" stroke-width="1.5" fill="none" />
                            <path d="M34 58 Q42 54 44 60" stroke="#0f172a" stroke-width="2" fill="none" stroke-linecap="round" /> <!-- Crooked smile -->
                            <path d="M25 52 L31 52 M28 49 L28 55" stroke="#ef4444" stroke-width="1.5" />'; // Red band-aid on cheek
            
            $accessories = '<path d="M21 28 L59 36" stroke="#f8fafc" stroke-width="4.5" stroke-linecap="round" opacity="0.95" />'; // Head band-aid
        } elseif ($porcentaje <= 66) {
            $stateTitle = "Afectación Moderada (34-66%)";
            $stateDesc = "Parche en Ojo, Vendaje y Cabestrillo";
            
            // State 3: Messy spiky hair, eye patch, spiral eye, missing tooth, shoulder cast/cabestrillo
            $hairSvg = '<path d="M23 26 L20 18 L28 22 L34 14 L38 23 L46 12 L50 22 Z" fill="' . $hairColor . '" />';
            
            $faceDetails = '<!-- Goofy spiral eye -->
                            <circle cx="33" cy="47" r="3" stroke="#0f172a" stroke-width="1" fill="none" />
                            <path d="M31 47 Q33 45 35 47 Q33 49 31 47" stroke="#0f172a" stroke-width="1.5" fill="none" />
                            <!-- Eye patch on the right eye -->
                            <path d="M40 38 L54 52" stroke="#0f172a" stroke-width="2" />
                            <rect x="42" y="42" width="10" height="10" fill="#0f172a" rx="2" />
                            <!-- Missing tooth smile -->
                            <path d="M33 58 Q40 63 46 56" stroke="#0f172a" stroke-width="2.5" fill="none" />
                            <rect x="38" y="58" width="3" height="3" fill="#ffffff" />
                            <rect x="43" y="57" width="2" height="3" fill="#ffffff" />';
            
            $accessories = '<!-- Big head wrap -->
                            <path d="M21 22 Q40 18 59 26" stroke="#e2e8f0" stroke-width="6" stroke-linecap="round" />
                            <path d="M23 27 Q40 24 57 31" stroke="#e2e8f0" stroke-width="5" stroke-linecap="round" />';
            
            $bodySvg = '<!-- Body with arm cast/cabestrillo -->
                        <path d="M15 75 C15 65 25 58 40 58 C55 58 65 65 65 75 Z" fill="#312e81" />
                        <path d="M20 65 L48 75 L18 75 Z" fill="#e2e8f0" stroke="#cbd5e1" stroke-width="1" /> <!-- Arm cast -->';
        } else {
            $stateTitle = "Afectación Severa (67-100%)";
            $stateDesc = "Cara Deformada, Yeso Total y Silla de Ruedas";
            
            // State 4: Crazy wild hair, fully wrapped head, crossed dizzy eyes, drool drop, wheelchair backing
            $hairSvg = '<path d="M 18 25 L 12 18 L 22 22 L 30 10 L 38 21 L 52 8 L 56 22 L 68 15 Z" fill="' . $hairColor . '" />';
            
            $faceDetails = '<!-- Crossed dizzy eyes of different sizes -->
                            <circle cx="31" cy="45" r="4.5" fill="#f8fafc" stroke="#0f172a" />
                            <line x1="29" y1="43" x2="33" y2="47" stroke="#ef4444" stroke-width="1.5" />
                            <line x1="33" y1="43" x2="29" y2="47" stroke="#ef4444" stroke-width="1.5" />
                            
                            <circle cx="49" cy="49" r="2.5" fill="#f8fafc" stroke="#0f172a" />
                            <line x1="48" y1="48" x2="50" y2="50" stroke="#ef4444" stroke-width="1" />
                            <line x1="50" y1="48" x2="48" y2="50" stroke="#ef4444" stroke-width="1" />
                            
                            <!-- Goofy drooling mouth -->
                            <path d="M31 58 Q38 66 48 57" stroke="#0f172a" stroke-width="2.5" fill="none" stroke-linecap="round" />
                            <path d="M44 60 C44 63 42 66 40 66 C38 66 38 63 38 60 Z" fill="#38bdf8" opacity="0.8" /> <!-- Drool -->
                            <path d="M26 40 L34 38 M46 42 L54 40" stroke="#0f172a" stroke-width="1.5" />'; // Confused eyebrows
            
            $accessories = '<!-- Mummy-like total head cast wrapping -->
                            <path d="M 23 20 L 57 20" stroke="#f8fafc" stroke-width="8" />
                            <path d="M 21 28 L 59 34" stroke="#f8fafc" stroke-width="6" />
                            <path d="M 20 38 L 28 42" stroke="#f8fafc" stroke-width="6" />
                            <path d="M 52 42 L 60 38" stroke="#f8fafc" stroke-width="6" />
                            <!-- Neck brace cast -->
                            <rect x="31" y="51" width="18" height="6" fill="#e2e8f0" rx="1" stroke="#cbd5e1" stroke-width="1" />';
            
            $bodySvg = '<!-- Body + Wheelchair background -->
                        <!-- Wheelchair handles and backing -->
                        <rect x="10" y="52" width="6" height="23" fill="#475569" rx="1" />
                        <rect x="64" y="52" width="6" height="23" fill="#475569" rx="1" />
                        <path d="M 8 58 L 72 58" stroke="#64748b" stroke-width="4" stroke-linecap="round" />
                        <!-- Body -->
                        <path d="M15 75 C15 65 25 58 40 58 C55 58 65 65 65 75 Z" fill="#312e81" />
                        <path d="M18 64 L50 75 L16 75 Z" fill="#e2e8f0" stroke="#cbd5e1" stroke-width="1.5" /> <!-- Body cast wrap -->';
        }

        return <<<HTML
<style>
    .noUi-handle, [role="slider"], .noUi-origin .noUi-handle {
        background: #3b82f6 !important;
        border-color: #1d4ed8 !important;
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.6) !important;
    }
    .noUi-connect {
        background: #3b82f6 !important;
    }
</style>
<div class="flex flex-col items-center justify-center p-6 bg-slate-900 rounded-2xl border border-slate-800 shadow-xl max-w-sm mx-auto transition-all duration-300">
    <div class="relative w-48 h-48 mb-4">
        <svg viewBox="0 0 80 80" class="w-full h-full drop-shadow-[0_0_15px_rgba(99,102,241,0.25)]">
            <defs>
                <linearGradient id="avatarBg" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="{$bgStart}" />
                    <stop offset="100%" stop-color="{$bgEnd}" />
                </linearGradient>
            </defs>
            
            <!-- Background Circular Badge -->
            <circle cx="40" cy="40" r="38" fill="url(#avatarBg)" stroke="#4f46e5" stroke-width="2" />
            
            <!-- Back Hair (if Femenino & 0-33%) -->
            {$hairSvg}
            
            <!-- Body / Shoulders -->
            {$bodySvg}
            
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

