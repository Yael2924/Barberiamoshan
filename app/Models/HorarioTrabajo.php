<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HorarioTrabajo extends Model
{

    use SoftDeletes;

    protected $table = 'horarios_trabajo';
    
    protected $fillable = [
        'dia',
        'hora'
    ];

    protected $dates = ['deleted_at'];
}
