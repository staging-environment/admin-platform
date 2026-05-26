<?php

namespace App\Filament\Resources\Gasolineras;

use App\Filament\Resources\Gasolineras\Pages\EditGasolinera;
use App\Filament\Resources\Gasolineras\Pages\ListGasolineras;
use App\Filament\Resources\Gasolineras\Schemas\GasolineraForm;
use App\Filament\Resources\Gasolineras\Tables\GasolinerasTable;
use App\Models\Gasolinera;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GasolineraResource extends Resource
{
    protected static ?string $model = Gasolinera::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;



    protected static ?string $navigationLabel = 'Gasolineras';

    protected static ?string $pluralLabel = 'Gasolineras';

    protected static ?string $modelLabel = 'Gasolinera';

    public static function form(Schema $schema): Schema
    {
        return GasolineraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GasolinerasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MensajesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGasolineras::route('/'),
            'edit' => EditGasolinera::route('/{record}/edit'),
        ];
    }
}
