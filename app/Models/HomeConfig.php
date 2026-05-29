<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeConfig extends Model
{
    protected $connection = 'mariadb';
    protected $table = 'home_configs';

    protected $fillable = [
        'titulo',
        'subtitulo',
        'texto_inicio',
        'quienes_somos',
        'contacto_email',
        'contacto_telefono',
        'contacto_direccion',
        'latitud',
        'longitud',
        'slider_images',
        'condiciones_uso',
        'aviso_legal',
        'politica_privacidad',
    ];

    protected $casts = [
        'slider_images' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if (is_array($model->slider_images)) {
                $model->slider_images = array_values($model->slider_images);
            }
        });
    }
}
