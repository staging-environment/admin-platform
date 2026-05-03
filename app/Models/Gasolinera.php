<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasolinera extends Model
{
    // Usamos la conexión remota configurada en el .env
    protected $connection = 'virtusgesnet';

    // Nombre exacto de la tabla según el SHOW TABLES
    protected $table = 'estaciones';

    // Clave primaria identificada en el DESCRIBE
    protected $primaryKey = 'Codigo';

    // Desactivamos timestamps ya que la tabla original no los usa
    public $timestamps = false;
}
