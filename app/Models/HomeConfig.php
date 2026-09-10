<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        'feria_utrera_inicio',
        'feria_utrera_fin',
        'feria_utrera_synced_at',
        'feria_utrera_source',
    ];

    protected $casts = [
        'slider_images' => 'array',
        'feria_utrera_inicio' => 'datetime',
        'feria_utrera_fin' => 'datetime',
        'feria_utrera_synced_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if (is_array($model->slider_images)) {
                $model->slider_images = array_values($model->slider_images);
            }
        });
    }

    /**
     * Determina si la Feria de Utrera está activa según las fechas almacenadas en base de datos.
     */
    public static function isFeriaUtreraActiva(?CarbonInterface $now = null): bool
    {
        try {
            $config = static::first();
            if ($config && $config->feria_utrera_inicio && $config->feria_utrera_fin) {
                $checkDate = $now ?? now();
                return $checkDate->between($config->feria_utrera_inicio, $config->feria_utrera_fin);
            }
        } catch (\Throwable $e) {
            Log::warning('Error consultando estado de Feria de Utrera en BD: ' . $e->getMessage());
        }

        return false;
    }
}
