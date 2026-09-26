<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\VentaProducto;
use App\Models\VentaServicio;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;

class VentaController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                //abort(404, 'No tienes permiso para acceder a las ventas totales.');
                return redirect(route('error.page'))->with('error', 'No tienes permiso para acceder a las ventas totales.');
            }
            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol != "Administrador") {
                //abort(404, 'Solo los administradores pueden realizar esta acción.');
                return redirect(route('error.page'))->with('error', 'Solo los administradores pueden realizar esta acción.');
            }
            return $next($request);
        })->only(['resumenVentas', 'exportarPDF']);
        
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

    public function resumenVentas(Request $request) {
        $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
        $tipo = $request->tipo; // Obtener el tipo seleccionado

        if (!$fechaInicio || !$fechaFin) {
            return view('ventas.resumen'); 
        }

        // Consultar ventas de productos
        $ventasProductos = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->select('id', 'fecha_hora', 'total', \DB::raw("'Producto' as tipo"));

        // Consultar ventas de servicios
        $ventasServicios = VentaServicio::whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->select('id', 'fecha_hora', 'total', \DB::raw("'Servicio' as tipo"));

        // Combinar las consultas según el filtro
        if ($tipo == 'Producto') {
            $query = $ventasProductos;
        } elseif ($tipo == 'Servicio') {
            $query = $ventasServicios;
        } else {
            $query = $ventasProductos->union($ventasServicios);
        }

        // Calcular el total de todas las ventas
        $totalVentas = $query->sum('total');

        // Obtener los resultados paginados
        $ventas = $query->orderBy('fecha_hora', 'desc')->paginate(10);

        return view('ventas.resumen', compact('ventas', 'totalVentas', 'tipo'));
    }

    public function exportarPDF(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
        ]);

        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();
        $tipo = $request->tipo; // Obtener el tipo del request
        
        // Consultar ventas de productos
        $ventasProductos = Venta::whereBetween('created_at', [$fecha_inicio, $fecha_fin])
            ->select('id', 'fecha_hora', 'total', \DB::raw("'Producto' as tipo"));

        // Consultar ventas de servicios
        $ventasServicios = VentaServicio::whereBetween('fecha_hora', [$fecha_inicio, $fecha_fin])
            ->select('id', 'fecha_hora', 'total', \DB::raw("'Servicio' as tipo"));

        // Aplicar el filtro según el tipo
        if ($tipo == 'Producto') {
            $ventas = $ventasProductos->get();
        } elseif ($tipo == 'Servicio') {
            $ventas = $ventasServicios->get();
        } else {
            $ventas = $ventasProductos->union($ventasServicios)->get();
        }

        $totalVentas = $ventas->sum('total');
        $usuario = Auth::user()->nombre;

        $pdf = Pdf::loadView('ventas.pdf', compact('ventas', 'totalVentas', 'fecha_inicio', 'fecha_fin', 'usuario', 'tipo'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('reporte_ventas.pdf');
    }

    public function detalles($id, $tipo)
    {
        if ($tipo === 'producto') {
            $venta = Venta::with('productos')->withTrashed()->find($id);
            
            if (!$venta) {
                return abort(404, 'Venta de productos no encontrada');
            }

            foreach ($venta->productos as $producto) {
                $producto->subtotal_calculado = $producto->pivot->cantidad * $producto->pivot->precio_unitario;
            }

            return view('ventas.detalles', compact('venta'));
        }
        elseif ($tipo === 'servicio') {
            $ventaServicio = VentaServicio::with('servicios')->find($id);

            if (!$ventaServicio) {
                return abort(404, 'Venta de servicios no encontrada');
            }

            foreach ($ventaServicio->servicios as $servicio) {
                $servicio->subtotal_calculado = $servicio->pivot->cantidad * $servicio->pivot->precio_unitario;
            }

            return view('ventas.detalles_servicio', compact('ventaServicio'));
        }

        return abort(404, 'Tipo de venta no válido');
    }

    public function exportarRecibo($id, $tipo)
    {
        $usuario = Auth::user()->nombre;

        if ($tipo === 'producto') {
            $venta = Venta::with('productos')->withTrashed()->findOrFail($id);

            foreach ($venta->productos as $producto) {
                $producto->subtotal_calculado = $producto->pivot->cantidad * $producto->pivot->precio_unitario;
            }

            $pdf = Pdf::loadView('ventas_productos.recibo_pdf', compact('venta', 'usuario'))
                    ->setPaper('a4', 'portrait');

            return $pdf->stream('recibo_venta_' . $venta->id . '.pdf');
        }
        elseif ($tipo === 'servicio') {
            $ventaServicio = VentaServicio::with('servicios')->findOrFail($id);

            foreach ($ventaServicio->servicios as $servicio) {
                $servicio->subtotal_calculado = $servicio->pivot->cantidad * $servicio->pivot->precio_unitario;
            }

            $pdf = Pdf::loadView('ventas_servicios.recibo_pdf', compact('ventaServicio', 'usuario'))
                    ->setPaper('a4', 'portrait');

            return $pdf->stream('recibo_venta_servicio_' . $ventaServicio->id . '.pdf');
        }

        return abort(404, 'Tipo de venta no válido');
    }

    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venta $venta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        //
    }
}
