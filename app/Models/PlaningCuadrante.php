<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaningCuadrante extends Model
{
    protected $table = 'planing_cuadrantes';

    protected $fillable = [
        'gasolinera_codigo',
        'anio',
        'mes',
        'estado',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'gasolinera_codigo' => 'integer',
        'anio' => 'integer',
        'mes' => 'integer',
    ];

    public function turnos(): HasMany
    {
        return $this->hasMany(PlaningTurno::class, 'planing_cuadrante_id');
    }

    public function gasolinera(): BelongsTo
    {
        return $this->belongsTo(Gasolinera::class, 'gasolinera_codigo', 'Codigo');
    }

    public function getNombreGasolineraAttribute(): string
    {
        return match ((int) $this->gasolinera_codigo) {
            1 => 'E.S. Vistalegre (Utrera)',
            2 => 'E.S. Ronda Norte (Utrera)',
            3 => 'E.S. Rodalabota (El Cuervo)',
            4 => 'E.S. Atenas (Lebrija)',
            default => 'Estación #' . $this->gasolinera_codigo,
        };
    }

    public function getNombreMesAttribute(): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return $meses[$this->mes] ?? 'Mes ' . $this->mes;
    }
}

