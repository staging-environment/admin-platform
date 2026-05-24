<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

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
            $model->uid = 33;
            $model->gid = 33;
            if (!str_ends_with($model->dir, $model->user)) {
                $model->dir = rtrim($model->dir, '/') . '/' . $model->user;
            }
            $model->homedir = $model->dir;
        });

        static::saved(function ($model) {
            $path = $model->dir;
            try {
                // 1. Crear la carpeta si no existe (PHP puro)
                if (!file_exists($path)) {
                    // La creamos con 775 para que nazca con vida
                    mkdir($path, 0775, true);
                    @chown($path, 33);
                    @chgrp($path, 33);
                }

                // 2. Aplicar el modo según tus checks de Laravel
                // Si NO puede subir ni borrar -> 0555 (Lectura y Ejecución, NO Escritura)
                // Si puede -> 0775 (Escritura para el dueño/grupo)
                $mode = ($model->can_upload || $model->can_delete) ? 0775 : 0555;

                // Bloqueo total si no puede descargar
                if (!$model->can_download) { $mode = 0000; }

                chmod($path, $mode);
                Log::info("FTP: Carpeta {$path} configurada con modo " . decoct($mode));

            } catch (\Exception $e) {
                Log::error("FTP Error: " . $e->getMessage());
            }
        });

        static::deleting(function ($model) {
            $path = $model->dir;
            if (!empty($path) && file_exists($path)) {
                File::deleteDirectory($path);
            }
        });
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
    }
}
