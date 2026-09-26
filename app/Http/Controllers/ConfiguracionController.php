<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ConfiguracionController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                //abort(404, 'No tienes permiso para acceder a configuración.');
                return redirect(route('error.page'))->with('error', 'No tienes permiso para acceder a configuración.');
            }
            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol != "Administrador") {
                //abort(404, 'Solo los administradores pueden realizar esta acción.');
                return redirect(route('error.page'))->with('error', 'Solo los administradores pueden realizar esta acción.');
            }
            return $next($request);
        })->only(['index', 'guardar']);
        
        $this->middleware(function ($request, $next) {
        if (Auth::check() && Auth::user()->rol == "Cliente") {
            Auth::logout(); // Cierra la sesión del usuario
            $request->session()->invalidate(); // Invalida la sesión
            $request->session()->regenerateToken(); // Regenera el token CSRF por seguridad
            
            return redirect(route('error.page'))->with('error', 'No tienes los permisos.');
        }
        return $next($request);
    });
    }
    // Método para mostrar la vista de configuración
    public function index()
    {
        $configuracion = Configuracion::first(); // Obtener la configuración existente
        return view('configuracion.index', compact('configuracion')); // Retorna la vista con los datos
    }

    // Método para guardar la nueva configuración de porcentaje de ganancia
    public function guardar(Request $request)
    {
        // Validar que el porcentaje esté entre 0 y 100
        $request->validate([
            'porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        // Obtener la configuración o crear una nueva si no existe
        $configuracion = Configuracion::first() ?? new Configuracion();
        $configuracion->porcentaje_ganancia = $request->input('porcentaje');
        $configuracion->save(); // Guardar el cambio

        // Redirigir con un mensaje de éxito
        return redirect()->route('configuracion.index')->with('success', 'Porcentaje de ganancia actualizado');
    }
}
