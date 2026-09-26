<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;

class CitaReagendadaNotification extends Notification
{
    protected $datos;
    
    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function toFirebase($notifiable)
    {
        $body = "{$this->datos['barbero_nombre']} ha reagendado tu cita para el {$this->datos['fecha_nueva']} a las {$this->datos['hora_nueva']}";
        
        if (isset($this->datos['fecha_anterior']) && isset($this->datos['hora_anterior'])) {
            $body .= " (antes {$this->datos['fecha_anterior']} a las {$this->datos['hora_anterior']})";
        }

        return CloudMessage::withTarget('token', $notifiable->fcm_token)
            ->withData([
                'title' => 'Cita reagendada',
                'body' => $body,
                'user_id' => (string)$notifiable->id,
                'tipo' => 'cita_reagendada',
                'cita_id' => $this->datos['cita_id'],
                'fecha_nueva' => $this->datos['fecha_nueva'],
                'hora_nueva' => $this->datos['hora_nueva'],
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]);
    }
}