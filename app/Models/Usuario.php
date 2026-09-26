<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\User as Authenticatable;

//NOTIFICACIONES
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Authenticatable implements AuthenticatableContract
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = "usuarios"; 
    protected $primaryKey = "id"; 
    public $incrementing = true; 
    protected $keyType = "int"; 
    
    // Devuelve el token FCM del usuario
    public function routeNotificationForFirebase()
    {
        return $this->fcm_token; // Asegúrate de que el campo fcm_token exista en la tabla de usuarios
    }

    /**
     * Campos asignables en masa
     */
    protected $fillable = [
        'nombre',
        'nombre_usuario', 
        'email',          
        'password',
        'telefono',       
        'rol',
        'fcm_token',            
    ];

    /**
     * Campos que deben ocultarse al serializar el modelo
     */
    protected $hidden = [
        'password',      
        'remember_token', // Token "remember me"
        'api_token',     // Token de la API
    ];

    /**
     * Campos que deben ser mutados a tipos de datos específicos
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relación con la tabla barberos
     * Un usuario puede estar relacionado con varios barberos
     */
    public function barberos()
    {
        return $this->hasMany(Barbero::class, 'usuario_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class); // Relación de uno a muchos
    }
}