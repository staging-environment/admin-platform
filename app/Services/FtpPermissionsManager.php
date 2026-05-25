<?php

namespace App\Services;

use App\Models\FtpUser;
use Illuminate\Support\Facades\Log;

class FtpPermissionsManager
{
    public static function apply(FtpUser $user, string $role): bool
    {
        $path = $user->dir;

        // Sincronizamos con la lógica del modelo
        // Si no puede subir, forzamos lectura (555)
        $rawUpload = $user->getAttributes()['can_upload'] ?? 1;
        $canUpload = !($rawUpload === 0 || $rawUpload === "0" || $rawUpload === false);

        $rawDelete = $user->getAttributes()['can_delete'] ?? 1;
        $canDelete = !($rawDelete === 0 || $rawDelete === "0" || $rawDelete === false);

        $rawDownload = $user->getAttributes()['can_download'] ?? 1;
        $canDownload = !($rawDownload === 0 || $rawDownload === "0" || $rawDownload === false);

        if (!$canDownload) {
            $modeOctal = 0000;
        } elseif (!$canUpload) {
            $modeOctal = 0555;
        } else {
            $modeOctal = 02775;
        }

        // Si la carpeta no existe, intentamos crearla aquí también por si acaso

        // Comandos de sistema para DDEV / Producción
        // Usamos funciones nativas de PHP ya que 'developer' es dueño de /home/ftpusers
        try {
            if (!file_exists($path)) {
                @mkdir($path, 0775, true);
            }
            @chown($path, 'developer');
            @chgrp($path, 'www-data');
            chmod($path, $modeOctal);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
