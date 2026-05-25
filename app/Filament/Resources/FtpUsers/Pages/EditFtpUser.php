<?php

namespace App\Filament\Resources\FtpUsers\Pages;

use App\Filament\Resources\FtpUsers\FtpUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Process;

class EditFtpUser extends EditRecord
{
    protected static string $resource = FtpUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Sincronización al editar (especialmente para la contraseña y permisos)
     */
    protected function afterSave(): void
    {
        // En producción y local, confiamos en que el modelo FtpUser maneja los permisos vía eventos.
        // Solo gestionamos la contraseña en local si DDEV está activo.

        if (app()->environment('local')) {
            $username = $this->record->user;

            if (isset($this->data['password']) && filled($this->data['password'])) {
                $password = $this->data['password'];

                // Cambiar contraseña en el contenedor de FTP
                $updatePassCommand = sprintf(
                    "ddev exec --user=root -s ftp bash -c 'printf \"%s\\n%s\\n\" %s | pure-pw passwd %s -f /etc/pure-ftpd/passwd/pureftpd.txt'",
                    $password,
                    $password,
                    escapeshellarg($password),
                    escapeshellarg($username)
                );
                Process::run($updatePassCommand);

                // Compilar DB
                Process::run("ddev exec --user=root -s ftp pure-pw mkdb /etc/pure-ftpd/db/pureftpd.pdb -f /etc/pure-ftpd/passwd/pureftpd.txt");
            }
        }
    }
}
