<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoAusencia extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($model) {
            try {
                $empleado = $model->empleado;
                if ($empleado) {
                    $admins = \App\Models\User::all()->filter(function($user) {
                        return $user->can('recibir_notificaciones_recursos_humanos') || $user->email === 'jarodriguezbonilla@gmail.com';
                    });

                    foreach ($admins as $admin) {
                        \Illuminate\Support\Facades\Mail::to($admin->email)->send(new \App\Mail\NuevaSolicitudAdminMail(
                            "{$empleado->nombre} {$empleado->apellidos}",
                            $empleado->email,
                            'Bajas médicas',
                            \Carbon\Carbon::parse($model->fecha_inicio)->format('d/m/Y'),
                            $model->fecha_fin ? \Carbon\Carbon::parse($model->fecha_fin)->format('d/m/Y') : 'Abierta',
                            $model->comentario_empleado
                        ));
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error sending absence creation email to admins: " . $e->getMessage());
            }
        });

        static::updated(function ($model) {
            if ($model->wasChanged('estado') && in_array($model->estado, ['Aceptada', 'Rechazada'])) {
                try {
                    $empleado = $model->empleado;
                    if ($empleado && $empleado->email) {
                        \Illuminate\Support\Facades\Mail::to($empleado->email)->send(new \App\Mail\SolicitudEstadoMail(
                            $empleado->nombre,
                            'Bajas médicas',
                            \Carbon\Carbon::parse($model->fecha_inicio)->format('d/m/Y'),
                            $model->fecha_fin ? \Carbon\Carbon::parse($model->fecha_fin)->format('d/m/Y') : null,
                            $model->estado,
                            $model->comentario_aprobador
                        ));
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error sending absence state update email: " . $e->getMessage());
                }
            }
        });

        static::saved(function ($model) {
            $model->empleado?->actualizarAlertas();
        });

        static::deleted(function ($model) {
            $model->empleado?->actualizarAlertas();
        });
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
