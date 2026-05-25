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
        'user', 'password', 'dir', 'uid', 'gid',
    ];

    protected $casts = [
        // Los permisos granulares ya no se manejan directamente en el modelo
    ];

    protected static function booted()
    {
        parent::booted();

        static::saving(function ($model) {
            // Forzamos UID 1000 (developer) y GID 33 (www-data) para coincidir con el comportamiento de Pure-FTPd
            $model->uid = 1000; // UID de 'developer'
            $model->gid = 33;   // GID de 'www-data'

            // Aseguramos que el directorio termine con el nombre de usuario
            if (!str_ends_with($model->dir, $model->user)) {
                $model->dir = rtrim($model->dir, '/') . '/' . $model->user;
            }
        });

        static::deleting(function ($model) {
            $path = $model->dir;
            // La eliminación del directorio también debería ser manejada por FtpPermissionsManager
            // o un servicio dedicado que use sudo de forma segura.
            // Por ahora, se mantiene la lógica de eliminación recursiva, pero sin sudo.
            if (!empty($path) && is_dir($path)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($files as $fileinfo) {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    @$todo($fileinfo->getRealPath()); // @ para suprimir errores si no hay permisos
                }

                @rmdir($path); // @ para suprimir errores si no hay permisos
            }
        });
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
    }
}
