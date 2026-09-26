<?php

namespace App\Http\Controllers;

use App\Models\Barbero;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class BarberoController extends Controller
{

    public function __construct(){
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                //abort(404, 'No tienes permiso para acceder al módulo de barberos.');
                return redirect(route('error.page'))->with('success', 'No tienes permiso para acceder al módulo de barberos.');
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

        // Mapeo de colores
        $coloresGoogle = [
            '#039be5' => 'Turquesa',
            '#33b679' => 'Verde Salvia', 
            '#8e24aa' => 'Uva',
            '#e67c73' => 'Flamenco',
            '#f6bf26' => 'Banana',
            '#f4511e' => 'Mandarina',
            '#616161' => 'Grafito',
            '#3f51b5' => 'Índigo',
            '#0b8043' => 'Albahaca',
            '#d50000' => 'Tomate'
        ];

        // Determinar estado si la búsqueda es "activo" o "inactivo"
        $estado = null;
        if ($busqueda) {
            $busquedaLower = strtolower($busqueda);
            if ($busquedaLower === 'activo') {
                $estado = 1;
            } elseif ($busquedaLower === 'inactivo') {
                $estado = 0;
            }
        }

        // Consulta principal
        $query = Barbero::with('usuario')
            ->when($busqueda, function($query) use ($busqueda, $estado) {
                $query->where(function($q) use ($busqueda, $estado) {
                    $q->where('nombre', 'like', '%'.$busqueda.'%')
                    ->orWhere('telefono', 'like', '%'.$busqueda.'%');
                    
                    if ($estado !== null) {
                        $q->orWhere('estado', $estado);
                    }
                });
            })
            ->orderBy('created_at', 'desc');

        $lista = $query->paginate(10);

        return view('barberos.index', compact('lista', 'busqueda', 'coloresGoogle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usuarios = Usuario::where('rol', 'barbero')->get();
        return view('barberos.create', compact('usuarios'));

        // Obtener usuarios que no estén asociados a un barbero
        /*$usuarios = Usuario::whereDoesntHave('barbero') // Verifica que no exista relación con Barbero
        ->where('rol', 'barbero') // Solo usuarios con rol 'barbero'
        ->get();

        return view('barberos.create', compact('usuarios'));*/
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id|unique:barberos,usuario_id', // Validar usuario único
            'nombre' => [
                'required',
                'string',
                'min:10',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿÑñ\s]+$/'
            ],
            'telefono' => [
                'required',
                'digits:10' // Asegura exactamente 10 dígitos
            ],
            'estado' => 'required|string|in:activo,inactivo', // Solo permite estos valores
        ], [
            'usuario_id.required' => 'El campo usuario es obligatorio.',
            'usuario_id.exists' => 'El usuario especificado no existe.',
            'usuario_id.unique' => 'Este usuario ya está registrado como barbero.',
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto.',
            'nombre.min' => 'El nombre debe contener al menos 10 caracteres.',
            'nombre.max' => 'El nombre no debe exceder 50 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios, sin números ni caracteres especiales.',
            'telefono.required' => 'Debes proporcionar un número de teléfono.',
            'telefono.digits' => 'El teléfono debe contener exactamente 10 dígitos.',
            'estado.required' => 'Debes especificar el estado.',
            'estado.in' => 'El estado debe ser "activo" o "inactivo".'
        ]);

        $barbe = new Barbero; // Suponiendo que el modelo se llama Barbero
        $barbe->usuario_id = $request->usuario_id; // Relación con usuarios
        $barbe->nombre = $request->nombre;
        $barbe->telefono = $request->telefono;
        $barbe->estado = $request->estado === 'activo' ? 1 : 0;
        $barbe->save();

        return redirect(route('barberos.index'))->with('success', 'Barbero creado correctamente.');
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
    public function edit($id)
    {
        $barbe = Barbero::findOrFail($id);
        $usuarios = Usuario::where('rol', 'barbero')->get();

        // Mapeo de colores hexadecimales a IDs de Google Calendar
        $coloresGoogle = [
            '#039be5' => 'Turquesa',  // TURQUESA
            '#33b679' => 'Verde Salvia',  // VERDE SALVIA
            '#8e24aa' => 'Uva',  // UVA
            '#e67c73' => 'Flamenco',  // FLAMENCO
            '#f6bf26' => 'Banana',  // BANANA
            '#f4511e' => 'Mandarina',  // MANDARINA
            '#616161' => 'Grafito',  // GRAFITO
            '#3f51b5' => 'Índigo',  // ÍNDIGO
            '#0b8043' => 'Albahaca', // ALBAHACA
            '#d50000' => 'Tomate'  // TOMATE
        ];

        return view('barberos.edit')->with(compact('barbe', 'usuarios', 'coloresGoogle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id|unique:barberos,usuario_id,' . $id, // Ignorar el barbero actual
            'nombre' => [
                'required',
                'string',
                'min:10',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿÑñ\s]+$/'
            ],
            'telefono' => [
                'required',
                'digits:10' // Asegura exactamente 10 dígitos
            ],
            'estado' => 'required|string|in:activo,inactivo', // Solo permite estos valores
            'color' => 'required|string|in:#039be5,#33b679,#8e24aa,#e67c73,#f6bf26,#f4511e,#616161,#3f51b5,#0b8043,#d50000', // Solo permite estos colores
        ], [
            'usuario_id.required' => 'El campo usuario es obligatorio.',
            'usuario_id.exists' => 'El usuario especificado no existe.',
            'usuario_id.unique' => 'Este usuario ya está registrado como barbero.',

            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto.',
            'nombre.min' => 'El nombre debe contener al menos 10 caracteres.',
            'nombre.max' => 'El nombre no debe exceder 50 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios, sin números ni caracteres especiales.',

            'telefono.required' => 'Debes proporcionar un número de teléfono.',
            'telefono.digits' => 'El teléfono debe contener exactamente 10 dígitos.',

            'estado.required' => 'Debes especificar el estado.',
            'estado.in' => 'El estado debe ser "activo" o "inactivo".',

            'color.required' => 'El color es obligatorio.',
            'color.string' => 'El color debe ser una cadena de texto.',
            'color.in' => 'El color debe ser uno de los siguientes: #039be5, #33b679, #8e24aa, #e67c73, #f6bf26, #f4511e, #616161, #3f51b5, #0b8043, #d50000',
        ]);

        $barbe = Barbero::find($id);
        $barbe->usuario_id = $request->usuario_id;
        $barbe->nombre = $request->nombre;
        $barbe->telefono = $request->telefono;
        $barbe->estado = $request->estado === 'activo' ? 1 : 0;
        $barbe->color = $request->color; // Asignar el color seleccionado
        $barbe->save();

        return redirect(route('barberos.index'))->with('success', 'Barbero actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $barbe = Barbero::find($id);
        $usuario = Usuario::find($barbe->usuario_id);
        $barbe->delete();
        if ($usuario) {
            $usuario->delete();
        }

        return redirect(route('barberos.index'));
    }

    public function exportarPDF(Request $request)
    {
        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;

        $busqueda = $request->input('busqueda', '');

        $estado = null;
        if (strtolower($busqueda) === 'activo') {
            $estado = 1;
        } elseif (strtolower($busqueda) === 'inactivo') {
            $estado = 0;
        }

        $lista = Barbero::where('nombre','like','%'.$busqueda.'%')
        ->when($estado !== null, function ($query) use ($estado) {
            return $query->orWhere('estado', $estado);
        })->get();

        // $pdf = Pdf::loadView('barberos.pdf', compact('lista'));
        // return $pdf->download('barberos.pdf');
        $pdf = Pdf::loadView('barberos.pdf', compact('lista', 'usuario'))
                ->setPaper('a4', 'portrait');
        return $pdf->stream('barberos.pdf');
    }
}