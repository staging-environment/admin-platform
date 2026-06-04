<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $guarded = [];

    public function documentos()
    {
        return $this->hasMany(EmpleadoDocumento::class);
    }

    public function cursos()
    {
        return $this->hasMany(EmpleadoCurso::class);
    }

    public function notificaciones()
    {
        return $this->hasMany(EmpleadoNotificacion::class);
    }

    public function horarios()
    {
        return $this->hasMany(EmpleadoHorario::class);
    }

    public function ausencias()
    {
        return $this->hasMany(EmpleadoAusencia::class);
    }

    public function vacaciones()
    {
        return $this->hasMany(EmpleadoVacacion::class);
    }

    public function contratos()
    {
        return $this->hasMany(EmpleadoContrato::class);
    }

    public function comentarios()
    {
        return $this->hasMany(EmpleadoComentario::class);
    }
}
