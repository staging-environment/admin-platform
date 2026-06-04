<?php

namespace App\Filament\Resources\Empleados\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class ContratosRelationManager extends RelationManager
{
    protected static string $relationship = 'contratos';

    protected static ?string $title = 'Contratos Laborales';
    protected static ?string $modelLabel = 'Contrato';
    protected static ?string $pluralModelLabel = 'Contratos Laborales';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('gestion_contratos_empleados');
    }

    protected function canCreate(): bool
    {
        return auth()->user()->can('gestion_contratos_empleados');
    }

    protected function canEdit(Model $record): bool
    {
        return auth()->user()->can('gestion_contratos_empleados');
    }

    protected function canDelete(Model $record): bool
    {
        return auth()->user()->can('gestion_contratos_empleados');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tipo_contrato')
                    ->label('Tipo de Contrato')
                    ->placeholder('Indefinido, Temporal, Prácticas...')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required(),
                DatePicker::make('fecha_fin')
                    ->label('Fecha de Fin (Opcional)'),
                Select::make('jornada')
                    ->label('Jornada')
                    ->options([
                        'Completa' => 'Jornada Completa',
                        'Parcial' => 'Jornada Parcial',
                    ])
                    ->required(),
                TextInput::make('salario')
                    ->label('Salario Bruto Anual / Mensual')
                    ->numeric()
                    ->prefix('€')
                    ->required(),
                TextInput::make('centro_trabajo')
                    ->label('Centro de Trabajo')
                    ->placeholder('Oficina Principal, Delegación Sevilla...')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('file_path')
                    ->label('Archivo del Contrato (Opcional)')
                    ->directory('empleados/contratos')
                    ->disk('local')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo_contrato')
            ->columns([
                TextColumn::make('tipo_contrato')
                    ->label('Contrato')
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date(),
                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date()
                    ->default('Indefinido'),
                TextColumn::make('jornada')
                    ->label('Jornada')
                    ->badge(),
                TextColumn::make('salario')
                    ->label('Salario')
                    ->money('EUR'),
                TextColumn::make('centro_trabajo')
                    ->label('Centro de Trabajo'),
                \Filament\Tables\Columns\IconColumn::make('file_path')
                    ->label('Adjunto')
                    ->icon(fn (string $state): string => 'heroicon-o-document-text')
                    ->color(fn (string $state): string => 'success')
                    ->default(false)
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-x-mark'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Previsualizar')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalWidth('screen')
                    ->modalHeading(fn ($record) => "Previsualización: {$record->tipo_contrato}")
                    ->modalSubmitAction(false)
                    ->visible(fn ($record) => !empty($record->file_path))
                    ->modalContent(function ($record) {
                        $extension = strtolower(pathinfo($record->file_path, PATHINFO_EXTENSION));
                        $url = route('admin.recursos_humanos.ver_archivo', ['path' => $record->file_path]);
                        
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                            return new HtmlString("
                                <div class='flex flex-col items-center space-y-4'>
                                    <div class='flex justify-center items-center p-2 bg-gray-50 border rounded-lg overflow-auto w-full' style='height: 75vh; min-height: 600px;'>
                                        <img src='{$url}' alt='{$record->tipo_contrato}' class='object-contain w-full h-full rounded shadow-sm' style='max-height: 100%;' />
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
                                    <div class='p-4 bg-gray-100 rounded-full'>
                                        <svg class='w-12 h-12 text-gray-400' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'></path></svg>
                                    </div>
                                    <div class='space-y-1'>
                                        <p class='text-lg font-medium text-gray-900'>No se puede previsualizar este archivo</p>
                                        <p class='text-sm text-gray-500'>Descarga el archivo para verlo en tu dispositivo.</p>
                                    </div>
                                    <div class='pt-4'>
                                        <a href='" . route('admin.recursos_humanos.descargar_archivo', ['path' => $record->file_path]) . "' class='inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-md text-sm font-bold transition-all shadow-sm' target='_blank'>
                                            <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'></path></svg>
                                            Descargar Archivo
                                        </a>
                                    </div>
                                </div>
                            ");
                        }
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
