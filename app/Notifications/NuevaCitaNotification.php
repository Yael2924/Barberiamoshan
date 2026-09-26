<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;

class NuevaCitaNotification extends Notification
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
        $body = "Nueva cita con {$this->datos['cliente_nombre']} el {$this->datos['fecha']} a las {$this->datos['hora']}";

        return CloudMessage::withTarget('token', $notifiable->fcm_token)
            ->withData([
                'title' => 'Nueva cita agendada',
                'body' => $body,
                'user_id' => (string)$notifiable->id,
                'tipo' => 'nueva_cita',
                'cita_id' => $this->datos['cita_id'],
                'fecha' => $this->datos['fecha'],
                'hora' => $this->datos['hora'],
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]);
    }
}