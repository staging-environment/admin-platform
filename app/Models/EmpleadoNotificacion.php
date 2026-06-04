<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoNotificacion extends Model
{
    protected $guarded = [];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
