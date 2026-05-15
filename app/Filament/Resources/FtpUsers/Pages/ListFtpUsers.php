<?php

namespace App\Filament\Resources\FtpUsers\Pages;

use App\Filament\Resources\FtpUsers\FtpUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFtpUsers extends ListRecords
{
    protected static string $resource = FtpUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
