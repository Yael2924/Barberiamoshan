<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VentaServicio extends Model
{
    //use HasFactory;
    use SoftDeletes;

    protected $table = "venta_servicio";

    // Relación con Barbero
    public function barbero()
    {
        return $this->belongsTo(Barbero::class, 'barbero_id')->withTrashed();
    }

    // Relación con Servicio
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id')->withTrashed();
    }
}
