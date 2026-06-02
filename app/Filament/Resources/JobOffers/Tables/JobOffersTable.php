<?php

namespace App\Filament\Resources\JobOffers\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use App\Models\JobOffer;

class JobOffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Inscritos')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                IconColumn::make('active')
                    ->label('Activa')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('view_applications')
                    ->label('Ver Inscritos')
                    ->icon('heroicon-o-users')
                    ->color('info')
                    ->url(fn (JobOffer $record): string => route('filament.admin.resources.job-applications.index', [
                        'tableFilters' => [
                            'jobOffer' => [
                                'value' => $record->id,
                            ],
                        ],
                    ])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}