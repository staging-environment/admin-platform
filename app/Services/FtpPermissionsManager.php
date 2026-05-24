<?php

namespace App\Services;

use App\Models\FtpUser;
use Illuminate\Support\Facades\Log;

class FtpPermissionsManager
{
    public static function apply(FtpUser $user, string $role): bool
    {
        $path = $user->dir;
        $groupName = "ftp_" . $user->user;
        $mode = ($role === 'editor') ? '2775' : '2755';

        // Comandos de sistema para DDEV
        $commands = [
            "groupadd {$groupName} 2>/dev/null || true",
            "chown -R www-data:{$groupName} " . escapeshellarg($path),
            "chmod -R {$mode} " . escapeshellarg($path)
        ];

        foreach ($commands as $command) {
            exec($command . ' 2>&1', $output, $returnCode);
            if ($returnCode !== 0) {
                Log::error("Error permisos: " . implode(" ", $output));
                return false;
            }
        }
        return true;
    }
}
