<?php

namespace App\Filament\Resources\JobOffers;

use App\Filament\Resources\JobOffers\Pages\CreateJobOffer;
use App\Filament\Resources\JobOffers\Pages\EditJobOffer;
use App\Filament\Resources\JobOffers\Pages\ListJobOffers;
use App\Filament\Resources\JobOffers\RelationManagers\ApplicationsRelationManager;
use App\Filament\Resources\JobOffers\Schemas\JobOfferForm;
use App\Filament\Resources\JobOffers\Tables\JobOffersTable;
use App\Models\JobOffer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JobOfferResource extends Resource
{
    protected static ?string $model = JobOffer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        return $user->hasRole('Admin') || $user->can('gestion_ofertas');
    }

    public static function getNavigationLabel(): string
    {
        return 'Ofertas de Empleo';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Administración';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return JobOfferForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobOffersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ApplicationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobOffers::route('/'),
            'create' => CreateJobOffer::route('/create'),
            'edit' => EditJobOffer::route('/{record}/edit'),
        ];
    }
}