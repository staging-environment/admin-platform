<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoHorario extends Model
{
    protected $guarded = [];

    protected $casts = [
        'dias_laborales' => 'array',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
