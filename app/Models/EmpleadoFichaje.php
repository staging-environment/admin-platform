<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpleadoFichaje extends Model
{
    use SoftDeletes;

    protected $table = 'empleado_fichajes';

    protected $guarded = [];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'server_checkin_at' => 'datetime',
        'server_checkout_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
