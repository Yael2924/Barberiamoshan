<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServicioController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                //abort(404, 'No tienes permiso para acceder al módulo de servicios.');
                return redirect(route('error.page'))->with('error', 'No tienes permiso para acceder al módulo de servicios.');
            }
            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol != "Administrador") {
                //abort(404, 'Solo los administradores pueden realizar esta acción.');
                return redirect(route('error.page'))->with('error', 'Solo los administradores pueden realizar esta acción.');
            }
            return $next($request);
        })->only(['destroy', 'create', 'store', 'edit', 'update']);
        
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
    public function index(Request $request)
    {
        $busqueda = $request->busqueda;
        $lista = Servicio::where('nombre','like','%'.$busqueda.'%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('servicios.index')->with(compact('lista','busqueda'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('servicios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => [
                'required', 
                'min:5', 
                'max:50', 
                'regex:/[a-zA-ZÀ-ÿ]/', // Al menos una letra
                Rule::unique('servicios')->whereNull('deleted_at')
            ], // Excluir registros eliminados
            'descripcion' => [
                'required', 
                'min:15', 
                'max:100', 
                'regex:/[a-zA-ZÀ-ÿ]/'
            ], // Al menos una letra
            'duracion' => 'required|integer|min:10|max:180', // Duración en minutos
            'precio' => 'required|numeric|min:20',
            // 'disponibilidad' => 'required|boolean', // Asumiendo que sea true/false
        ], [
            'nombre.required' => 'Debes llenar el campo de nombre.',
            'nombre.unique' => 'El nombre del servicio ya existe.',
            'nombre.min' => 'El nombre del servicio debe contener mínimo 5 caracteres.',
            'nombre.max' => 'El nombre del servicio no debe de exceder 50 caracteres.',
            'nombre.regex' => 'El nombre debe contener al menos una letra.',
            'descripcion.required' => 'Debes llenar el campo de descripción.',
            'descripcion.min' => 'La descripción debe contener mínimo 15 caracteres.',
            'descripcion.max' => 'La descripción no debe de exceder 100 caracteres.',
            'descripcion.regex' => 'La descripción debe contener al menos una letra.',
            'duracion.required' => 'Debes especificar la duración del servicio.',
            'duracion.integer' => 'La duración debe ser un número entero.',
            'duracion.min' => 'La duración debe ser al menos 10 minutos.',
            'duracion.max' => 'La duración no debe exceder 180 minutos.',
            'precio.required' => 'Debes especificar el precio del servicio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio debe ser mayor a $20.00 pesos.',
            // 'disponibilidad.required' => 'Debes especificar la disponibilidad del servicio.',
            // 'disponibilidad.boolean' => 'La disponibilidad debe ser un valor booleano (true o false).'
        ]);
        
        
        
        $servi = new Servicio; // Asume que el modelo se llama Servicio
        $servi->nombre = $request->nombre;
        $servi->descripcion = $request->descripcion;
        $servi->duracion = $request->duracion;
        $servi->precio = $request->precio;
        // $servi->disponibilidad = $request->disponibilidad;
        $servi->save();
        
        return redirect(route('servicios.index'))->with('success', 'Servicio creado correctamente.');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Servicio $servicio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $servi = Servicio::find($id);
        return view('servicios.edit')->with(compact('servi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => [
                'required', 
                'min:5', 
                'max:50', 
                'regex:/[a-zA-ZÀ-ÿ]/', // Al menos una letra
                Rule::unique('servicios')->ignore($id)->whereNull('deleted_at')
            ], // Excluir registros eliminados
            'descripcion' => [
                'required', 
                'min:15', 
                'max:100', 
                'regex:/[a-zA-ZÀ-ÿ]/'
            ], // Al menos una letra
            'duracion' => 'required|integer|min:10|max:180', // Duración en minutos
            'precio' => 'required|numeric|min:20',
            // 'disponibilidad' => 'required|boolean', // Asumiendo que sea true/false
        ], [
            'nombre.required' => 'Debes llenar el campo de nombre.',
            'nombre.unique' => 'El nombre del servicio ya existe.',
            'nombre.min' => 'El nombre del servicio debe contener mínimo 5 caracteres.',
            'nombre.max' => 'El nombre del servicio no debe de exceder 50 caracteres.',
            'nombre.regex' => 'El nombre debe contener al menos una letra.',
            'descripcion.required' => 'Debes llenar el campo de descripción.',
            'descripcion.min' => 'La descripción debe contener mínimo 15 caracteres.',
            'descripcion.max' => 'La descripción no debe de exceder 100 caracteres.',
            'descripcion.regex' => 'La descripción debe contener al menos una letra.',
            'duracion.required' => 'Debes especificar la duración del servicio.',
            'duracion.integer' => 'La duración debe ser un número entero.',
            'duracion.min' => 'La duración debe ser al menos 10 minutos.',
            'duracion.max' => 'La duración no debe exceder 180 minutos.',
            'precio.required' => 'Debes especificar el precio del servicio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio debe ser mayor a $20.00 pesos.',
            // 'disponibilidad.required' => 'Debes especificar la disponibilidad del servicio.',
            // 'disponibilidad.boolean' => 'La disponibilidad debe ser un valor booleano (true o false).'
        ]);
        
        
        
        $servi = Servicio::find($id); // Asume que el modelo se llama Servicio
        $servi->nombre = $request->nombre;
        $servi->descripcion = $request->descripcion;
        $servi->duracion = $request->duracion;
        $servi->precio = $request->precio;
        // $servi->disponibilidad = $request->disponibilidad;
        $servi->save();
        
        return redirect(route('servicios.index'))->with('success', 'Servicio actualizado correctamente.');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $servi = Servicio::find($id);
        $servi->delete();

        return redirect(route('servicios.index'));
    }

    public function exportarPDF(Request $request)
    {
        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;

        $busqueda = $request->input('busqueda', '');
        $lista = Servicio::where('nombre','like','%'.$busqueda.'%')->get();

        // $pdf = Pdf::loadView('servicios.pdf', compact('lista'));
        // return $pdf->download('servicios.pdf');
        $pdf = Pdf::loadView('servicios.pdf', compact('lista', 'usuario'))
                ->setPaper('a4', 'portrait');
        return $pdf->stream('servicios.pdf');
    }
}
