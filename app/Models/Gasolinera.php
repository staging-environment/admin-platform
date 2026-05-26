<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasolinera extends Model
{
    protected $connection = 'virtusgesnet';
    protected $table = 'estaciones';
    protected $primaryKey = 'Codigo';
    public $timestamps = false;

    public function contenido()
    {
        return $this->hasOne(GasolineraContenido::class, 'gasolinera_codigo', 'Codigo');
    }

    public function mensajes()
    {
        return $this->hasMany(ContactoMensaje::class, 'gasolinera_codigo', 'Codigo');
    }
}
