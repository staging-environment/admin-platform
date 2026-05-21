<?php

namespace App\Filament\Resources\FtpUsers;

use App\Filament\Resources\FtpUsers\Pages\CreateFtpUser;
use App\Filament\Resources\FtpUsers\Pages\EditFtpUser;
use App\Filament\Resources\FtpUsers\Pages\ListFtpUsers;
use App\Filament\Resources\FtpUsers\Schemas\FtpUserForm;
use App\Filament\Resources\FtpUsers\Tables\FtpUsersTable;
use App\Models\FtpUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FtpUserResource extends Resource
{
    protected static ?string $model = FtpUser::class;

    protected static ?string $navigationLabel = 'Usuarios FTP';

    protected static ?string $slug = 'ftp-management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function form(Schema $schema): Schema
    {
        return FtpUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FtpUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFtpUsers::route('/'),
            'create' => CreateFtpUser::route('/create'),
            'edit' => EditFtpUser::route('/{record}/edit'),
        ];
    }

    /**
     * Control de acceso temporal para asegurar la visibilidad en producción
     * Retorna true para descartar problemas de permisos durante la prueba.
     */
    public static function canViewAny(): bool
    {
        return true;
    }
}
