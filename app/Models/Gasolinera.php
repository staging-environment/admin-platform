<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasolinera extends Model
{
    protected $connection = 'virtusgesnet';
    protected $table = 'estaciones';
    protected $primaryKey = 'Codigo';
    public $timestamps = false;
}
