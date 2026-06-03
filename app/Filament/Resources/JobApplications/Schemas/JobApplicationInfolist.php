<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

class JobApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Candidatura')
                    ->schema([
                        TextEntry::make('jobOffer.title')
                            ->label('Oferta de Empleo'),
                        TextEntry::make('status')
                            ->label('Estado'),
                        TextEntry::make('created_at')
                            ->label('Fecha de Inscripción')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('first_name')
                            ->label('Nombre'),
                        TextEntry::make('last_name')
                            ->label('Apellidos'),
                        TextEntry::make('email')
                            ->label('Correo Electrónico'),
                        TextEntry::make('phone')
                            ->label('Teléfono'),
                        TextEntry::make('years_of_experience')
                            ->label('Años de experiencia'),
                        TextEntry::make('incorporation_time')
                            ->label('Tiempo de incorporación'),
                        TextEntry::make('travel_possibility')
                            ->label('¿Posibilidad de viajar?')
                            ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                        TextEntry::make('cv_path')
                            ->label('Currículum Adjunto')
                            ->formatStateUsing(fn () => 'Descargar Currículum (PDF/DOC)')
                            ->url(fn (\App\Models\JobApplication $record) => route('admin.cv.download', $record->id))
                            ->openUrlInNewTab()
                            ->color('primary')
                            ->weight('bold'),
                        TextEntry::make('profile_description')
                            ->label('Mensaje / Carta de Presentación')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }
}
