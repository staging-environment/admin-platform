<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoVacacion extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::updated(function ($model) {
            if ($model->wasChanged('estado') && in_array($model->estado, ['Aceptada', 'Rechazada'])) {
                try {
                    $empleado = $model->empleado;
                    if ($empleado && $empleado->email) {
                        \Illuminate\Support\Facades\Mail::to($empleado->email)->send(new \App\Mail\SolicitudEstadoMail(
                            $empleado->nombre,
                            $model->tipo,
                            \Carbon\Carbon::parse($model->fecha_inicio)->format('d/m/Y'),
                            $model->fecha_fin ? \Carbon\Carbon::parse($model->fecha_fin)->format('d/m/Y') : \Carbon\Carbon::parse($model->fecha_inicio)->format('d/m/Y'),
                            $model->estado
                        ));
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error sending vacation state update email: " . $e->getMessage());
                }
            }
        });
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
