<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreciosProducto extends Model
{
    // Conexión a la base de datos externa
    protected $connection = 'virtusgesnet';

    // Tabla donde residen los PVP
    protected $table = 'preciosdeproductos';

    protected $primaryKey = 'ID';

    public $timestamps = false;
}
