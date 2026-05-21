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
        $username = $this->record->user;
        $password = $this->data['password'] ?? '';
        $userHome = $this->record->dir;

        if (app()->environment('local')) {
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
            Process::run("ddev exec --user=root -s ftp mkdir -p " . escapeshellarg($userHome));
            Process::run("ddev exec --user=root -s ftp chown -R ftpuser:ftpgroup " . escapeshellarg($userHome));
            Process::run("ddev exec --user=root -s ftp chmod 755 " . escapeshellarg($userHome));
        } else {
            // En producción usamos la base de datos compartida entre Docker y la VM.
            // No necesitamos ejecutar comandos pure-pw porque el servidor FTP leerá la BD directamente.
            // Solo nos aseguramos de que el directorio físico exista en el HOST (VM).

            // Nota: Este comando de mkdir fallará si PHP no tiene permisos de sudo en la VM,
            // pero la autenticación en FileZilla debería funcionar si la BD está bien enlazada.
            Process::run("sudo mkdir -p " . escapeshellarg($userHome));
            Process::run("sudo chown 1000:1000 " . escapeshellarg($userHome));
        }
    }
}
