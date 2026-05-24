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
<<<<<<< HEAD
        'role', // Integrado para control de permisos
=======
        'can_upload',   // Añadido
        'can_download', // Añadido
        'can_delete',   // Añadido
>>>>>>> 7176c9fb85d7db25198c8aadf7141b83ba425255
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = 33;
            $model->gid = 33;
            // Valor por defecto si no se especifica
            $model->role = $model->role ?? 'viewer';

            if (!str_ends_with($model->dir, $model->user)) {
                $model->dir = rtrim($model->dir, '/') . '/' . $model->user;
            }
        });
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
    }
}
