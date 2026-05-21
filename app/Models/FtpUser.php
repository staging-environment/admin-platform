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
        'user',
        'password',
        'dir',
        'uid',
        'gid',
    ];

    /**
     * Eventos del modelo: Aquí automatizamos los valores antes de guardar.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // 1. Forzamos siempre el UID/GID 33 (www-data)
            $model->uid = 33;
            $model->gid = 33;

            // 2. Si la ruta no trae el nombre del usuario, se lo concatenamos
            // Esto asegura que la ruta sea /home/ftpusers/nombre_usuario
            if (!str_ends_with($model->dir, $model->user)) {
                $model->dir = rtrim($model->dir, '/') . '/' . $model->user;
            }
        });
    }

    /**
     * Mutador: Si el FTP está en 'cleartext', quitamos el md5.
     * Si prefieres MD5, déjalo con md5($value).
     */
    public function setPasswordAttribute($value)
    {
        // De momento lo dejamos en texto plano para que coincida con el config del FTP
        $this->attributes['password'] = $value;
    }
}
