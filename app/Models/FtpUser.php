<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        'user',
        'password',
        'dir',
        'uid',
        'gid',
        'role',
        'can_upload',
        'can_download',
        'can_delete',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = 33;
            $model->gid = 33;

            if (!str_ends_with($model->dir, $model->user)) {
                $model->dir = rtrim($model->dir, '/') . '/' . $model->user;
            }

            // Sincronizamos homedir con la base por si el FTP la requiere
            $model->homedir = $model->dir;
        });

        // --- ESTO ES LO QUE FALTA: CREACIÓN FÍSICA ---
        static::created(function ($model) {
            $path = $model->dir;

            try {
                if (!file_exists($path)) {
                    // Creamos la carpeta con permisos 0775 (recursivo)
                    mkdir($path, 0775, true);

                    // Intentamos asegurar que el grupo sea www-data si el sistema lo permite
                    @chown($path, 'www-data');
                    @chgrp($path, 'www-data');
                }
            } catch (\Exception $e) {
                Log::error("No se pudo crear la carpeta FTP para {$model->user}: " . $e->getMessage());
            }
        });
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
    }
}
