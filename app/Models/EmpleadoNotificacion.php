<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoNotificacion extends Model
{
    protected $guarded = [];

    protected $casts = [
        'fecha_comunicacion' => 'date',
        'fecha_efecto' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_cierre' => 'date',
        'dias_suspension' => 'integer',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
