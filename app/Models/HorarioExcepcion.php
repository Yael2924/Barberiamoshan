<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioExcepcion extends Model
{
    protected $table = 'horarios_excepciones';
    protected $fillable = ['barbero_id', 'fecha', 'hora_inicio', 'hora_fin', 'disponible'];

    public function barbero()
    {
        return $this->belongsTo(Barbero::class, 'barbero_id');
    }
}
