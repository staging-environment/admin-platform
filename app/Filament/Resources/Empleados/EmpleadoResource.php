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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('user', function ($query) {
                $query->role('Empleado');
            });
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
                \Filament\Schemas\Components\Section::make()
                    ->heading(fn ($record) => new \Illuminate\Support\HtmlString(
                        view('filament.components.alerts-header', ['record' => $record])->render()
                    ))
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Schemas\Components\Html::make('
                            <div style="display: flex; align-items: center; gap: 12px; padding: 16px; margin-bottom: 16px;" class="bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800/30 rounded-2xl text-sm text-red-800 dark:text-red-400">
                                <svg style="width: 24px; height: 24px; flex-shrink: 0;" class="text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <strong class="font-bold">DNI / NIE no registrado:</strong>
                                    Este empleado no tiene ningún documento de DNI o NIE asociado en su ficha. Por favor, suba el documento correspondiente para registrar y validar su fecha de caducidad.
                                </div>
                            </div>
                        ')
                            ->visible(fn ($record) => $record && !$record->documentos()->where('tipo', 'DNI')->exists())
                            ->columnSpanFull(),

                        \Filament\Schemas\Components\Grid::make(4)
                            ->schema([
                                \Filament\Infolists\Components\ImageEntry::make('foto')
                                    ->label('Foto de perfil')
                                    ->circular()
                                    ->defaultImageUrl(fn ($record) => match ($record?->sexo) {
                                        'Masculino' => asset('images/avatar-male.svg'),
                                        'Femenino' => asset('images/avatar-female.svg'),
                                        default => asset('images/avatar-generic.svg'),
                                    })
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
                                    ->label('Correo Electrónico'),
                                \Filament\Infolists\Components\TextEntry::make('iban')
                                        ->label('Código IBAN')
                                        ->placeholder('No especificado')
                                        ->copyable(),
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
                                    ->state(function ($record) {
                                        $latest = $record?->documentos()->where('tipo', 'Contratos')->latest('id')->first();
                                        if ($latest && $latest->gasolinera_codigo) {
                                            return \App\Models\Gasolinera::where('Codigo', $latest->gasolinera_codigo)->first()?->Nombre;
                                        }
                                        return $record?->gasolinera?->Nombre;
                                    })
                                    ->placeholder('No asignada'),
                                \Filament\Infolists\Components\TextEntry::make('puesto')
                                    ->label('Puesto')
                                    ->state(function ($record) {
                                        $latest = $record?->documentos()->where('tipo', 'Contratos')->latest('id')->first();
                                        if ($latest && $latest->puesto) {
                                            return $latest->puesto;
                                        }
                                        return $record?->puesto;
                                    })
                                    ->placeholder('No asignado'),
                                 \Filament\Infolists\Components\TextEntry::make('tipo_contrato')
                                      ->label('Tipo de Contrato')
                                      ->state(function ($record) {
                                          $latest = $record?->documentos()->where('tipo', 'Contratos')->latest('id')->first();
                                          $val = ($latest && $latest->tipo_contrato) ? $latest->tipo_contrato : ($record?->tipo_contrato);
                                          return $val === 'Indefinido' ? 'Fijo' : $val;
                                      })
                                      ->placeholder('Sin contrato registrado')
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
                                    ->columnSpan(1),
                                \Filament\Infolists\Components\TextEntry::make('estado')
                                    ->label('Estado')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Alta' => 'success',
                                        'Baja' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                \Filament\Schemas\Components\Section::make('Información de Baja')
                    ->visible(fn ($record) => $record && $record->estado === 'Baja')
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('fecha_baja')
                                    ->label('Fecha de baja')
                                    ->date('d/m/Y')
                                    ->placeholder('No registrada'),
                                \Filament\Infolists\Components\TextEntry::make('motivo_baja')
                                    ->label('Motivo de la baja')
                                    ->placeholder('No registrado'),
                                \Filament\Infolists\Components\TextEntry::make('observaciones_baja')
                                    ->label('Observaciones / Detalles')
                                    ->placeholder('Ninguna'),
                            ]),
                    ]),

                \Filament\Schemas\Components\Section::make('Discapacidad / Incapacidad')
                    ->columnSpanFull()
                    ->visible(fn () => auth()->user()?->can('ver_documentacion_empleados') ?? true)
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(4)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('tiene_discapacidad')
                                    ->label('¿Tiene Discapacidad?')
                                    ->html()
                                    ->state(function (?\App\Models\Empleado $record) {
                                        if (!$record || !$record->tiene_discapacidad) {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">No</span>');
                                        }
                                        
                                        $badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">Sí</span>';
                                        
                                        $docs = [
                                            'ver_resolucion' => $record->documentos()->where('tipo', 'Resolución Discapacidad')->exists(),
                                            'ver_dictamen' => $record->documentos()->where('tipo', 'Dictamen Técnico')->exists(),
                                            'ver_certificado' => $record->documentos()->where('tipo', 'Certificado Discapacidad')->exists(),
                                        ];
                                        
                                        $iconsHtml = '';
                                        foreach ($docs as $actionName => $exists) {
                                            if ($exists) {
                                                $label = match($actionName) {
                                                    'ver_resolucion' => 'Resolución',
                                                    'ver_dictamen' => 'Dictamen',
                                                    'ver_certificado' => 'Certificado',
                                                };
                                                $iconsHtml .= '
                                                    <a href="#" x-on:click.prevent="$wire.mountAction(\'' . $actionName . '\')" title="Ver ' . $label . ' Discapacidad" style="display: inline-flex; align-items: center; justify-content: center; padding: 4px; color: #d97706; transition: all 0.2s;" class="hover:bg-amber-50 dark:hover:bg-amber-950/20 rounded-md">
                                                        <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                ';
                                            }
                                        }
                                        
                                        return new \Illuminate\Support\HtmlString('<div style="display: flex; align-items: center; gap: 6px;">' . $badge . $iconsHtml . '</div>');
                                    }),

                                \Filament\Infolists\Components\TextEntry::make('tiene_incapacidad')
                                    ->label('¿Tiene Incapacidad?')
                                    ->html()
                                    ->state(function (?\App\Models\Empleado $record) {
                                        if (!$record || !$record->tiene_incapacidad) {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">No</span>');
                                        }
                                        
                                        $badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">Sí</span>';
                                        
                                        $exists = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->exists();
                                        $iconHtml = '';
                                        if ($exists) {
                                            $iconHtml = '
                                                <a href="#" x-on:click.prevent="$wire.mountAction(\'ver_incapacidad\')" title="Ver Documento Incapacidad" style="display: inline-flex; align-items: center; justify-content: center; padding: 4px; color: #d97706; transition: all 0.2s;" class="hover:bg-amber-50 dark:hover:bg-amber-950/20 rounded-md">
                                                    <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            ';
                                        }
                                        
                                        return new \Illuminate\Support\HtmlString('<div style="display: flex; align-items: center; gap: 6px;">' . $badge . $iconHtml . '</div>');
                                    }),

                                \Filament\Infolists\Components\TextEntry::make('no_tiene_discapacidad')
                                    ->label('No tiene Discapacidad / Incapacidad')
                                    ->html()
                                    ->state(function (?\App\Models\Empleado $record) {
                                        if ($record && $record->no_tiene_discapacidad) {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-300">Sí</span>');
                                        }
                                        return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">No</span>');
                                    }),

                                \Filament\Infolists\Components\TextEntry::make('tipo_discapacidad')
                                    ->label('Tipo de Discapacidad')
                                    ->visible(fn ($record) => $record && $record->tiene_discapacidad)
                                    ->state(function (?\App\Models\Empleado $record) {
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
