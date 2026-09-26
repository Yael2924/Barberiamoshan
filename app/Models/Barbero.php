<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

//NOTIFICACIONES
use Illuminate\Notifications\Notifiable;

class Barbero extends Model
{
    use HasFactory;
    use SoftDeletes;

    //NOTIFICACIONES
    use Notifiable;

    // Devuelve el token FCM del barbero
    public function routeNotificationForFirebase()
    {
        return $this->fcm_token; // Asegúrate de que el campo fcm_token exista en la tabla de barberos
    }

    protected $table = "barberos";

    protected $fillable = [
        'usuario_id',
        'nombre',
        'telefono',
        'estado',
        'color',
        'fcm_token',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function venta_servicio()
    {
        return $this->hasMany(VentaServicio::class, 'barbero_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

}
