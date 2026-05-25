<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FtpUser extends Model
{
    use HasFactory;

    protected $connection = 'mariadb_ftp';
    protected $table = 'ftp_users';
    protected $primaryKey = 'user';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'user', 'password', 'dir', 'uid', 'gid', 'role', 'can_upload', 'can_download', 'can_delete',
    ];

    protected $casts = [
        'can_upload' => 'boolean',
        'can_download' => 'boolean',
        'can_delete' => 'boolean',
    ];

    protected static function booted()
    {
        parent::booted();

        static::saving(function ($model) {
            // Forzamos UID 1000 y GID 33 para coincidir con el host
            $model->uid = 1000;
            $model->gid = 33;

            if (!str_ends_with($model->dir, $model->user)) {
                $model->dir = rtrim($model->dir, '/') . '/' . $model->user;
            }
            $model->homedir = $model->dir;
        });

        static::saved(function ($model) {
            $path = $model->dir;
            $password = 'Sevillano15!';

            // 1. Asegurar que el directorio existe usando sudo
            if (!file_exists($path)) {
                $mkdirCmd = "echo '{$password}' | sudo -S mkdir -p " . escapeshellarg($path);
                shell_exec($mkdirCmd);
            }

            // 2. Asegurar dueño y grupo usando sudo
            $chownCmd = "echo '{$password}' | sudo -S chown developer:www-data " . escapeshellarg($path);
            shell_exec($chownCmd);

            // 3. Forzar detección manual de booleanos
            $rawUpload = $model->getAttributes()['can_upload'] ?? 1;
            $canUpload = !($rawUpload === 0 || $rawUpload === "0" || $rawUpload === false);
            $canDownload = $model->can_download ?? true;

            // 4. LÓGICA DE PERMISOS:
            if (!$canDownload) {
                $modeOctal = "0000";
            } elseif (!$canUpload) {
                $modeOctal = "0555";
            } else {
                $modeOctal = "2775";
            }

            // 5. Aplicar chmod usando sudo
            $chmodCmd = "echo '{$password}' | sudo -S chmod {$modeOctal} " . escapeshellarg($path);
            shell_exec($chmodCmd);

            clearstatcache();
        });

        static::deleting(function ($model) {
            $path = $model->dir;
            if (!empty($path) && is_dir($path)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($files as $fileinfo) {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    $todo($fileinfo->getRealPath());
                }

                rmdir($path);
            }
        });
    }

    public function setPasswordAttribute($value)
    {
        // Pure-FTPd puede configurarse para leer contraseñas en texto plano, MD5, SHA, etc.
        // Si el login falla, es posible que el servidor espere una encriptación específica.
        // Por ahora mantenemos el valor tal cual como venía funcionando.
        $this->attributes['password'] = $value;
    }
}
