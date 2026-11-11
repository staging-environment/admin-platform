<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaningTurno extends Model
{
    protected $table = 'planing_turnos';

    protected $fillable = [
        'planing_cuadrante_id',
        'fecha',
        'puesto',
        'turno',
        'empleado_id',
        'empleado_titular_id',
        'es_sustitucion',
        'motivo_sustitucion',
        'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'es_sustitucion' => 'boolean',
    ];

    public function cuadrante(): BelongsTo
    {
        return $this->belongsTo(PlaningCuadrante::class, 'planing_cuadrante_id');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function empleadoTitular(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_titular_id');
    }

    public function getNombreTurnoAttribute(): string
    {
        return match ($this->turno) {
            'M' => 'Mañana',
            'T' => 'Tarde',
            'N' => 'Noche',
            default => $this->turno,
        };
    }

    /**
     * Devuelve el estilo y esquema de color asignado al TURNO
     */
    public function getEstiloColorAttribute(): ?array
    {
        if (!$this->empleado_id && !$this->empleado_titular_id) {
            return null;
        }

        $gasolineraCodigo = $this->cuadrante?->gasolinera_codigo;
        if (!$gasolineraCodigo) {
            $cuadrante = PlaningCuadrante::find($this->planing_cuadrante_id);
            $gasolineraCodigo = $cuadrante?->gasolinera_codigo ?? 1;
        }

        return app(\App\Services\PlaningTurnosService::class)->resolverColorTurno(
            $gasolineraCodigo,
            $this->puesto,
            $this->turno,
            $this->empleado_titular_id,
            $this->empleado_id
        );
    }

    public function getColorBgAttribute(): ?string
    {
        return $this->estilo_color['bg'] ?? null;
    }

    public function getColorTextAttribute(): ?string
    {
        return $this->estilo_color['text'] ?? null;
    }

    public function getColorBorderAttribute(): ?string
    {
        return $this->estilo_color['border'] ?? null;
    }
}

