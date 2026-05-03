<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),

                TextInput::make('email')
                    ->email()
                    ->required(),

                CheckboxList::make('roles')
                    ->relationship('roles', 'name')
                    ->columns(2),
            ]);
    }
}
