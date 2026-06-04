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
        return auth()->user()->can('gestion_documentacion_empleados');
    }

    protected function canCreate(): bool
    {
        return auth()->user()->can('gestion_documentacion_empleados');
    }

    protected function canEdit(Model $record): bool
    {
        return auth()->user()->can('gestion_documentacion_empleados');
    }

    protected function canDelete(Model $record): bool
    {
        return auth()->user()->can('gestion_documentacion_empleados');
    }

    public function isReadOnly(): bool
    {
        return false;
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
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => auth()->user()->can('gestion_documentacion_empleados')),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Previsualizar')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn ($record) => "Previsualización: {$record->nombre}")
                    ->modalSubmitAction(false)
                    ->modalContent(function ($record) {
                        $extension = strtolower(pathinfo($record->file_path, PATHINFO_EXTENSION));
                        $url = route('admin.recursos_humanos.ver_archivo', ['path' => $record->file_path]);
                        
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
                            return new HtmlString("
                                <div class='flex flex-col items-center space-y-4'>
                                    <div class='flex justify-center items-center p-2 bg-gray-50 border rounded-lg max-h-[60vh] overflow-y-auto w-full'>
                                        <img src='{$url}' alt='{$record->nombre}' class='max-w-full h-auto rounded shadow-sm' />
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
                                    <div class='w-full h-[600px] border rounded-lg overflow-hidden'>
                                        <iframe src='{$url}' class='w-full h-full' style='border: none;'></iframe>
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
                    ->color('success')
                    ->url(fn ($record) => route('admin.recursos_humanos.descargar_archivo', ['path' => $record->file_path]), true),
                EditAction::make()
                    ->visible(fn () => auth()->user()->can('gestion_documentacion_empleados')),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()->can('gestion_documentacion_empleados')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
