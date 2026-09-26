<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Barbero;

class GoogleCalendarService
{
    protected $client;
    protected $calendarService;
    protected $calendarId;

    public function __construct()
    {
        try {
            $this->client = new Client();
            $this->client->setClientId(config('google.client_id'));
            $this->client->setClientSecret(config('google.client_secret'));
            $this->client->setRedirectUri(config('google.redirect_uri'));
            $this->client->setScopes([
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/calendar.events'
            ]);
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');
            
            $refreshToken = config('google.refresh_token');
            
            if (empty($refreshToken)) {
                throw new \Exception('Refresh token no configurado en .env');
            }
            
            $this->client->refreshToken($refreshToken);
            
            // Verificar si el token de acceso está expirado
            if ($this->client->isAccessTokenExpired()) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                $this->client->setAccessToken($newToken);
            }
            
            $this->calendarService = new Calendar($this->client);
            $this->calendarId = 'primary';
            
        } catch (\Exception $e) {
            Log::error('Error al inicializar Google Calendar: ' . $e->getMessage());
            throw $e; // Re-lanzar la excepción para manejo superior
        }
    }

    public function createEvent($cita)
    {
        $barbero = Barbero::find($cita->barbero_id);
        
        // Mapeo de colores hexadecimales a IDs de Google Calendar
        $coloresGoogle = [
            '#039be5' => 1,  // TURQUESA
            '#33b679' => 2,  // VERDE SALVIA
            '#8e24aa' => 3,  // UVA
            '#e67c73' => 4,  // FLAMENCO
            '#f6bf26' => 5,  // BANANA
            '#f4511e' => 6,  // MANDARINA
            // '#039be5' => 7,  // TURQUESA
            '#616161' => 8,  // GRAFITO
            '#3f51b5' => 9,  // ÍNDIGO
            '#0b8043' => 10, // ALBAHACA
            '#d50000' => 11  // TOMATE
        ];
        
        $colorId = $coloresGoogle[strtolower($barbero->color ?? '')] ?? null;
    
        $event = new Event([
            'summary' => 'Cita - ' . $cita->nombre_cli,
            'description' => "Barbero: ".($barbero ? $barbero->nombre : 'No asignado')."\n".
                            "Cliente: {$cita->nombre_cli}\n".
                            "Teléfono del Cliente: {$cita->telefono}\n".
                            "Hora: {$cita->hora}",
            'start' => new EventDateTime([
                'dateTime' => Carbon::parse($cita->fecha.' '.$cita->hora)->toIso8601String(),
                'timeZone' => 'America/Mexico_City',
            ]),
            'end' => new EventDateTime([
                'dateTime' => Carbon::parse($cita->fecha.' '.$cita->hora)->addHour()->toIso8601String(),
                'timeZone' => 'America/Mexico_City',
            ]),
            'colorId' => $colorId,
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60],
                    ['method' => 'popup', 'minutes' => 30],
                ],
            ],
        ]);
    
        try {
            $createdEvent = $this->calendarService->events->insert($this->calendarId, $event);
            return $createdEvent->getId();
        } catch (\Exception $e) {
            \Log::error('Error Google Calendar: '.$e->getMessage());
            return null;
        }
    }

    public function updateEvent($eventId, $cita)
    {
        try {
            $barbero = Barbero::find($cita->barbero_id);
            
            // Mapeo de colores (debería estar como propiedad de clase para reutilizar)
            $coloresGoogle = [
                '#039be5' => 1,  // TURQUESA
                '#33b679' => 2,  // VERDE SALVIA
                '#8e24aa' => 3,  // UVA
                '#e67c73' => 4,  // FLAMENCO
                '#f6bf26' => 5,  // BANANA
                '#f4511e' => 6,  // MANDARINA
                '#616161' => 8,  // GRAFITO
                '#3f51b5' => 9,  // ÍNDIGO
                '#0b8043' => 10, // ALBAHACA
                '#d50000' => 11  // TOMATE
            ];
            
            $colorId = $coloresGoogle[strtolower($barbero->color ?? '')] ?? null;

            // Obtener el evento existente
            $event = $this->calendarService->events->get($this->calendarId, $eventId);
            
            // Actualizar todos los campos
            $event->setSummary('Cita - ' . $cita->nombre_cli);
            $event->setDescription(
                'Barbero: ' . ($barbero ? $barbero->nombre : 'No asignado') .
                "\nCliente: " . $cita->nombre_cli . 
                "\nTeléfono: " . $cita->telefono . 
                "\nHora: " . $cita->hora
            );
            
            // Actualizar el color si está definido
            if ($colorId) {
                $event->setColorId($colorId);
            }
            
            $event->setStart(new EventDateTime([
                'dateTime' => Carbon::parse($cita->fecha . ' ' . $cita->hora)->toIso8601String(),
                'timeZone' => 'America/Mexico_City',
            ]));
            
            $event->setEnd(new EventDateTime([
                'dateTime' => Carbon::parse($cita->fecha . ' ' . $cita->hora)->addHour()->toIso8601String(),
                'timeZone' => 'America/Mexico_City',
            ]));

            // Guardar los cambios
            $this->calendarService->events->update($this->calendarId, $eventId, $event);
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Error al actualizar evento en Google Calendar: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteEvent($eventId)
    {
        try {
            $this->calendarService->events->delete($this->calendarId, $eventId);
            return true;
        } catch (\Exception $e) {
            \Log::error('Error al eliminar evento en Google Calendar: ' . $e->getMessage());
            return false;
        }
    }
}