<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorarioTrabajo;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class HorarioController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                //abort(404, 'No tienes permiso para acceder a horarios.');
                return redirect(route('error.page'))->with('error', 'No tienes permiso para acceder a horarios.');
            }
            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol != "Administrador") {
                //abort(404, 'Solo los administradores pueden realizar esta acción.');
                return redirect(route('error.page'))->with('error', 'Solo los administradores pueden realizar esta acción.');
            }
            return $next($request);
        })->only(['index', 'store', 'delete', 'restore']);
        
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todas las horas de trabajo, incluyendo las eliminadas (soft delete)
        $horarios = HorarioTrabajo::withTrashed()->get();

        // Agrupar los horarios por día
        $horariosPorDia = $horarios->groupBy('dia');

        return view('horarios.index', compact('horariosPorDia'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'dia' => 'required|string|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'hora' => [
                'required',
                'date_format:H:i',
                // Validación personalizada para horas en punto
                function ($attribute, $value, $fail) {
                    $minutos = date('i', strtotime($value));
                    if ($minutos != '00') {
                        $fail('La hora debe ser en punto (por ejemplo, 8:00, 9:00, etc.).');
                    }
                },
                // Validación personalizada para evitar duplicados
                function ($attribute, $value, $fail) use ($request) {
                    $existeHorario = HorarioTrabajo::where('dia', $request->dia)
                        ->where('hora', $value)
                        ->exists();

                    if ($existeHorario) {
                        $fail('El horario para este día y hora ya está registrado.');
                    }
                },
            ],
        ]);

        // Crear un nuevo horario de trabajo
        HorarioTrabajo::create([
            'dia' => $request->dia,
            'hora' => $request->hora,
        ]);

        return redirect()->route('horarios.index')->with('success', 'Horario agregado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /*public function update(Request $request, string $id)
    {
        // Buscar el horario
        $horario = HorarioTrabajo::withTrashed()->findOrFail($id);

        // Si el checkbox está marcado, restaurar (habilitar) el horario
        if ($request->habilitado) {
            $horario->restore();
        }
        // Si el checkbox no está marcado, deshabilitar (soft delete) el horario
        else {
            $horario->delete();
        }

        return response()->json(['success' => true]);
    }*/

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function delete($id)
    {
        // Buscar el horario (incluso si está eliminado)
        $horario = HorarioTrabajo::withTrashed()->findOrFail($id);

        // Soft delete: Establecer deleted_at en la fecha actual
        $horario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Horario deshabilitado correctamente.',
        ]);
    }

    public function restore($id)
    {
        // Buscar el horario (incluso si está eliminado)
        $horario = HorarioTrabajo::withTrashed()->findOrFail($id);

        // Restaurar: Establecer deleted_at en NULL
        $horario->restore();

        return response()->json([
            'success' => true,
            'message' => 'Horario habilitado correctamente.',
        ]);
    }
}
