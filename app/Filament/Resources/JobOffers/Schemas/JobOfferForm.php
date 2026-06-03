<?php

namespace App\Filament\Resources\JobOffers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;

class JobOfferForm
{
    public static function configure(Schema $schema): Schema
    {
        // En Filament v5, usamos ->components() asegurando el retorno directo del mapeo
        return $schema->components([
            TextInput::make('title')
                ->label('Título de la oferta')
                ->required()
                ->maxLength(255),

            TinyEditor::make('description')
                ->label('Descripción del puesto')
                ->required(),

            Grid::make(2)
                ->schema([
                    TextInput::make('min_experience')
                        ->label('Experiencia mínima')
                        ->maxLength(255),

                    TextInput::make('salary_range')
                        ->label('Rango salarial')
                        ->maxLength(255),
                ]),

            Toggle::make('active')
                ->label('Oferta Activa / Publicada')
                ->default(true),
        ]);
    }
}