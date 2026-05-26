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

    public function gasolinera()
    {
        return $this->belongsTo(Gasolinera::class, 'gasolinera_codigo', 'Codigo');
    }
}
