<?php 

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Models\Inventario;

class ProductoController extends controller
{

    public function __construct(){
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                //abort(404, 'No tienes permiso para acceder al módulo de productos.');
                return redirect(route('error.page'))->with('error', 'No tienes permiso para acceder al módulo de productos.');
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

        // Busca por nombre o por código de barras
        $lista = Producto::where('nombre', 'like', '%' . $busqueda . '%')
            ->orWhere('codigo_barras', 'like', '%' . $busqueda . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('productos.index')->with(compact('lista', 'busqueda'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('productos.create');
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
                Rule::unique('productos')->whereNull('deleted_at')
            ], // Excluir registros eliminados
            'descripcion' => [
                'required', 
                'min:15', 
                'max:100', 
                'regex:/[a-zA-ZÀ-ÿ]/', // Al menos una letra
            ],
            'precio' => 'required|numeric|min:1',
            'mayoreo' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value >= $request->precio) {
                        $fail('El precio por mayoreo debe ser menor al precio normal');
                    }
                },
            ],
            'stock' => 'required|integer|min:1',
            'codigo_barras' => [
                'required', 
                'required', 
                'digits_between:10,15', // Asegura que tenga entre 10 y 15 dígitos
                Rule::unique('productos')->whereNull('deleted_at')
            ], // Excluir registros eliminados,
        ], [
            'nombre.required' => 'Debes llenar el campo de nombre.',
            'nombre.unique' => 'El nombre del producto ya existe.',
            'nombre.min' => 'El nombre del producto debe contener mínimo 5 caracteres.',
            'nombre.max' => 'El nombre del producto no debe exceder 50 caracteres.',
            'nombre.regex' => 'El nombre debe contener al menos una letra.',
            'descripcion.required' => 'Debes llenar el campo de descripción.',
            'descripcion.min' => 'La descripción debe contener mínimo 15 caracteres.',
            'descripcion.max' => 'La descripción no debe exceder 100 caracteres.',
            'descripcion.regex' => 'La descripción debe contener al menos una letra.',
            'precio.required' => 'Debes especificar el precio del producto.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio debe ser minimo 1',
            'mayoreo.required' => 'Debes especificar el precio del producto.',
            'mayoreo.numeric' => 'El precio debe ser un valor numérico.',
            'mayoreo.min' => 'El precio debe ser minimo 1',
            'stock.required' => 'Debes especificar el stock del producto.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock debe ser al menos 1 unidad.',
            'codigo_barras.required' => 'Debes llenar el campo de código de barras.',
            'codigo_barras.digits_between' => 'El código de barras debe tener entre 10 y 15 dígitos numéricos.',
            'codigo_barras.unique' => 'El código de barras ya existe.',
        ]);

        $prod = new Producto;
        $prod->nombre = $request->nombre;
        $prod->descripcion = $request->descripcion;
        $prod->precio = $request->precio;
        $prod->mayoreo = $request->mayoreo;
        $prod->stock = $request->stock;
        $prod->codigo_barras = $request->codigo_barras;
        $prod->save(); // Guarda primero el producto

        // Ahora creamos el registro en inventario con el ID del producto recién creado
        $inven = new Inventario;
        $inven->producto_id = $prod->id; // Aquí usamos el ID del producto recién guardado
        $inven->fecha = now();
        $inven->stock = $request->stock;
        $inven->save();

        
        return redirect(route('productos.index'))->with('success', 'Producto creado correctamente.');
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
        $prod = Producto::find($id);
        return view('productos.edit')->with(compact('prod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nombre' => [
                'required', 
                'min:5', 
                'max:50', 
                'regex:/[a-zA-ZÀ-ÿ]/', // Al menos una letra
                Rule::unique('productos')->ignore($id)->whereNull('deleted_at') // Ignorar el producto actual
            ],
            'descripcion' => [
                'required', 
                'min:15', 
                'max:100', 
                'regex:/[a-zA-ZÀ-ÿ]/' // Al menos una letra
            ],
            'precio' => 'required|numeric|min:1',
            'mayoreo' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value >= $request->precio) {
                        $fail('El precio por mayoreo debe ser menor al precio normal');
                    }
                },
            ],
            'codigo_barras' => [
                'required', 
                'digits_between:10,15', // Asegura que tenga entre 10 y 15 dígitos
                Rule::unique('productos')->ignore($id)->whereNull('deleted_at') // Ignorar el producto actual
            ],
        ], [
            'nombre.required' => 'Debes llenar el campo de nombre.',
            'nombre.unique' => 'El nombre del producto ya existe.',
            'nombre.min' => 'El nombre del producto debe contener mínimo 5 caracteres.',
            'nombre.max' => 'El nombre del producto no debe exceder 50 caracteres.',
            'nombre.regex' => 'El nombre debe contener al menos una letra.',
            'descripcion.required' => 'Debes llenar el campo de descripción.',
            'descripcion.min' => 'La descripción debe contener mínimo 15 caracteres.',
            'descripcion.max' => 'La descripción no debe exceder 100 caracteres.',
            'descripcion.regex' => 'La descripción debe contener al menos una letra.',
            'precio.required' => 'Debes especificar el precio del producto.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio debe ser minimo 1',
            'mayoreo.required' => 'Debes especificar el precio del producto.',
            'mayoreo.numeric' => 'El precio debe ser un valor numérico.',
            'mayoreo.min' => 'El precio debe ser minimo 1',
            'codigo_barras.required' => 'Debes llenar el campo de código de barras.',
            'codigo_barras.digits_between' => 'El código de barras debe tener entre 10 y 15 dígitos numéricos.',
            'codigo_barras.unique' => 'El código de barras ya existe.',
        ]);

        $prod = Producto::find($id);
        $prod->nombre = $request->nombre;
        $prod->descripcion = $request->descripcion;
        $prod->precio = $request->precio;
        $prod->mayoreo = $request->mayoreo;
        $prod->codigo_barras = $request->codigo_barras;
        $prod->save();

        return redirect(route('productos.index'))->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prod = Producto::find($id);
        $prod->delete();

        return redirect(route('productos.index'));
    }

    public function exportarPDF(Request $request)
    {
        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;

        $busqueda = $request->input('busqueda', '');

        // Busca por nombre o por código de barras
        $lista = Producto::where('nombre', 'like', '%' . $busqueda . '%')
            ->orWhere('codigo_barras', 'like', '%' . $busqueda . '%')
            ->get();

        // $pdf = Pdf::loadView('servicios.pdf', compact('lista'));
        // return $pdf->download('servicios.pdf');
        $pdf = Pdf::loadView('productos.pdf', compact('lista', 'usuario'))
                ->setPaper('a4', 'portrait');
        return $pdf->stream('productos.pdf');
    }
}