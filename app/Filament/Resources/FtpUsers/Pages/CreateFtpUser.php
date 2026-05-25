<?php

namespace App\Filament\Resources\FtpUsers\Pages;

use App\Filament\Resources\FtpUsers\FtpUserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Process;

class CreateFtpUser extends CreateRecord
{
    protected static string $resource = FtpUserResource::class;

    /**
     * Automatización con el contenedor de FTP independiente
     */
    protected function afterCreate(): void
    {
        // En producción y local, confiamos en el modelo FtpUser para la creación de directorios y permisos.
        // Solo manejamos Pure-FTPd en local vía DDEV.

        if (app()->environment('local')) {
            $username = $this->record->user;
            $password = $this->data['password'] ?? '';
            $userHome = $this->record->dir;

            $createUserCommand = sprintf(
                "ddev exec --user=root -s ftp bash -c 'printf \"%s\\n%s\\n\" %s | pure-pw useradd %s -u ftpuser -d %s -f /etc/pure-ftpd/passwd/pureftpd.txt'",
                $password,
                $password,
                escapeshellarg($password),
                escapeshellarg($username),
                escapeshellarg($userHome)
            );
            Process::run($createUserCommand);
            Process::run("ddev exec --user=root -s ftp pure-pw mkdb /etc/pure-ftpd/db/pureftpd.pdb -f /etc/pure-ftpd/passwd/pureftpd.txt");
        }
    }
}
