<?php

namespace App\Filament\Resources\Empleados\Pages;

use App\Filament\Resources\Empleados\EmpleadoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ViewEmpleado extends ViewRecord
{
    protected static string $resource = EmpleadoResource::class;

    public function getCachedHeaderActions(): array
    {
        return array_filter(
            parent::getCachedHeaderActions(),
            fn ($action) => method_exists($action, 'getName') && !in_array($action->getName(), ['ver_incapacidad', 'ver_resolucion', 'ver_dictamen', 'ver_certificado'])
        );
    }

    public function content(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                $this->getInfolistContentComponent()
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'ficha-empleado-container']),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('notificacionesDocuments')
                ->label('Notificaciones')
                ->icon('heroicon-o-bell')
                ->color(function ($record) {
                    if (!$record) return 'gray';
                    $hasNotifs = $record->notificaciones()->exists();
                    return $hasNotifs ? 'warning' : 'gray';
                })
                ->extraAttributes(function ($record) {
                    $hasNotifs = $record?->notificaciones()->exists();
                    if (!$hasNotifs) {
                        return ['style' => 'background-color: #e2e8f0 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;'];
                    }
                    return [];
                })
                ->modalHeading('Notificaciones del Empleado')
                ->modalWidth('7xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalContent(fn ($record) => view('filament.pages.notificaciones-modal', ['record' => $record]))
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
            \Filament\Actions\Action::make('dniDocuments')
                ->label('DNI')
                ->icon('heroicon-o-identification')
                ->color(function ($record) {
                    if (!$record) return 'gray';
                    $hasDocs = $record->documentos()->where('tipo', 'DNI')->exists();
                    $isExpired = $record->fecha_caducidad_dni && $record->fecha_caducidad_dni->isPast();
                    $hasAlert = $record->alertas()->whereIn('tipo', ['sin_dni', 'dni_caducado'])->exists();
                    if (!$hasDocs || $isExpired || $hasAlert) {
                        return 'danger';
                    }
                    return 'success';
                })
                ->modalHeading("DNI's asociados al empleado")
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalContent(fn ($record) => view('filament.pages.documentos-modal', ['record' => $record, 'family' => 'dni']))
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
            \Filament\Actions\Action::make('contratosDocuments')
                ->label('Contratos')
                ->icon('heroicon-o-document-text')
                ->color(function ($record) {
                    if (!$record) return 'gray';
                    $hasDocs = $record->documentos()->where('tipo', 'Contratos')->exists();
                    if (!$hasDocs) {
                        return 'danger';
                    }
                    $latest = $record->documentos()->where('tipo', 'Contratos')->latest('id')->first();
                    $isExpired = $latest && $latest->tipo_contrato === 'Eventual' && $latest->fecha_vencimiento_contrato && $latest->fecha_vencimiento_contrato->isPast();
                    $hasAlert = $record->alertas()->whereIn('tipo', ['sin_contrato', 'contrato_vencido'])->exists();
                    if ($isExpired || $hasAlert) {
                        return 'danger';
                    }
                    return 'success';
                })
                ->modalHeading('Documentos Contratos')
                ->modalWidth('7xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalContent(fn ($record) => view('filament.pages.documentos-modal', ['record' => $record, 'family' => 'contratos']))
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
            \Filament\Actions\Action::make('formacionDocuments')
                ->label('Formación')
                ->icon('heroicon-o-academic-cap')
                ->color(function ($record) {
                    if (!$record) return 'gray';
                    $hasDocs = $record->documentos()->whereIn('tipo', ['Certificados', 'Titulaciones', 'Carnets', 'Otros', 'Prevención de riesgos laborales', 'Manipulación de alimentos'])->exists();
                    $hasCursos = $record->cursos()->exists();
                    return ($hasDocs || $hasCursos) ? 'success' : 'gray';
                })
                ->extraAttributes(function ($record) {
                    if (!$record) return ['style' => 'background-color: #e2e8f0 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;'];
                    $hasDocs = $record->documentos()->whereIn('tipo', ['Certificados', 'Titulaciones', 'Carnets', 'Otros', 'Prevención de riesgos laborales', 'Manipulación de alimentos'])->exists();
                    $hasCursos = $record->cursos()->exists();
                    if (!$hasDocs && !$hasCursos) {
                        return ['style' => 'background-color: #e2e8f0 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;'];
                    }
                    return [];
                })
                ->modalHeading('Documentos Formación')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalContent(fn ($record) => view('filament.pages.documentos-modal', ['record' => $record, 'family' => 'formacion']))
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
            \Filament\Actions\Action::make('discapacidadIncapacidad')
                ->label('Discapacidad / Incapacidad')
                ->icon('heroicon-o-heart')
                ->color(function ($record) {
                    if (!$record) return 'gray';
                    $hasOption = $record->tiene_discapacidad || $record->tiene_incapacidad || $record->no_tiene_discapacidad;
                    $hasAlert = $record->alertas()->whereIn('tipo', [
                        'sin_discapacidad',
                        'discapacidad_archivos_pendientes',
                        'incapacidad_archivos_pendientes',
                        'falta_autorizacion_consulta',
                    ])->exists();

                    if (!$hasOption || $hasAlert) {
                        return 'danger';
                    }

                    if ($record->tiene_discapacidad || $record->tiene_incapacidad) {
                        $hasAuth = $record->documentos()->where('tipo', 'Autorización de Consulta')->exists();
                        if (!$hasAuth) {
                            return 'danger';
                        }

                        if ($record->tiene_discapacidad) {
                            $hasRes = $record->documentos()->where('tipo', 'Resolución Discapacidad')->exists();
                            $hasDict = $record->documentos()->where('tipo', 'Dictamen Técnico')->exists();
                            $hasCert = $record->documentos()->where('tipo', 'Certificado Discapacidad')->exists();
                            if (!$hasRes && !$hasDict && !$hasCert) {
                                return 'danger';
                            }
                        }

                        if ($record->tiene_incapacidad) {
                            $hasIncapDocs = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->exists();
                            if (!$hasIncapDocs) {
                                return 'danger';
                            }
                        }

                        return 'success';
                    }

                    return 'gray';
                })
                ->extraAttributes(function ($record) {
                    if (!$record) return ['style' => 'background-color: #e2e8f0 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;'];
                    $hasOption = $record->tiene_discapacidad || $record->tiene_incapacidad || $record->no_tiene_discapacidad;
                    $hasAlert = $record->alertas()->whereIn('tipo', [
                        'sin_discapacidad',
                        'discapacidad_archivos_pendientes',
                        'incapacidad_archivos_pendientes',
                        'falta_autorizacion_consulta',
                    ])->exists();

                    if ($hasOption && !$hasAlert && !$record->tiene_discapacidad && !$record->tiene_incapacidad) {
                        return ['style' => 'background-color: #e2e8f0 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;'];
                    }
                    return [];
                })
                ->modalHeading('Discapacidad / Incapacidad')
                ->modalWidth('4xl')
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->fillForm(function ($record) {
                    $res = $record->documentos()->where('tipo', 'Resolución Discapacidad')->first();
                    $dict = $record->documentos()->where('tipo', 'Dictamen Técnico')->first();
                    $cert = $record->documentos()->where('tipo', 'Certificado Discapacidad')->first();
                    $aut = $record->documentos()->where('tipo', 'Autorización de Consulta')->first();
                    $incapDocs = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->get();
                    
                    $incapacidadArchivos = [];
                    foreach ($incapDocs as $d) {
                        $incapacidadArchivos[] = [
                            'file_path' => $d->file_path,
                            'comentario' => $d->comentario,
                        ];
                    }

                    return [
                        'tiene_discapacidad' => $record->tiene_discapacidad,
                        'tipo_discapacidad' => $record->tipo_discapacidad,
                        'porcentaje_discapacidad' => $record->porcentaje_discapacidad,
                        'fecha_reconocimiento' => $record->fecha_reconocimiento,
                        'fecha_resolucion_discapacidad' => $record->fecha_resolucion_discapacidad,
                        'pertenece_andalucia' => $record->pertenece_andalucia,
                        'comunidad_autonoma' => $record->comunidad_autonoma,
                        'resolucion_discapacidad' => $res?->file_path,
                        'dictamen_tecnico' => $dict?->file_path,
                        'certificado_discapacidad' => $cert?->file_path,
                        'tiene_incapacidad' => $record->tiene_incapacidad,
                        'tipo_incapacidad' => $record->tipo_incapacidad,
                        'incapacidad_archivos' => $incapacidadArchivos,
                        'autorizacion_consulta' => $aut?->file_path,
                        'no_tiene_discapacidad' => $record->no_tiene_discapacidad,
                    ];
                })
                ->form([
                    Toggle::make('tiene_discapacidad')
                        ->label('¿Tiene discapacidad?')
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if ($state) {
                                $set('no_tiene_discapacidad', false);
                            }
                        }),
                    
                    Grid::make(['default' => 1, 'sm' => 2, 'lg' => 4])
                        ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                        ->schema([
                            Select::make('tipo_discapacidad')
                                ->label('Tipo de Discapacidad')
                                ->multiple()
                                ->options([
                                    'Física' => 'Física',
                                    'Psíquica' => 'Psíquica',
                                    'Sensorial' => 'Sensorial',
                                    'Intelectual' => 'Intelectual',
                                ])
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),
                            
                            TextInput::make('porcentaje_discapacidad')
                                ->label('Porcentaje de Discapacidad')
                                ->numeric()
                                ->suffix('%')
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),

                            DatePicker::make('fecha_reconocimiento')
                                ->label('Fecha reconocimiento')
                                ->displayFormat('d/m/Y')
                                ->native(false)
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),

                            DatePicker::make('fecha_resolucion_discapacidad')
                                ->label('Fecha resolución')
                                ->displayFormat('d/m/Y')
                                ->native(false)
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),
                        ]),

                    Grid::make(['default' => 1, 'sm' => 12])
                        ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                        ->schema([
                            Toggle::make('pertenece_andalucia')
                                ->label('¿Pertenece a Andalucía?')
                                ->default(true)
                                ->live()
                                ->columnSpan(['default' => 12, 'sm' => fn (Get $get) => ! $get('pertenece_andalucia') ? 5 : 12]),

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
                                ->visible(fn (Get $get) => ! $get('pertenece_andalucia'))
                                ->required(fn (Get $get) => ! $get('pertenece_andalucia'))
                                ->columnSpan(['default' => 12, 'sm' => 7]),
                        ]),

                    Grid::make(['default' => 1, 'sm' => 3])
                        ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                        ->schema([
                            FileUpload::make('resolucion_discapacidad')
                                ->label('Resolución Discapacidad')
                                ->markAsRequired()
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad') && empty($get('resolucion_discapacidad')) && empty($get('dictamen_tecnico')) && empty($get('certificado_discapacidad')))
                                ->validationMessages([
                                    'required' => 'Debe adjuntar al menos uno de los tres archivos de discapacidad.',
                                ])
                                ->directory('empleados/resoluciones')
                                ->disk('local')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->openable()
                                ->downloadable()
                                ->previewable(false),

                            FileUpload::make('dictamen_tecnico')
                                ->label('Dictamen Técnico')
                                ->markAsRequired()
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad') && empty($get('resolucion_discapacidad')) && empty($get('dictamen_tecnico')) && empty($get('certificado_discapacidad')))
                                ->validationMessages([
                                    'required' => 'Debe adjuntar al menos uno de los tres archivos de discapacidad.',
                                ])
                                ->directory('empleados/resoluciones')
                                ->disk('local')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->openable()
                                ->downloadable()
                                ->previewable(false),

                            FileUpload::make('certificado_discapacidad')
                                ->label('Certificado Discapacidad')
                                ->markAsRequired()
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad') && empty($get('resolucion_discapacidad')) && empty($get('dictamen_tecnico')) && empty($get('certificado_discapacidad')))
                                ->validationMessages([
                                    'required' => 'Debe adjuntar al menos uno de los tres archivos de discapacidad.',
                                ])
                                ->directory('empleados/resoluciones')
                                ->disk('local')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->openable()
                                ->downloadable()
                                ->previewable(false),
                        ]),

                    Grid::make(['default' => 1, 'sm' => 12])
                        ->schema([
                            Toggle::make('tiene_incapacidad')
                                ->label('¿Tiene incapacidad?')
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if ($state) {
                                        $set('no_tiene_discapacidad', false);
                                    }
                                })
                                ->columnSpan(['default' => 12, 'sm' => fn (Get $get) => (bool) $get('tiene_incapacidad') ? 4 : 12]),

                            Select::make('tipo_incapacidad')
                                ->label('Tipo de Incapacidad')
                                ->multiple()
                                ->options([
                                    'Físico' => 'Físico',
                                    'Psíquico' => 'Psíquico',
                                ])
                                ->visible(fn (Get $get) => (bool) $get('tiene_incapacidad'))
                                ->required(fn (Get $get) => (bool) $get('tiene_incapacidad'))
                                ->columnSpan(['default' => 12, 'sm' => 8]),
                        ]),

                    Repeater::make('incapacidad_archivos')
                        ->label(new \Illuminate\Support\HtmlString('Adjuntar Documentación de Incapacidad <span class="text-red-600 font-bold">*</span>'))
                        ->schema([
                            FileUpload::make('file_path')
                                ->label('Archivo')
                                ->directory('empleados/documentos')
                                ->disk('local')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->openable()
                                ->downloadable()
                                ->previewable(false)
                                ->required(),
                            TextInput::make('comentario')
                                ->label('Comentario / Descripción')
                                ->placeholder('Ej: Informe de resolución médica')
                                ->nullable(),
                        ])
                        ->columns(2)
                        ->compact()
                        ->reorderable(false)
                        ->addActionLabel('Añadir otro archivo')
                        ->visible(fn (Get $get) => (bool) $get('tiene_incapacidad'))
                        ->required(fn (Get $get) => (bool) $get('tiene_incapacidad'))
                        ->columnSpanFull(),

                    FileUpload::make('autorizacion_consulta')
                        ->label('Autorización de Consulta')
                        ->directory('empleados/autorizaciones')
                        ->disk('local')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->openable()
                        ->downloadable()
                        ->previewable(false)
                        ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad') || (bool) $get('tiene_incapacidad'))
                        ->columnSpanFull(),

                    Toggle::make('no_tiene_discapacidad')
                        ->label('No tiene discapacidad / incapacidad')
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if ($state) {
                                $set('tiene_discapacidad', false);
                                $set('tiene_incapacidad', false);
                            }
                        }),
                ])
                ->action(function ($record, array $data) {
                    $noTieneDiscapacidad = (bool) ($data['no_tiene_discapacidad'] ?? false);
                    $tieneDiscapacidad = $noTieneDiscapacidad ? false : (bool) ($data['tiene_discapacidad'] ?? false);
                    $tieneIncapacidad = $noTieneDiscapacidad ? false : (bool) ($data['tiene_incapacidad'] ?? false);

                    if ($tieneDiscapacidad) {
                        $hasRes = !empty($data['resolucion_discapacidad']);
                        $hasDict = !empty($data['dictamen_tecnico']);
                        $hasCert = !empty($data['certificado_discapacidad']);

                        if (!$hasRes && !$hasDict && !$hasCert) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'resolucion_discapacidad' => 'Debe adjuntar al menos uno de los tres archivos de discapacidad (Resolución, Dictamen técnico o Certificado).',
                            ]);
                        }
                    }

                    $record->update([
                        'tiene_discapacidad' => $tieneDiscapacidad,
                        'tipo_discapacidad' => $tieneDiscapacidad ? $data['tipo_discapacidad'] : null,
                        'porcentaje_discapacidad' => $tieneDiscapacidad ? $data['porcentaje_discapacidad'] : null,
                        'fecha_reconocimiento' => $tieneDiscapacidad ? $data['fecha_reconocimiento'] : null,
                        'fecha_resolucion_discapacidad' => $tieneDiscapacidad ? $data['fecha_resolucion_discapacidad'] : null,
                        'pertenece_andalucia' => $tieneDiscapacidad ? $data['pertenece_andalucia'] : true,
                        'comunidad_autonoma' => ($tieneDiscapacidad && !$data['pertenece_andalucia']) ? $data['comunidad_autonoma'] : null,
                        'tiene_incapacidad' => $tieneIncapacidad,
                        'tipo_incapacidad' => $tieneIncapacidad ? $data['tipo_incapacidad'] : null,
                        'no_tiene_discapacidad' => $noTieneDiscapacidad,
                    ]);
                    
                    // Save documents
                    if ($tieneDiscapacidad) {
                        if (!empty($data['resolucion_discapacidad'])) {
                            $record->documentos()->updateOrCreate(
                                ['tipo' => 'Resolución Discapacidad'],
                                [
                                    'nombre' => 'Resolución de Discapacidad ' . $record->nombre . ' ' . $record->apellidos,
                                    'file_path' => $data['resolucion_discapacidad'],
                                ]
                            );
                        } else {
                            $record->documentos()->where('tipo', 'Resolución Discapacidad')->delete();
                        }
                        if (!empty($data['dictamen_tecnico'])) {
                            $record->documentos()->updateOrCreate(
                                ['tipo' => 'Dictamen Técnico'],
                                [
                                    'nombre' => 'Dictamen Técnico Facultativo ' . $record->nombre . ' ' . $record->apellidos,
                                    'file_path' => $data['dictamen_tecnico'],
                                ]
                            );
                        } else {
                            $record->documentos()->where('tipo', 'Dictamen Técnico')->delete();
                        }
                        if (!empty($data['certificado_discapacidad'])) {
                            $record->documentos()->updateOrCreate(
                                ['tipo' => 'Certificado Discapacidad'],
                                [
                                    'nombre' => 'Certificado de Discapacidad ' . $record->nombre . ' ' . $record->apellidos,
                                    'file_path' => $data['certificado_discapacidad'],
                                ]
                            );
                        } else {
                            $record->documentos()->where('tipo', 'Certificado Discapacidad')->delete();
                        }
                    } else {
                        $record->documentos()->whereIn('tipo', ['Resolución Discapacidad', 'Dictamen Técnico', 'Certificado Discapacidad'])->delete();
                    }
                    
                    if ($tieneIncapacidad) {
                        $archivos = $data['incapacidad_archivos'] ?? [];
                        $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->delete();

                        foreach ($archivos as $item) {
                            if (!empty($item['file_path'])) {
                                $tipo = 'Incapacidad Física';
                                $tipoIncapacidad = $data['tipo_incapacidad'] ?? [];
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
                    } else {
                        $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->delete();
                    }

                    if ($tieneDiscapacidad || $tieneIncapacidad) {
                        if (!empty($data['autorizacion_consulta'])) {
                            $record->documentos()->updateOrCreate(
                                ['tipo' => 'Autorización de Consulta'],
                                [
                                    'nombre' => 'Autorización de Consulta ' . $record->nombre . ' ' . $record->apellidos,
                                    'file_path' => $data['autorizacion_consulta'],
                                ]
                            );
                        } else {
                            $record->documentos()->where('tipo', 'Autorización de Consulta')->delete();
                        }
                    } else {
                        $record->documentos()->where('tipo', 'Autorización de Consulta')->delete();
                    }

                    $record->actualizarAlertas();
                })
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
            \Filament\Actions\Action::make('ver_incapacidad')
                ->extraAttributes(['style' => 'display: none !important;'])
                ->modalHeading('Documentación de Incapacidad')
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
                }),
            \Filament\Actions\Action::make('ver_resolucion')
                ->extraAttributes(['style' => 'display: none !important;'])
                ->modalHeading('Resolución de Discapacidad')
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
                            <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) . "' class='underline text-amber-600 font-bold' target='_blank'>Descargar Resolución</a>
                        </div>
                    ");
                }),
            \Filament\Actions\Action::make('ver_dictamen')
                ->extraAttributes(['style' => 'display: none !important;'])
                ->modalHeading('Dictamen Técnico Facultativo')
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
                            <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) . "' class='underline text-amber-600 font-bold' target='_blank'>Descargar Dictamen</a>
                        </div>
                    ");
                }),
            \Filament\Actions\Action::make('ver_certificado')
                ->extraAttributes(['style' => 'display: none !important;'])
                ->modalHeading('Certificado de Discapacidad')
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
                            <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) . "' class='underline text-amber-600 font-bold' target='_blank'>Descargar Certificado</a>
                        </div>
                    ");
                }),
            \Filament\Actions\Action::make('ver_autorizacion_consulta')
                ->extraAttributes(['style' => 'display: none !important;'])
                ->modalHeading('Autorización de Consulta')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalWidth('7xl')
                ->modalContent(function ($record) {
                    $doc = $record->documentos()->where('tipo', 'Autorización de Consulta')->first();
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
                            <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) . "' class='underline text-amber-600 font-bold' target='_blank'>Descargar Autorización</a>
                        </div>
                    ");
                }),
            EditAction::make()
                ->label('Modificar datos')
                ->color('info'),
        ];
    }
}
