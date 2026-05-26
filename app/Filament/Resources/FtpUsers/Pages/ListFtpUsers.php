<?php

namespace App\Filament\Resources\FtpUsers\Pages;

use App\Filament\Resources\FtpUsers\FtpUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Process;

class ListFtpUsers extends ListRecords
{
    protected static string $resource = FtpUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Volver al Dashboard')
                ->color('gray')
                ->icon('heroicon-m-arrow-left')
                ->url('/dashboard'),
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Sincronización al eliminar masivamente o desde la lista
     */
    protected function afterDelete($record): void
    {
        $username = $record->user;
        $userHome = $record->dir;

        if (app()->environment('local')) {
            // Eliminar del archivo de passwords de Pure-FTPd
            Process::run("ddev exec --user=root -s ftp pure-pw userdel " . escapeshellarg($username) . " -f /etc/pure-ftpd/passwd/pureftpd.txt");

            // Compilar DB
            Process::run("ddev exec --user=root -s ftp pure-pw mkdb /etc/pure-ftpd/db/pureftpd.pdb -f /etc/pure-ftpd/passwd/pureftpd.txt");
        } else {
            // Sincronización en Producción (MySQL Compartido)
            // No hacemos nada; Pure-FTPd detecta el borrado en la BD.
        }
    }
}
