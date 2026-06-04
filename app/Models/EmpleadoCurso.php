<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoCurso extends Model
{
    protected $guarded = [];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
