<?php

namespace App\Http\Controllers;

use App\Models\VentaProducto;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB; 

class VentaProductoController extends Controller
{

    public function __construct(){
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                //abort(404, 'No tienes permiso para acceder a la venta de productos.');
                return redirect(route('error.page'))->with('error', 'No tienes permiso para acceder a la venta de productos.');
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
        $busqueda = $request->busqueda; // Ahora $request está definido correctamente

        // Filtrar por ID solo si se proporciona una búsqueda
        $lista = Venta::when($busqueda, function ($query, $busqueda) {
            return $query->where('id', $busqueda);
        })->orderBy('fecha_hora', 'desc')->paginate(10);

        return view('ventas_productos.index', compact('lista', 'busqueda'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productos = Producto::all();
        $ventas = Venta::all();
        return view('ventas_productos.create')->with(compact('productos','ventas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos enviados
        $request->validate([
            'productos' => 'required|array',
            'productos.*.cantidad' => 'required|integer|min:1',
            'total_general' => 'required|numeric|min:0',
        ], [
            'productos.required' => 'Producto no encontrado',
            'productos.min' => 'Debe agregar al menos un producto a la venta',
            'productos.*.cantidad.required' => 'La cantidad es requerida para todos los productos',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero',
            'productos.*.cantidad.min' => 'La cantidad mínima es 1',
            'total_general.required' => 'El total general es requerido',
            'total_general.numeric' => 'El total debe ser un valor numérico',
            'total_general.min' => 'El total mínimo debe ser mayor a 0',
        ]);
    
        // Crear la venta
        $venta = Venta::create([
            'total' => $request->input('total_general'),
            'fecha_hora' => now(),
        ]);
    
        // Agregar los productos a la venta
        foreach ($request->input('productos') as $productoId => $producto) {
            $productoModel = Producto::find($productoId);
    
            // Validar que el producto exista y que haya suficiente stock
            if ($productoModel && $producto['cantidad'] <= $productoModel->stock) {
                $subtotal = $producto['cantidad'] * $productoModel->precio; // Calcular subtotal
    
                // Agregar a la tabla pivote
                $venta->productos()->attach($productoId, [
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $productoModel->precio,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
    
                // Reducir el stock del producto
                $productoModel->decrement('stock', $producto['cantidad']);
            } else {
                return back()->withErrors(['error' => 'Stock insuficiente para el producto: ' . $productoModel->nombre]);
            }
        }
    
        return redirect()->route('ventas_productos.index')
            ->with('success', 'Venta registrada correctamente.');
    }
    
    public function detalles($id)
    {
        // Buscar la venta por ID con los productos asociados
        $venta = Venta::with('productos')->withTrashed()->find($id);

        if (!$venta) {
            return abort(404, 'Venta no encontrada');
        }
    
        // Calcular subtotales dinámicamente
        foreach ($venta->productos as $producto) {
            $producto->subtotal_calculado = $producto->pivot->cantidad * $producto->pivot->precio_unitario;
        }

        // Pasar los parámetros de filtro a la vista
        $filtros = request()->only([
            'fecha_inicio', 
            'fecha_fin', 
            'producto_id', 
            'total_minimo', 
            'total_maximo'
        ]);
    
        return view('ventas_productos.detalles', compact('venta', 'filtros'));
    }
    
    public function filtro(Request $request)
    {    
        $ventas = [];
        $totalVentas = 0;

        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();

        $query = Venta::whereBetween('fecha_hora', [$fecha_inicio, $fecha_fin]);

        // Filtro por producto
        if ($request->filled('producto_id')) {
            $query->whereHas('productos', function($q) use ($request) {
                $q->where('producto_id', $request->producto_id);
            });
        }

        // Filtro por total mínimo (>= 1)
        if ($request->filled('total_minimo') && $request->total_minimo >= 1) {
            $query->where('total', '>=', $request->total_minimo);
        }

        // Filtro por total máximo (solo si es mayor o igual al mínimo)
        if ($request->filled('total_maximo')) {
            // Si hay mínimo, validar que máximo sea >= mínimo
            if ($request->filled('total_minimo')) {
                if ($request->total_maximo >= $request->total_minimo) {
                    $query->where('total', '<=', $request->total_maximo);
                }
            } else {
                // Si no hay mínimo, aplicar máximo normalmente
                $query->where('total', '<=', $request->total_maximo);
            }
        }

        $totalVentas = $query->sum('total');
        $ventas = $query->orderBy('fecha_hora', 'desc')
                ->paginate(10);

        $productos = Producto::all();
        return view('ventas_productos.filtro', compact('ventas', 'totalVentas', 'productos'));
    }
    
    public function exportarPDF(Request $request)
    {

        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
        ]);


        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();
        
        $query = Venta::whereBetween('fecha_hora', [$fecha_inicio, $fecha_fin]);

        if ($request->has('producto_id') && $request->producto_id != '') {
            $query->whereHas('productos', function($q) use ($request) {
                $q->where('producto_id', $request->producto_id);
            });
        }

        if ($request->has('total_minimo') && $request->total_minimo >= 1) {
            $query->where('total', '>=', $request->total_minimo);
        }

        if ($request->has('total_maximo') && $request->total_maximo >= $request->total_minimo) {
            $query->where('total', '<=', $request->total_maximo);
        }

        $ventas = $query->get();
        $totalVentas = $ventas->sum('total');
        
        $usuario = Auth::user()->nombre;

        $pdf = Pdf::loadView('ventas_productos.pdf', compact('ventas', 'totalVentas', 'fecha_inicio', 'fecha_fin', 'usuario'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('reporte_ventas_productos.pdf');
    }
    
    public function exportarRecibo($id)
    {

        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;

        // Obtener la venta con sus productos
        $venta = Venta::with('productos')->withTrashed()->findOrFail($id);
    
        // Calcular subtotales dinámicamente
        foreach ($venta->productos as $producto) {
            $producto->subtotal_calculado = $producto->pivot->cantidad * $producto->pivot->precio_unitario;
        }
    
        // Generar el PDF
        $pdf = Pdf::loadView('ventas_productos.recibo_pdf', compact('venta', 'usuario'))
                    ->setPaper('a4', 'portrait');; // 'recibo' es el nombre de la vista del recibo
    
        // Retornar el PDF como descarga
        return $pdf->stream('recibo_venta_' . $venta->id . '.pdf');
    }
    

    /**
     * Display the specified resource.
     */
    public function show(VentaProducto $ventaProducto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $venta = Venta::with('productos')->findOrFail($id);
        $productos = Producto::all();
        
        return view('ventas_productos.edit', compact('venta', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'productos' => 'required|array',
        //     'productos.*.cantidad' => 'required|integer|min:1',
        //     'total_general' => 'required|numeric|min:0',
        // ]);

        // Validar los datos enviados
        $request->validate([
            'productos' => 'required|array',
            'productos.*.cantidad' => 'required|integer|min:1',
            'total_general' => 'required|numeric|min:0',
        ], [
            'productos.required' => 'Producto no encontrado',
            'productos.min' => 'Debe agregar al menos un producto a la venta',
            'productos.*.cantidad.required' => 'La cantidad es requerida para todos los productos',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero',
            'productos.*.cantidad.min' => 'La cantidad mínima es 1',
            'total_general.required' => 'El total general es requerido',
            'total_general.numeric' => 'El total debe ser un valor numérico',
            'total_general.min' => 'El total mínimo debe ser mayor a 0',
        ]);

        DB::beginTransaction();

        try {
            $venta = Venta::with('productos')->findOrFail($id);
            
            // 1. Guardar fechas originales y restaurar stock
            $fechasOriginales = [];
            foreach ($venta->productos as $producto) {
                // Asegurarnos de que created_at no sea null
                $fechasOriginales[$producto->id] = [
                    'created_at' => $producto->pivot->created_at ?? now()
                ];
                $producto->increment('stock', $producto->pivot->cantidad);
            }
            
            // 2. Eliminar relaciones antiguas
            $venta->productos()->detach();
            
            // 3. Actualizar total de la venta
            $venta->total = $request->input('total_general');
            $venta->save();
            
            // 4. Agregar nuevos productos
            foreach ($request->input('productos') as $productoId => $productoData) {
                $productoModel = Producto::find($productoId);
                
                if (!$productoModel) {
                    throw new \Exception("Producto no encontrado");
                }
                
                if ($productoData['cantidad'] > $productoModel->stock) {
                    throw new \Exception("Stock insuficiente para: " . $productoModel->nombre);
                }
                
                // Usar created_at original si existe y no es null, de lo contrario usar ahora
                $createdAt = $fechasOriginales[$productoId]['created_at'] ?? now();
                
                $venta->productos()->attach($productoId, [
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoModel->precio,
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                ]);
                
                $productoModel->decrement('stock', $productoData['cantidad']);
            }
            
            DB::commit();
            
            return redirect()->route('ventas_productos.index')
                ->with('success', 'Venta actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VentaProducto $ventaProducto)
    {
        //
    }
}
