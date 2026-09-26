<?php

namespace App\Http\Controllers;

use App\Models\VentaServicio;
use App\Models\Servicio;
use App\Models\Barbero;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class VentaServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     // $lista = VentaServicio::all();
    //     // return view('ventas_servicios.index')->with(compact('lista'));


    //     // Obtener el ID del usuario autenticado
    //     $usuarioActualId = auth()->id();
    //     $id_barbero = Barbero::where('usuario_id', '=', $usuarioActualId)->id();
    //     $lista = VentaServicio::where('barbero_id', '=', $id_barbero)->get();
    //     return view('ventas_servicios.index')->with(compact('lista'));

    //     // $usuarios = Usuario::where('id', '!=', $usuarioActualId)->get();
    //     // return view('usuarios.index', compact('usuarios'));
    // }



    public function __construct()
{
    $this->middleware(function ($request, $next) {
        // Bloquear si es Administrador
        if (Auth::check() && Auth::user()->rol === "Administrador") {
            return redirect(route('error.page'))->with('error', 'No tienes permiso para acceder a la venta de servicios.');
        }

        // Bloquear si es Barbero o Administrador Y existe en la tabla barberos
        if (Auth::check() 
            && (Auth::user()->rol === 'Barbero' || Auth::user()->rol === 'Administrador') 
            && Barbero::where('usuario_id', Auth::user()->id)->exists()) {
            return redirect(route('error.page'))->with('error', 'No tienes permiso para acceder a la venta de servicios.');
        }

        return $next($request);
    })->except(['index', 'porcentaje', 'filtro', 'exportarPDF', 'generarRecibo', 'edit', 'update', 'create', 'store']);
    
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


    public function index(Request $request)
    {
        $user = Auth::user();
        $busqueda = $request->busqueda; // Obtener el valor de búsqueda

        if ($user->rol === 'Barbero') {
            $usuarioActualId = auth()->id();
            $barbero = Barbero::where('usuario_id', $usuarioActualId)->first();

            if (!$barbero) {
                return redirect()->route('home')->with('error', 'No se encontró un barbero asociado.');
            }

            // Filtrar solo por ID
            $lista = VentaServicio::with(['servicio' => function($query) {
                    $query->withTrashed(); // Esto es clave para detectar servicios eliminados
                }])
                ->where('barbero_id', $barbero->id)
                ->when($busqueda, function ($query, $busqueda) {
                    return $query->where('id', $busqueda); // Filtrar solo por ID exacto
                })
                ->orderBy('fecha_hora', 'desc')
                ->paginate(10);

            return view('ventas_servicios.index', compact('lista', 'busqueda'));
        }
        

        // Filtrar todas las ventas solo por ID
        $lista = VentaServicio::with(['servicio' => function($query) {
                $query->withTrashed(); // Esto es clave para detectar servicios eliminados
            }])
            ->when($busqueda, function ($query, $busqueda) {
                return $query->where('id', $busqueda); // Filtrar solo por ID exacto
            })
            ->orderBy('fecha_hora', 'desc')
            ->paginate(10);

        return view('ventas_servicios.index', compact('lista', 'busqueda'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $servicios = Servicio::all();
        // $barberos = Barbero::all();
        return view('ventas_servicios.create')->with(compact('servicios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $validated = $request->validate([
            'servicio_id' => 'required|exists:servicios,id', // El servicio debe existir en la base de datos
            // 'barbero_id' => 'required|exists:barberos,id', // El barbero debe existir en la base de datos
            'cantidad' => 'required|integer|min:1', // La cantidad debe ser un número entero mayor que 0
            'total' => 'required|numeric|min:0', // El total debe ser un número mayor o igual a 0
        ], [
            'servicio_id.required' => 'Debe seleccionar un servicio.',
            'servicio_id.exists' => 'El servicio seleccionado no es válido.',
            // 'barbero_id.required' => 'Debe seleccionar un barbero responsable.',
            // 'barbero_id.exists' => 'El barbero seleccionado no es válido.',
            'cantidad.required' => 'Debe ingresar la cantidad.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
            'total.required' => 'Debe calcular el total correctamente.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total no puede ser menor que 0.',
        ]);

        $user = Auth::user();

        if ($user->rol === 'Barbero' || $user->rol === 'Administrador') {
            // Obtener el ID del usuario autenticado
            $usuarioActualId = auth()->id();

            // Buscar el barbero asociado al usuario autenticado
            $barbero = Barbero::where('usuario_id', $usuarioActualId)->first();
        }

        // Crear la venta de servicio
        $ventaServicio = new VentaServicio();
        $ventaServicio->fecha_hora = now(); // Establece la fecha y hora actual
        $ventaServicio->servicio_id = $request->servicio_id;
        $ventaServicio->barbero_id = $barbero->id;
        $ventaServicio->cantidad = $request->cantidad;
        $ventaServicio->precio_unitario = $request->precio_unitario;
        $ventaServicio->total = $request->total;
        $ventaServicio->save();

        return redirect(route('ventas_servicios.index'));
    }

    // public function reporte(Request $request)
    // {
    //     $barberos = Barbero::all();
    //     $ventas = [];
    //     $totalVentas = 0;
    //     $ganancia = 0;

    //     if ($request->has(['fecha_inicio', 'fecha_fin', 'porcentaje'])) {
    //         $query = VentaServicio::whereBetween('fecha_hora', [$request->fecha_inicio, $request->fecha_fin]);

    //         if ($request->barbero_id) {
    //             $query->where('barbero_id', $request->barbero_id);
    //         }

    //         $ventas = $query->get();
    //         $totalVentas = $ventas->sum('total');
    //         $ganancia = ($totalVentas * $request->porcentaje) / 100;
    //     }

    //     return view('ventas_servicios.reporte', compact('barberos', 'ventas', 'totalVentas', 'ganancia'));
    // }

    public function porcentaje(Request $request)
    {
        // Si no se han enviado datos (por ejemplo, si solo se accede a la vista de reportes), no se ejecuta la validación aún.
        // if ($request->isMethod('get')) {
        //     // Mostrar la vista vacía inicialmente
        //     $barberos = Barbero::all();
        //     return view('ventas_servicios.porcentaje', compact('barberos'));
        // }
    
        // Si el formulario se envió, aplica la validación y carga los datos
        // $request->validate([
        //     'fecha_inicio' => 'required|date',
        //     'fecha_fin' => 'required|date',
        //     'barbero_id' => 'required|exists:barberos,id',
        //     'porcentaje' => 'required|numeric|min:0|max:100',
        // ]);
    
        // Si la validación pasa, filtra las ventas y calcula la ganancia
        // Obtener todos los barberos, incluyendo los eliminados con SoftDeletes
        $barberos = Barbero::withTrashed()->orderBy('nombre', 'asc')->get(); //ORDENAR POR NOMBRE ASCENDENTE
        $ventas = [];
        $totalVentas = 0;
        $ganancia = 0;

        // En tu controlador de reportes, obtenemos el porcentaje desde la configuración
        $porcentajeGanancia = Configuracion::first()->porcentaje_ganancia ?? 10; // Valor predeterminado: 10

    
        // Convertir fechas para incluir todo el rango del día
        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();
    
        // Filtrar ventas por rango de fechas
        $query = VentaServicio::whereBetween('fecha_hora', [$fecha_inicio, $fecha_fin]);

        // Filtrar por barbero de manera obligatoria
        $query->where('barbero_id', $request->barbero_id);

    
        // Obtener los datos
        $ventas = $query->orderBy('fecha_hora', 'desc') // Ordenar por fecha de la más reciente a la más antigua
        ->paginate(10);
        $totalVentas = $ventas->sum('total');
        $ganancia = ($totalVentas * $porcentajeGanancia) / 100;
    
        // Retornar vista con los datos
        return view('ventas_servicios.porcentaje', compact('barberos', 'ventas', 'totalVentas', 'ganancia', 'porcentajeGanancia'));
    }

    public function filtro(Request $request)
    {    
        // Obtener el usuario autenticado
        $usuarioActual = Auth::user();

        // Verificar si el usuario autenticado es un barbero
        $esBarbero = $usuarioActual->rol === 'Barbero';

        // Obtener todos los barberos, incluyendo los eliminados con SoftDeletes
        // Obtener barberos que tienen ventas asociadas
        $barberos = Barbero::whereHas('venta_servicio', function($query) {
            $query->whereNotNull('fecha_hora');
        })->withTrashed()->orderBy('nombre', 'asc')->get();

        // Obtener servicios que tienen ventas asociadas
        $servicios = Servicio::whereHas('venta_servicio', function($query) {
            $query->whereNotNull('fecha_hora');
        })->withTrashed()->orderBy('nombre', 'asc')->get();

        $ventas = [];
        $totalVentas = 0;

        // Convertir fechas para incluir todo el rango del día
        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();

        // Iniciar la consulta con el filtro de fechas
        $query = VentaServicio::whereBetween('fecha_hora', [$fecha_inicio, $fecha_fin]);

        
        // Si el usuario es un barbero, filtrar solo sus ventas
        if ($esBarbero) {
            $barbero = Barbero::where('usuario_id', $usuarioActual->id)->first();

            if ($barbero) {
                // Filtrar las ventas por el ID del barbero
                $query->where('barbero_id', $barbero->id);
            } else {
                // Si no se encuentra el barbero, no mostrar ventas
                $query->where('barbero_id', -1); // Filtro que no devolverá resultados
            }
        } else {
            // Aplicar filtro por barbero (si está presente y no es un barbero)
            if ($request->filled('barbero_id')) {
                $query->where('barbero_id', $request->barbero_id);
            }

            // Aplicar filtro por estado del barbero (activo/inactivo)
            if ($request->filled('estado_barbero')) {
                if ($request->estado_barbero === 'activo') {
                    $query->whereHas('barbero', function ($q) {
                        $q->whereNull('deleted_at'); // Filtrar solo barberos activos
                    });
                } elseif ($request->estado_barbero === 'inactivo') {
                    $query->whereHas('barbero', function ($q) {
                        $q->whereNotNull('deleted_at'); // Filtrar solo barberos eliminados
                    });
                }
            }
        }

        // Aplicar filtro por servicio (si está presente)
        if ($request->filled('servicio_id')) {
            $query->where('servicio_id', $request->servicio_id);
        }

        // Aplicar filtro por barbero (si está presente)
        /*if ($request->filled('barbero_id')) {
            $query->where('barbero_id', $request->barbero_id);
        }

        // Aplicar filtro por estado del barbero (activo/inactivo)
        if ($request->filled('estado_barbero')) {
            if ($request->estado_barbero === 'activo') {
                $query->whereHas('barbero', function ($q) {
                    $q->whereNull('deleted_at'); // Filtrar solo barberos activos
                });
            } elseif ($request->estado_barbero === 'inactivo') {
                $query->whereHas('barbero', function ($q) {
                    $q->whereNotNull('deleted_at'); // Filtrar solo barberos eliminados
                });
            }
        }*/

        // Aplicar filtro por rango de precio unitario
        if ($request->filled('precio_min')) {
            $query->where('precio_unitario', '>=', $request->precio_min);
        }
        if ($request->filled('precio_max')) {
            $query->where('precio_unitario', '<=', $request->precio_max);
        }

        // Aplicar filtro por cantidad vendida
        if ($request->filled('cantidad_min')) {
            $query->where('cantidad', '>=', $request->cantidad_min);
        }
        if ($request->filled('cantidad_max')) {
            $query->where('cantidad', '<=', $request->cantidad_max);
        }

        // Aplicar filtro por total de venta
        if ($request->filled('total_min')) {
            $query->where('total', '>=', $request->total_min);
        }
        if ($request->filled('total_max')) {
            $query->where('total', '<=', $request->total_max);
        }

        $totalVentas = $query->sum('total');
        
        // Obtener los datos filtrados
        $ventas = $query->orderBy('fecha_hora', 'desc') // Ordenar por fecha de la más reciente a la más antigua
                    ->paginate(10); // Paginar cada 10 elementos


        // Retornar la vista con los datos filtrados
        return view('ventas_servicios.filtro', compact('esBarbero','barberos', 'servicios', 'ventas', 'totalVentas', 'usuarioActual'));
    }

    
    public function exportarPDF(Request $request)
    {
        // Validar que las fechas están presentes
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
        ]);

        // Obtener el usuario autenticado
        $usuarioActual = Auth::user();

        // Verificar si el usuario autenticado es un barbero
        $esBarbero = $usuarioActual->rol === 'Barbero';

    
        // Convertir las fechas al formato correcto
        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();
        
        // Inicializar la consulta para las ventas
        $ventasQuery = VentaServicio::whereBetween('fecha_hora', [$fecha_inicio, $fecha_fin]);
    

        // Si el usuario es un barbero, filtrar solo sus ventas
        if ($esBarbero) {
            $barbero = Barbero::where('usuario_id', $usuarioActual->id)->first();

            if ($barbero) {
                // Filtrar las ventas por el ID del barbero
                $ventasQuery->where('barbero_id', $barbero->id);
            } else {
                // Si no se encuentra el barbero, no mostrar ventas
                $ventasQuery->where('barbero_id', -1); // Filtro que no devolverá resultados
            }
        } else {
            // Aplicar filtro por barbero (si está presente y no es un barbero)
            if ($request->filled('barbero_id')) {
                $ventasQuery->where('barbero_id', $request->barbero_id);
            }
        }


        // Aplicar otros filtros si existen
        if ($request->filled('servicio_id')) {
            $ventasQuery->where('servicio_id', $request->servicio_id);
        }
    
        /*if ($request->filled('barbero_id')) {
            $ventasQuery->where('barbero_id', $request->barbero_id);
        }*/
    
        if ($request->filled('precio_min')) {
            $ventasQuery->where('precio_unitario', '>=', $request->precio_min);
        }
    
        if ($request->filled('precio_max')) {
            $ventasQuery->where('precio_unitario', '<=', $request->precio_max);
        }
    
        if ($request->filled('cantidad_min')) {
            $ventasQuery->where('cantidad', '>=', $request->cantidad_min);
        }
    
        if ($request->filled('cantidad_max')) {
            $ventasQuery->where('cantidad', '<=', $request->cantidad_max);
        }
    
        if ($request->filled('total_min')) {
            $ventasQuery->where('total', '>=', $request->total_min);
        }
    
        if ($request->filled('total_max')) {
            $ventasQuery->where('total', '<=', $request->total_max);
        }
    
        // Obtener las ventas filtradas
        $ventas = $ventasQuery->get();
    
    
        // Calcular el total de ventas filtradas
        $totalVentas = $ventas->sum('total');
    
        // Obtener usuario autenticado
        //$usuario = Auth::user()->nombre;

        // Obtener usuario autenticado
        $usuario = $usuarioActual->nombre;
    
        // Cargar la vista para el PDF con solo los resultados filtrados
        $pdf = Pdf::loadView('ventas_servicios.pdf', compact('ventas', 'totalVentas', 'fecha_inicio', 'fecha_fin', 'usuario'))
                  ->setPaper('a4', 'portrait');
    
        return $pdf->stream('reporte_ventas_servicios.pdf');
    }
    
    public function generarRecibo($id)
    {
        $venta = VentaServicio::findOrFail($id);

        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;

        $pdf = Pdf::loadView('ventas_servicios.recibo_pdf', compact('venta', 'usuario'))
        ->setPaper('letter', 'portrait');

        return $pdf->stream("recibo_venta_{$venta->id}.pdf");
    }
   

    /**
     * Display the specified resource.
     */
    public function show(VentaServicio $ventaServicio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ventaServicio = VentaServicio::findOrFail($id);

        $servicios = Servicio::all(); // O cualquier consulta que necesites

        // Si el usuario es barbero, verificar que la venta sea suya
        if (auth()->user()->rol === 'Barbero') {
            $barbero = Barbero::where('usuario_id', auth()->id())->first();
            
            if (!$barbero || $ventaServicio->barbero_id != $barbero->id) {
                return redirect(route('error.page'))->with('error', 'No tienes permiso para editar esta venta de servicios.');
            }
        }

        // Validación si el servicio fue eliminado
        if ($ventaServicio->servicio && $ventaServicio->servicio->trashed()) {
            return redirect(route('error.page'))->with('error', 'El servicio relacionado fue eliminado.');
        }

        return view('ventas_servicios.edit', compact('ventaServicio', 'servicios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'cantidad' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
        ], [
            'servicio_id.required' => 'Debe seleccionar un servicio.',
            'servicio_id.exists' => 'El servicio seleccionado no es válido.',
            // 'barbero_id.required' => 'Debe seleccionar un barbero responsable.',
            // 'barbero_id.exists' => 'El barbero seleccionado no es válido.',
            'cantidad.required' => 'Debe ingresar la cantidad.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
            'total.required' => 'Debe calcular el total correctamente.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total no puede ser menor que 0.',
        ]);

        // Crear la venta de servicio
        $ventaServicio = VentaServicio::findOrFail($id);
        $ventaServicio->servicio_id = $request->servicio_id;
        $ventaServicio->cantidad = $request->cantidad;
        $ventaServicio->precio_unitario = $request->precio_unitario;
        $ventaServicio->total = $request->total;
        $ventaServicio->save();

        return redirect(route('ventas_servicios.index'))->with('success', 'Venta de Servicio actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VentaServicio $ventaServicio)
    {
        //
    }
}
