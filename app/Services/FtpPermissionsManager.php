<?php

namespace App\Services;

use App\Models\FtpUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; // Usamos el Facade File para comprobaciones de directorio

class FtpPermissionsManager
{
    /**
     * Aplica los permisos de sistema de archivos para un usuario FTP.
     * Asume que el directorio es creado por Pure-FTPd en la primera conexión.
     */
    public static function apply(FtpUser $user): bool // Eliminado el parámetro $role
    {
        $path = $user->dir;
        // Obtener la contraseña de sudo de forma segura (ej. desde .env)
        // ADVERTENCIA: Almacenar contraseñas en .env es un riesgo de seguridad.
        // Idealmente, el usuario 'developer' debería tener NOPASSWD configurado para chown/chmod específicos.
        $sudoPassword = env('SUDO_PASSWORD');

        if (empty($sudoPassword)) {
            Log::error("SUDO_PASSWORD no está configurado en .env. No se pueden aplicar permisos de sistema de archivos para el usuario FTP {$user->user}.");
            return false;
        }

        // Pure-FTPd crea el directorio en la primera conexión.
        // Si el directorio no existe, no podemos aplicar permisos aún.
        if (!File::exists($path)) {
            Log::warning("El directorio FTP '{$path}' no existe aún para el usuario '{$user->user}'. Los permisos se aplicarán después de la primera conexión FTP.");
            // Retornamos true porque la creación del usuario fue exitosa, solo que los permisos de FS no se pudieron aplicar aún.
            return true;
        }

        // Los valores booleanos ya están casteados en el modelo FtpUser
        $canUpload = $user->can_upload;
        $canDownload = $user->can_download;
        $canDelete = $user->can_delete; // Aunque no se usa directamente en modeOctal, es parte de los permisos granulares.

        // LÓGICA DE PERMISOS:
        // 0000: No hay permisos (ni descarga, ni subida, ni eliminación)
        // 0555: Solo lectura (descarga), no subida, no eliminación
        // 2775: Lectura, escritura, ejecución para propietario y grupo (subida, descarga, eliminación)
        //       El '2' es para setgid, que asegura que los nuevos archivos hereden el GID del directorio.
        $modeOctal = "0000"; // Por defecto, sin permisos

        if ($canDownload) {
            $modeOctal = "0555"; // Puede descargar
        }
        if ($canUpload) {
            $modeOctal = "2775"; // Puede subir, descargar, eliminar (acceso completo para propietario/grupo)
        }
        // La lógica actual prioriza canUpload para 2775. Si canUpload es false, pero canDelete es true,
        // la lógica actual no lo maneja explícitamente para un modo octal diferente a 0555.
        // Si se necesita un control más fino, se podría expandir la lógica de modeOctal.

        try {
            // Asegurar dueño y grupo usando sudo
            // UID 1000 (developer), GID 33 (www-data)
            $chownCmd = "echo '{$sudoPassword}' | sudo -S chown 1000:33 " . escapeshellarg($path);
            $chownOutput = shell_exec($chownCmd);
            if ($chownOutput !== null && str_contains($chownOutput, 'password')) {
                Log::error("Sudo password incorrecto o comando falló para chown en '{$path}': {$chownOutput}");
                return false;
            }

            // Aplicar chmod usando sudo
            $chmodCmd = "echo '{$sudoPassword}' | sudo -S chmod {$modeOctal} " . escapeshellarg($path);
            $chmodOutput = shell_exec($chmodCmd);
            if ($chmodOutput !== null && str_contains($chmodOutput, 'password')) {
                Log::error("Sudo password incorrecto o comando falló para chmod en '{$path}': {$chmodOutput}");
                return false;
            }

            clearstatcache(); // Limpiar la caché de estadísticas de archivos de PHP
            Log::info("Permisos '{$modeOctal}' aplicados a '{$path}' para el usuario FTP '{$user->user}'.");
            return true;
        } catch (\Exception $e) {
            Log::error("Error aplicando permisos de sistema de archivos para el usuario FTP {$user->user} en la ruta '{$path}': " . $e->getMessage());
            return false;
        }
    }
}
