<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoDocumento extends Model
{
    protected $guarded = [];

    protected $casts = [
        'fecha_inicio_contrato' => 'date',
        'fecha_vencimiento_contrato' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'gasolineras_codigos' => 'array',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function getEstacionesAttribute()
    {
        $codigos = $this->gasolineras_codigos;
        if (empty($codigos) && $this->gasolinera_codigo) {
            $codigos = [$this->gasolinera_codigo];
        }
        if (empty($codigos)) {
            return collect();
        }
        try {
            return Gasolinera::whereIn('Codigo', $codigos)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public function getEstacionesNombresAttribute(): string
    {
        $codigos = $this->gasolineras_codigos;
        if (empty($codigos) && $this->gasolinera_codigo) {
            $codigos = [$this->gasolinera_codigo];
        }
        if (empty($codigos)) {
            return '—';
        }

        $map = [
            1 => 'E.S. VISTALEGRE',
            2 => 'RONDA NORTE',
            3 => 'E.S RODALABOTA',
            4 => 'E.S. ATENAS',
            5 => 'Trieste',
        ];

        try {
            $nombres = Gasolinera::whereIn('Codigo', $codigos)->pluck('Nombre')->filter()->toArray();
            if (!empty($nombres)) {
                return implode(', ', $nombres);
            }
        } catch (\Throwable $e) {
            // Fallback to static map
        }

        $nombres = array_filter(array_map(fn($c) => $map[(int)$c] ?? null, $codigos));
        return !empty($nombres) ? implode(', ', $nombres) : '—';
    }
}
