<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ClimaController extends Controller
{
    public function obtenerClima(Request $request)
    {
        $client = new Client();
        $apiKey = 'b20145d546a0e0195d7f39c3d6d01f3d'; // Reemplaza con tu API Key de OpenWeatherMap
        $ciudad = 'Ocosingo'; // Puedes pasar la ciudad desde el frontend o usar una por defecto.

        // Realizar la petición a la API
        $response = $client->get("http://api.openweathermap.org/data/2.5/weather?q={$ciudad}&appid={$apiKey}&lang=es&units=metric");

        // Obtener el cuerpo de la respuesta
        // $datosClima = json_decode($response->getBody(), true);

        if ($response != null){
            $datosClima = json_decode($response->getBody(), true);
        } else{
            return null;
        }

        // Retornar los datos al frontend
        return view('inicio', compact('datosClima'));
    }
}
