<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FtpUser extends Model
{
    use HasFactory;

    // Campos que se pueden rellenar de forma masiva
    protected $fillable = [
        'user',
        'password',
        'dir',
        'uid',
        'gid',
    ];
}
