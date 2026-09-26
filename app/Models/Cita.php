<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $table = "citas";

    protected $fillable = [
        'nombre_cli',
        'telefono',
        'barbero_id',
        'fecha',
        'hora',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id')->withTrashed();
    }

    public function barbero()
    {
        return $this->belongsTo(Barbero::class, 'barbero_id')->withTrashed();
    }
}
