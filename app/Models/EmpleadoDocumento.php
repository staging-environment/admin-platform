<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoDocumento extends Model
{
    protected $guarded = [];

    protected $casts = [
        'fecha_inicio_contrato' => 'date',
        'fecha_vencimiento_contrato' => 'date',
        'fecha_realizacion' => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
