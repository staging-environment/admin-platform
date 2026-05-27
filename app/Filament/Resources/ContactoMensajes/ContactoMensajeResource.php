<?php

namespace App\Filament\Resources\ContactoMensajes;

use App\Filament\Resources\ContactoMensajes\Pages\CreateContactoMensaje;
use App\Filament\Resources\ContactoMensajes\Pages\EditContactoMensaje;
use App\Filament\Resources\ContactoMensajes\Pages\ListContactoMensajes;
use App\Filament\Resources\ContactoMensajes\Schemas\ContactoMensajeForm;
use App\Filament\Resources\ContactoMensajes\Tables\ContactoMensajesTable;
use App\Models\ContactoMensaje;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContactoMensajeResource extends Resource
{
    protected static ?string $model = ContactoMensaje::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationLabel = 'Mensajes de Contacto';
    protected static ?string $pluralLabel = 'Mensajes de Contacto';
    protected static ?string $modelLabel = 'Mensaje';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        return $user->hasRole('Admin') || $user->can('ver_dashboard'); // Any admin/gestor can see it, adjust if needed
    }

    public static function form(Schema $schema): Schema
    {
        return ContactoMensajeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactoMensajesTable::configure($table);
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
            'index' => ListContactoMensajes::route('/'),
            'create' => CreateContactoMensaje::route('/create'),
            'view' => \App\Filament\Resources\ContactoMensajes\Pages\ViewContactoMensaje::route('/{record}'),
            'edit' => EditContactoMensaje::route('/{record}/edit'),
        ];
    }
}
