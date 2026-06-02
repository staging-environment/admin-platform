<?php

namespace App\Filament\Resources\JobOffers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;

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

            RichEditor::make('description')
                ->label('Descripción del puesto')
                ->required(),

            Toggle::make('active')
                ->label('Oferta Activa / Publicada')
                ->default(true),
        ]);
    }
}