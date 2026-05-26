<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoMensaje extends Model
{
    protected $connection = 'mariadb';
    protected $table = 'contacto_mensajes';

    protected $fillable = [
        'gasolinera_codigo',
        'nombre',
        'email',
        'mensaje',
    ];

    public function gasolinera()
    {
        return $this->belongsTo(Gasolinera::class, 'gasolinera_codigo', 'Codigo');
    }
}
