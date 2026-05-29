<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GasolineraContenido extends Model
{
    protected $connection = 'mariadb';
    protected $table = 'gasolinera_contenidos';

    protected $fillable = [
        'gasolinera_codigo',
        'slider_images',
        'imagen',
        'texto_inicio',
        'quienes_somos',
        'donde_estamos_texto',
        'contacto_email',
        'contacto_telefono',
        'horario',
        'servicios',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        'slider_images' => 'array',
        'servicios' => 'array',
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if (is_array($model->slider_images)) {
                $model->slider_images = array_values($model->slider_images);
            }
        });
    }

    public function gasolinera()
    {
        return $this->belongsTo(Gasolinera::class, 'gasolinera_codigo', 'Codigo');
    }
}
