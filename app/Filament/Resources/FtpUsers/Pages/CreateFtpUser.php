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
        // Capturamos los datos del cliente que se acaban de guardar en la BD
        $username = $this->record->username;
        $password = $this->record->password;
        $userHome = "/home/ftpusers/{$username}";

        // 1. Comando maestro con printf para saltarnos el prompt de contraseña en utrecar-ftp
        $createUserCommand = sprintf(
            "ddev exec --user=root -s ftp bash -c 'printf \"%s\\n%s\\n\" %s | pure-pw useradd %s -u ftpuser -d %s -f /etc/pure-ftpd/passwd/pureftpd.txt'",
            $password,
            $password,
            escapeshellarg($password),
            escapeshellarg($username),
            escapeshellarg($userHome)
        );
        Process::run($createUserCommand);

        // 2. Compilar la base de datos indexada (.pdb) que lee Pure-FTPd
        Process::run("ddev exec --user=root -s ftp pure-pw mkdb /etc/pure-ftpd/db/pureftpd.pdb -f /etc/pure-ftpd/passwd/pureftpd.txt");

        // 3. Crear el directorio físico independiente y asignar los permisos correctos
        Process::run("ddev exec --user=root -s ftp mkdir -p {$userHome}");
        Process::run("ddev exec --user=root -s ftp chown -R ftpuser:ftpgroup {$userHome}");
        Process::run("ddev exec --user=root -s ftp chmod 755 {$userHome}");
    }
}
