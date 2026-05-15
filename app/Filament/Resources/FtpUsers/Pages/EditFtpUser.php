<?php

namespace App\Filament\Resources\FtpUsers\Pages;

use App\Filament\Resources\FtpUsers\FtpUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFtpUser extends EditRecord
{
    protected static string $resource = FtpUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
