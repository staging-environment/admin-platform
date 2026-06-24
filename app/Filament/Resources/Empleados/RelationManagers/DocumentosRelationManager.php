<?php

namespace App\Filament\Resources\Empleados\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class DocumentosRelationManager extends RelationManager
{
    protected static string $relationship = 'documentos';

    protected static ?string $title = 'Documentación';
    protected static ?string $modelLabel = 'Documento';
    protected static ?string $pluralModelLabel = 'Documentos';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('ver_documentacion_empleados');
    }

    protected function canCreate(): bool
    {
        return auth()->user()->can('editar_documentacion_empleados');
    }

    protected function canEdit(Model $record): bool
    {
        return auth()->user()->can('editar_documentacion_empleados');
    }

    protected function canDelete(Model $record): bool
    {
        return auth()->user()->can('editar_documentacion_empleados');
    }

    public function isReadOnly(): bool
    {
        return !auth()->user()->can('editar_documentacion_empleados');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->required()
                    ->maxLength(255),
                FileUpload::make('file_path')
                    ->label('Archivo')
                    ->directory('empleados/documentos')
                    ->disk('local')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->paginated(false)
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre del Documento')
                    ->wrap()
                    ->formatStateUsing(function ($state, $record) {
                        $downloadUrl = route('admin.recursos_humanos.descargar_archivo', ['path' => $record->file_path]);
                        $canEdit = auth()->user()->can('editar_documentacion_empleados');
                        
                        $editHtml = $canEdit 
                            ? "<button type=\"button\" wire:click=\"mountTableAction('edit', '{$record->id}')\" class=\"doc-btn doc-btn-edit\" title=\"Editar Documento\">
                                <svg class=\"w-4 h-4\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.83 20.082a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10\" />
                                </svg>
                               </button>" 
                            : "";
                        $deleteHtml = $canEdit 
                            ? "<button type=\"button\" wire:click=\"mountTableAction('delete', '{$record->id}')\" class=\"doc-btn doc-btn-delete\" title=\"Borrar Documento\">
                                <svg class=\"w-4 h-4\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m14.74 9-.34 9m-4.72 0-.34-9m9.02-3.25a29.071 29.071 0 0 0-7.811-.57l-1.557-1.91a1.5 1.5 0 0 0-1.154-.532H9.28a1.5 1.5 0 0 0-1.154.532l-1.557 1.91a29.07 29.07 0 0 0-7.812.57m13.886 0L19 19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L5.34 6.22m13.886 0H3\" />
                                </svg>
                               </button>" 
                            : "";

                        return new HtmlString("
                            <div class=\"doc-row\">
                                <span class=\"doc-title\" title=\"{$state}\">
                                    {$state}
                                </span>
                                <div class=\"doc-actions\">
                                    <button type=\"button\" wire:click=\"mountTableAction('preview', '{$record->id}')\" class=\"doc-btn doc-btn-preview\" title=\"Previsualizar\">
                                        <svg class=\"w-4 h-4\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\">
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z\" />
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z\" />
                                        </svg>
                                    </button>
                                    <a href=\"{$downloadUrl}\" target=\"_blank\" class=\"doc-btn doc-btn-download\" title=\"Descargar\">
                                        <svg class=\"w-4 h-4\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\">
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3\" />
                                        </svg>
                                    </a>
                                    {$editHtml}
                                    {$deleteHtml}
                                </div>
                            </div>
                        ");
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => auth()->user()->can('editar_documentacion_empleados')),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Previsualizar')
                    ->icon('heroicon-o-eye')
                    ->iconButton()
                    ->tooltip('Previsualizar')
                    ->color('info')
                    ->modalWidth('screen')
                    ->modalHeading(fn ($record) => "Previsualización: {$record->nombre}")
                    ->modalSubmitAction(false)
                    ->modalContent(function ($record) {
                        $extension = strtolower(pathinfo($record->file_path, PATHINFO_EXTENSION));
                        $url = route('admin.recursos_humanos.ver_archivo', ['path' => $record->file_path]);
                        
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                            return new HtmlString("
                                <div class='flex flex-col items-center space-y-4'>
                                    <div class='flex justify-center items-center p-2 bg-gray-50 border rounded-lg overflow-auto w-full' style='height: 75vh; min-height: 600px;'>
                                        <img src='{$url}' alt='{$record->nombre}' class='object-contain w-full h-full rounded shadow-sm' style='max-height: 100%;' />
                                    </div>
                                    <div class='flex justify-end w-full'>
                                        <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $record->file_path]) . "' class='inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-md text-xs font-bold transition-all shadow-sm' target='_blank'>
                                            Descargar Archivo
                                        </a>
                                    </div>
                                </div>
                            ");
                        } elseif ($extension === 'pdf') {
                            return new HtmlString("
                                <div class='flex flex-col items-center space-y-4'>
                                    <div class='w-full border rounded-lg overflow-hidden' style='height: 75vh; min-height: 600px;'>
                                        <iframe src='{$url}' class='w-full h-full' style='border: none; min-height: 600px;'></iframe>
                                    </div>
                                    <div class='flex justify-end w-full'>
                                        <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $record->file_path]) . "' class='inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-md text-xs font-bold transition-all shadow-sm' target='_blank'>
                                            Descargar Archivo
                                        </a>
                                    </div>
                                </div>
                            ");
                        } else {
                            return new HtmlString("
                                <div class='flex flex-col items-center justify-center p-8 bg-gray-50 border rounded-lg text-center space-y-4 w-full'>
                                    <div class='text-gray-400'>
                                        <svg class='w-16 h-16 mx-auto' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' />
                                        </svg>
                                    </div>
                                    <p class='text-sm text-gray-500 font-medium'>No se puede previsualizar este tipo de archivo (.{$extension}).</p>
                                    <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $record->file_path]) . "' class='inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-md text-xs font-bold transition-all shadow-sm' target='_blank'>
                                        Descargar para ver
                                    </a>
                                </div>
                            ");
                        }
                    }),
                Action::make('download')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->tooltip('Descargar')
                    ->color('success')
                    ->url(fn ($record) => route('admin.recursos_humanos.descargar_archivo', ['path' => $record->file_path]), true),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar')
                    ->visible(fn () => auth()->user()->can('editar_documentacion_empleados')),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Borrar')
                    ->visible(fn () => auth()->user()->can('editar_documentacion_empleados')),
            ]);
    }
}
