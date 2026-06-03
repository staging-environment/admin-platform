<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;

class JobApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('job_offer_id')
                ->relationship('jobOffer', 'title')
                ->label('Oferta de Empleo')
                ->required(),

            Select::make('status')
                ->label('Estado')
                ->options([
                    'Nueva petición' => 'Nueva petición',
                    'En estudio' => 'En estudio',
                    'Aceptada' => 'Aceptada',
                    'Rechazada' => 'Rechazada',
                ])
                ->default('Nueva petición')
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

            TextInput::make('years_of_experience')
                ->label('Años de experiencia')
                ->maxLength(255),

            TextInput::make('incorporation_time')
                ->label('Tiempo de incorporación')
                ->maxLength(255),

            Toggle::make('travel_possibility')
                ->label('Posibilidad de viajar')
                ->default(false),

            Textarea::make('profile_description')
                ->label('Descripción / Mensaje')
                ->rows(3)
                ->columnSpanFull(),

            \Filament\Forms\Components\Placeholder::make('cv_download_link')
                ->label('Currículum Actual')
                ->content(function ($record) {
                    if (!$record || !$record->cv_path) {
                        return 'No hay currículum adjunto.';
                    }
                    return new \Illuminate\Support\HtmlString('<a href="' . route('admin.cv.download', $record->id) . '" target="_blank" style="color: #d97706; font-weight: bold; text-decoration: underline;">Descargar Currículum Actual (' . basename($record->cv_path) . ')</a>');
                }),

            FileUpload::make('cv_path')
                ->label('Reemplazar Currículum (PDF/DOC)')
                ->disk('private_cvs')
                ->visibility('private')
                ->required(),
        ]);
    }
}
