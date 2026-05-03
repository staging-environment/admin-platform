<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\CheckboxList;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required(),

                CheckboxList::make('roles')
                    ->relationship('roles', 'name')
                    ->columns(2),
            ]);
    }
}
