<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

class JobApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('job_offer_id')
                ->relationship('jobOffer', 'title')
                ->label('Oferta de Empleo')
                ->required(),

            TextInput::make('first_name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),

            TextInput::make('last_name')
                ->label('Apellidos')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Correo Electrónico')
                ->email()
                ->required()
                ->maxLength(255),

            TextInput::make('phone')
                ->label('Teléfono')
                ->required()
                ->maxLength(255),

            Textarea::make('profile_description')
                ->label('Descripción / Mensaje')
                ->rows(3)
                ->columnSpanFull(),

            FileUpload::make('cv_path')
                ->label('Currículum (PDF/DOC)')
                ->disk('private_cvs')
                ->visibility('private')
                ->required(),
        ]);
    }
}
