<?php

namespace App\Filament\Resources\Empleados\Pages;

use App\Filament\Resources\Empleados\EmpleadoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class EditEmpleado extends EditRecord
{
    protected static string $resource = EmpleadoResource::class;

    public function content(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent()
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'ficha-empleado-container']),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('dniDocuments')
                ->label('DNI')
                ->icon('heroicon-o-identification')
                ->color(function ($record) {
                    $hasDocs = $record->documentos()->where('tipo', 'DNI')->exists();
                    return $hasDocs ? 'warning' : 'danger';
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
                    $hasDocs = $record->documentos()->where('tipo', 'Contratos')->exists();
                    return $hasDocs ? 'warning' : 'danger';
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
                    $hasDocs = $record->documentos()->whereIn('tipo', ['Certificados', 'Titulaciones', 'Carnets', 'Otros', 'Prevención de riesgos laborales', 'Manipulación de alimentos'])->exists();
                    $hasCursos = $record->cursos()->exists();
                    return ($hasDocs || $hasCursos) ? 'warning' : 'danger';
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
                    $hasDocs = $record->documentos()->whereIn('tipo', ['Resolución Discapacidad', 'Dictamen Técnico', 'Certificado Discapacidad', 'Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->exists();
                    $hasOption = $record->tiene_discapacidad || $record->tiene_incapacidad || $record->no_tiene_discapacidad;
                    return ($hasOption || $hasDocs) ? 'warning' : 'danger';
                })
                ->modalHeading('Discapacidad / Incapacidad')
                ->fillForm(function ($record) {
                    $res = $record->documentos()->where('tipo', 'Resolución Discapacidad')->first();
                    $dict = $record->documentos()->where('tipo', 'Dictamen Técnico')->first();
                    $cert = $record->documentos()->where('tipo', 'Certificado Discapacidad')->first();
                    $incap = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->first();
                    
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
                        'incapacidad_file' => $incap?->file_path,
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
                    
                    Grid::make(4)
                        ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                        ->schema([
                            Select::make('tipo_discapacidad')
                                ->label('Tipo de Discapacidad')
                                ->multiple()
                                ->options([
                                    'Física' => 'Física',
                                    'Psíquica' => 'Psíquica',
                                    'Sensorial' => 'Sensorial',
                                ])
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),
                            
                            TextInput::make('porcentaje_discapacidad')
                                ->label('Porcentaje de Discapacidad')
                                ->numeric()
                                ->minValue(fn (Get $get) => (bool) $get('tiene_discapacidad') ? 33 : 0)
                                ->maxValue(100)
                                ->suffix('%')
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),

                            DatePicker::make('fecha_reconocimiento')
                                ->label('Fecha de reconocimiento')
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),

                            DatePicker::make('fecha_resolucion_discapacidad')
                                ->label('Fecha de resolución')
                                ->required(fn (Get $get) => (bool) $get('tiene_discapacidad')),
                        ]),

                    Grid::make(2)
                        ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                        ->schema([
                            Toggle::make('pertenece_andalucia')
                                ->label('¿Pertenece a Andalucía?')
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
                                ->visible(fn (Get $get) => ! $get('pertenece_andalucia'))
                                ->required(fn (Get $get) => ! $get('pertenece_andalucia')),
                        ]),

                    Grid::make(3)
                        ->visible(fn (Get $get) => (bool) $get('tiene_discapacidad'))
                        ->schema([
                            FileUpload::make('resolucion_discapacidad')
                                ->label(new \Illuminate\Support\HtmlString('Resolución de Discapacidad (Archivo) <span class="fi-fo-field-wrp-label-required-mark text-danger-600 dark:text-danger-400">*</span>'))
                                ->directory('empleados/resoluciones')
                                ->disk('local')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->previewable(false),

                            FileUpload::make('dictamen_tecnico')
                                ->label(new \Illuminate\Support\HtmlString('Dictamen técnico facultativo <span class="fi-fo-field-wrp-label-required-mark text-danger-600 dark:text-danger-400">*</span>'))
                                ->directory('empleados/resoluciones')
                                ->disk('local')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->previewable(false),

                            FileUpload::make('certificado_discapacidad')
                                ->label(new \Illuminate\Support\HtmlString('Certificado de discapacidad <span class="fi-fo-field-wrp-label-required-mark text-danger-600 dark:text-danger-400">*</span>'))
                                ->directory('empleados/resoluciones')
                                ->disk('local')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->previewable(false),
                        ]),

                    Toggle::make('tiene_incapacidad')
                        ->label('¿Tiene incapacidad?')
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if ($state) {
                                $set('no_tiene_discapacidad', false);
                            }
                        }),

                    Grid::make(2)
                        ->visible(fn (Get $get) => (bool) $get('tiene_incapacidad'))
                        ->schema([
                            Select::make('tipo_incapacidad')
                                ->label('Tipo de Incapacidad')
                                ->multiple()
                                ->options([
                                    'Físico' => 'Físico',
                                    'Psíquico' => 'Psíquico',
                                ])
                                ->required(fn (Get $get) => (bool) $get('tiene_incapacidad')),

                            FileUpload::make('incapacidad_file')
                                ->label('Adjuntar Documentación de Incapacidad')
                                ->directory('empleados/documentos')
                                ->disk('local')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->previewable(false)
                                ->required(fn (Get $get) => (bool) $get('tiene_incapacidad')),
                        ]),

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
                        if (!empty($data['incapacidad_file'])) {
                            $tipo = 'Incapacidad Física';
                            $tipoIncapacidad = $data['tipo_incapacidad'] ?? [];
                            if (is_array($tipoIncapacidad) && count($tipoIncapacidad) > 0) {
                                $first = $tipoIncapacidad[0];
                                $tipo = $first === 'Psíquico' ? 'Incapacidad Psíquica' : 'Incapacidad Física';
                            }
                            $record->documentos()->updateOrCreate(
                                ['tipo' => $tipo],
                                [
                                    'nombre' => 'Documentación Incapacidad ' . $record->nombre . ' ' . $record->apellidos,
                                    'file_path' => $data['incapacidad_file'],
                                ]
                            );
                        } else {
                            $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->delete();
                        }
                    } else {
                        $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->delete();
                    }
                })
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            \Filament\Actions\Action::make('darDeBaja')
                ->label('Baja de empleado')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->visible(fn ($record) => auth()->user()->can('dar_baja_empleado') && ($record->estado !== 'Baja'))
                ->modalHeading(fn ($record) => "Dar de baja a " . $record->nombre . " " . $record->apellidos)
                ->modalWidth('md')
                ->modalSubmitActionLabel('Confirmar Baja')
                ->form([
                    Select::make('motivo_baja')
                        ->label('Motivo de la baja')
                        ->options([
                            'Despido procedente' => 'Despido procedente',
                            'Despido disciplinario' => 'Despido disciplinario',
                            'Baja voluntaria' => 'Baja voluntaria',
                            'Finalización de contrato' => 'Finalización de contrato',
                            'Otros' => 'Otros',
                        ])
                        ->required()
                        ->live(),
                    TextInput::make('observaciones_baja')
                        ->label('Observaciones')
                        ->required(fn (Get $get) => $get('motivo_baja') === 'Otros')
                        ->visible(fn (Get $get) => $get('motivo_baja') === 'Otros')
                        ->maxLength(255),
                    DatePicker::make('fecha_baja')
                        ->label('Fecha de baja')
                        ->default(now())
                        ->required(),
                    FileUpload::make('documento_baja')
                        ->label('Documento de baja (Archivo)')
                        ->directory('empleados/bajas')
                        ->disk('local')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->previewable(false)
                        ->visible(fn (Get $get) => $get('motivo_baja') && $get('motivo_baja') !== 'Finalización de contrato')
                        ->required(fn (Get $get) => $get('motivo_baja') && $get('motivo_baja') !== 'Finalización de contrato'),
                ])
                ->action(function ($record, array $data) {
                    $docPath = $data['documento_baja'] ?? null;

                    $record->update([
                        'estado' => 'Baja',
                        'motivo_baja' => $data['motivo_baja'],
                        'observaciones_baja' => $data['motivo_baja'] === 'Otros' ? $data['observaciones_baja'] : null,
                        'fecha_baja' => $data['fecha_baja'],
                        'documento_baja_path' => $docPath,
                    ]);

                    if ($docPath) {
                        $record->documentos()->create([
                            'tipo' => 'Documento de Baja',
                            'nombre' => 'Documento de Baja ' . $record->nombre . ' ' . $record->apellidos,
                            'file_path' => $docPath,
                        ]);
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Empleado dado de baja correctamente')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                }),
            \Filament\Actions\Action::make('darDeAlta')
                ->label('Dar de alta')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->visible(fn ($record) => auth()->user()->can('dar_baja_empleado') && ($record->estado === 'Baja'))
                ->requiresConfirmation()
                ->modalHeading(fn ($record) => "Dar de alta a " . $record->nombre . " " . $record->apellidos)
                ->modalDescription('¿Estás seguro de que deseas dar de alta de nuevo a este empleado?')
                ->action(function ($record) {
                    $record->update([
                        'estado' => 'Alta',
                        'motivo_baja' => null,
                        'observaciones_baja' => null,
                        'fecha_baja' => null,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Empleado dado de alta correctamente')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                }),
            $this->getCancelFormAction(),
        ];
    }
}
