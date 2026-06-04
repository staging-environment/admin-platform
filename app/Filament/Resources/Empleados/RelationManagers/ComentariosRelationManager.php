<?php

namespace App\Filament\Resources\Empleados\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ComentariosRelationManager extends RelationManager
{
    protected static string $relationship = 'comentarios';

    protected static ?string $title = 'Comentarios sobre el empleado';
    protected static ?string $modelLabel = 'Comentario';
    protected static ?string $pluralModelLabel = 'Comentarios';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('gestion_comentarios_empleados');
    }

    protected function canCreate(): bool
    {
        return auth()->user()->can('gestion_comentarios_empleados');
    }

    protected function canEdit(Model $record): bool
    {
        return auth()->user()->can('gestion_comentarios_empleados');
    }

    protected function canDelete(Model $record): bool
    {
        return auth()->user()->can('gestion_comentarios_empleados');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Título / Asunto')
                    ->required()
                    ->maxLength(255),
                Textarea::make('comentario')
                    ->label('Comentario')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('comentario')
                    ->label('Comentario')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label('Autor')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => auth()->user()->can('gestion_comentarios_empleados'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => auth()->user()->can('gestion_comentarios_empleados')),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()->can('gestion_comentarios_empleados')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
