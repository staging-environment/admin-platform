<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoComentario extends Model
{
    protected $guarded = [];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
