<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Usuario;
use App\Models\Barbero;
use App\Models\HorarioTrabajo;
use App\Models\HorarioExcepcion;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;

use Illuminate\Validation\ValidationException;


//NOTIFICACIONES
use App\Notifications\NuevaCitaNotification;
use App\Notifications\CitaReagendadaNotification;

//PARA LO DE GOOGLE CALENDAR
use App\Services\GoogleCalendarService;


class CitaController extends Controller
{

    //PARA LO DE GOOGLE CALENDAR
    protected $googleCalendar;


    public function __construct(){

        // Inicializa el servicio de Google Calendar
        $this->googleCalendar = new GoogleCalendarService();


        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                //abort(404, 'No tienes permiso para acceder al módulo de citas.');
                return redirect(route('error.page'))->with('error', 'No tienes permiso para acceder al módulo de citas.');
            }
            return $next($request);
        })->except(['getHorasDisponibles', 'getHorasOcupadas', 'getHorarioTrabajo', 'getHorasDisponiblesMovil', 'getCitasUsuario', 'getCitasBarbero', 'crearCitaMovil', 'getCita', 'actualizarCitaMovil', 'eliminarCitaMovil', 'obtenerBarberos', 'obtenerClientes', 'getBarberoId']);
        
        // $this->middleware(function ($request, $next) {
        // if (Auth::check() && Auth::user()->rol == "Cliente") {
        //     Auth::logout(); // Cierra la sesión del usuario
        //     $request->session()->invalidate(); // Invalida la sesión
        //     $request->session()->regenerateToken(); // Regenera el token CSRF por seguridad
            
        //     return redirect(route('error.page'))->with('error', 'No tienes los permisos.');
        // }
        // return $next($request);
    // });
        

        // $this->middleware(function ($request, $next) {
        //     if (Auth::check() && Auth::user()->rol != "Administrador") {
        //         abort(404, 'Solo los administradores pueden realizar esta acción.');
        //     }
        //     return $next($request);
        // })->only(['destroy', 'create', 'store', 'edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     */
//     public function index(Request $request)
// {
//     $busqueda = $request->busqueda;

//     // 🔹 OBTENER LAS FECHAS ÚNICAS Y PAGINARLAS (con groupBy en lugar de distinct)
//     $fechas = Cita::select('fecha')
//         ->groupBy('fecha') // Agrupamos por fecha para asegurarnos de que sean únicas
//         ->orderBy('fecha', 'desc')
//         ->paginate(1); // 👈 Se paginan SOLO las fechas

//     // 🔹 OBTENER LAS CITAS DE LA FECHA ACTUALMENTE PAGINADA
//     $citas = Cita::with(['usuario', 'barbero'])
//         ->whereIn('fecha', $fechas->pluck('fecha')) // 👈 Filtramos por la fecha actual paginada
//         ->when($busqueda, function ($query, $busqueda) {
//             return $query->where(function ($subquery) use ($busqueda) {
//                 $subquery->whereHas('usuario', function ($q) use ($busqueda) {
//                     $q->where('nombre', 'like', '%' . $busqueda . '%');
//                 })
//                 ->orWhereHas('barbero', function ($q) use ($busqueda) {
//                     $q->where('nombre', 'like', '%' . $busqueda . '%');
//                 })
//                 ->orWhere('hora', 'like', '%' . $busqueda . '%');
//             });
//         })
//         ->orderBy('hora', 'asc')
//         ->get(); // 👈 Aquí NO paginamos las citas, solo filtramos por la fecha paginada

//     // 🔹 ACTUALIZAR ESTADO DE CITAS PASADAS
//     foreach ($citas as $cita) {
//         $fechaHoraCita = Carbon::parse($cita->fecha . ' ' . $cita->hora);
//         $ahora = Carbon::now();

//         if ($ahora->greaterThanOrEqualTo($fechaHoraCita->addHour()) && $cita->estado !== 'Completado') {
//             $cita->estado = 'Completado';
//             $cita->save();
//         }
//     }

//     return view('citas.index', compact('citas', 'fechas', 'busqueda'));
// }


    public function index(Request $request)
    {

        $barberos = Barbero::all();

        $busqueda = $request->busqueda;
        // 🔹 OBTENER LAS CITAS PAGINADAS (10 registros por página)
        $citas = Cita::with(['usuario', 'barbero'])
            ->where('nombre_cli', 'like', '%' . $busqueda . '%')
            ->orderBy('fecha', 'desc')  // Ordenar por fecha descendente
            ->orderBy('hora', 'asc')    // Ordenar las horas de cada día
            ->paginate(10);             // 👈 PAGINAR DE 10 EN 10

        // 🔹 ACTUALIZAR ESTADO DE CITAS PASADAS
        foreach ($citas as $cita) {
            $fechaHoraCita = Carbon::parse($cita->fecha . ' ' . $cita->hora);
            $ahora = Carbon::now();

            if ($ahora->greaterThanOrEqualTo($fechaHoraCita->addHour()) && $cita->estado !== 'Completado') {
                $cita->estado = 'Completado';
                $cita->save();
            }
        }

        return view('citas.index', compact('citas','busqueda', 'barberos'));
    }


    /**
     * Show the form for creating a new resource.
     */
    // Mostrar el formulario de creación de cita
    public function create()
    {
        $barberos = Barbero::where('estado', 1)->get(); // Obtener solo los barberos con estado igual a ACTIVO
        $clientes = Usuario::where('rol', 'Cliente')
               ->select('id', 'nombre', 'telefono', 'email')
               ->get();
        
        return view('citas.create', compact('barberos', 'clientes'));
    }

    // Función para obtener las horas disponibles según el día
    public function getHorasDisponibles(Request $request)
    {
        // Establecer el idioma a español para Carbon
        Carbon::setLocale('es');

        $fecha = $request->input('fecha');
        $barbero_id = $request->input('barbero_id');

        // Verifica que los parámetros recibidos estén correctos
        Log::debug('Fecha recibida  FUNCION GET:', [$fecha]);
        Log::debug('Barbero ID recibido  FUNCION GET:', [$barbero_id]);

        // Obtener el día de la semana de la fecha seleccionada en español
        $diaSemana = Carbon::parse($fecha)->locale('es')->isoFormat('dddd');
        Log::debug('Día de la semana:', [$diaSemana]);

        // Obtener los horarios disponibles del barbero y día seleccionado
        $horariosTrabajo = HorarioTrabajo::where('dia', $diaSemana)->get();
        Log::debug('Horarios de trabajo obtenidos  FUNCION GET:', [$horariosTrabajo]);

        // Si estamos editando una cita, excluimos la hora de la cita actual
        if (isset($id)) {
            $horasOcupadas = Cita::where('fecha', $fecha)
                ->where('barbero_id', $barbero_id)
                ->where('id', '!=', $id) // Excluye la cita que estamos editando
                ->pluck('hora')
                ->toArray();
        } else {
            // Si no estamos editando, obtenemos todas las horas ocupadas
            $horasOcupadas = Cita::where('fecha', $fecha)
                ->where('barbero_id', $barbero_id)
                ->pluck('hora')
                ->toArray();
        }


        // Filtrar las horas disponibles (restar las horas ocupadas de los horarios de trabajo)
        $horariosDisponibles = $horariosTrabajo->filter(function ($horario) use ($horasOcupadas) {
            return !in_array($horario->hora, $horasOcupadas);
        });

        // Convertir la colección de Eloquent a un arreglo simple
        $horariosDisponibles = $horariosDisponibles->values()->toArray();
        
        Log::debug('Horarios disponibles FUNCION GET:', [$horariosDisponibles]);

        // Devolver la respuesta en formato JSON
        return response()->json($horariosDisponibles);
    }

    // Función para obtener las horas disponibles según el día
    public function getHorasDisponiblesWeb(Request $request)
    {
        // Establecer el idioma a español para Carbon
        Carbon::setLocale('es');

        $fecha = $request->input('fecha');
        $barbero_id = $request->input('barbero_id');

        // Verifica que los parámetros recibidos estén correctos
        Log::debug('Fecha recibida  FUNCION GET:', [$fecha]);
        Log::debug('Barbero ID recibido  FUNCION GET:', [$barbero_id]);

        // Obtener el día de la semana de la fecha seleccionada en español
        $diaSemana = Carbon::parse($fecha)->locale('es')->isoFormat('dddd');
        Log::debug('Día de la semana:', [$diaSemana]);

        // Obtener los horarios disponibles del barbero y día seleccionado
        $horariosTrabajo = HorarioTrabajo::where('dia', $diaSemana)->get();
        Log::debug('Horarios de trabajo obtenidos  FUNCION GET:', [$horariosTrabajo]);
        
        // Obtener los horarios reservados (bloqueos de horarios)
        $horariosReservado = DB::table('bloqueos_horarios')
            ->where('fecha', $fecha)
            ->where('expira_en', '>=', now()) // Filtrar solo los bloqueos aún vigentes
            ->pluck('hora') // Obtener solo las horas
            ->toArray();

        // Si estamos editando una cita, excluimos la hora de la cita actual
        if (isset($id)) {
            $horasOcupadas = Cita::where('fecha', $fecha)
                ->where('barbero_id', $barbero_id)
                ->where('id', '!=', $id) // Excluye la cita que estamos editando
                ->pluck('hora')
                ->toArray();
        } else {
            // Si no estamos editando, obtenemos todas las horas ocupadas
            $horasOcupadas = Cita::where('fecha', $fecha)
                ->where('barbero_id', $barbero_id)
                ->pluck('hora')
                ->toArray();
        }


        // Filtrar horarios disponibles
        $horariosDisponibles = $horariosTrabajo->filter(function ($horario) use ($horasOcupadas, $horariosReservado) {
            return !in_array($horario->hora, $horasOcupadas) && !in_array($horario->hora, $horariosReservado);
        });
    
        // Convertir colección a arreglo
        $horariosDisponibles = $horariosDisponibles->values()->toArray();
    
        Log::debug('Horarios disponibles FUNCION GET:', [$horariosDisponibles]);
    
        return response()->json($horariosDisponibles);
        
    }

    // Función para obtener las horas ocupadas según el día
    public function getHorasOcupadas(Request $request)
    {
        // Establecer el idioma a español para Carbon
        Carbon::setLocale('es');

        $fecha = $request->input('fecha');
        $barbero_id = $request->input('barbero_id');

        // Verifica que los parámetros recibidos estén correctos
        Log::debug('Fecha recibida FUNCION GET HORAS OCUPADAS:', [$fecha]);
        Log::debug('Barbero ID recibido FUNCION GET HORAS OCUPADAS:', [$barbero_id]);

        // Obtener las horas ocupadas para el barbero en la fecha seleccionada
        $horasOcupadas = Cita::where('fecha', $fecha)
            ->where('barbero_id', $barbero_id)
            ->pluck('hora')
            ->toArray();

        Log::debug('Horas ocupadas FUNCION GET HORAS OCUPADAS:', [$horasOcupadas]);

        // Devolver la respuesta en formato JSON
        return response()->json($horasOcupadas);
    }

    // Función para obtener el horario de trabajo del barbero
    public function getHorarioTrabajo(Request $request)
    {
        // Establecer el idioma a español para Carbon
        Carbon::setLocale('es');

        $fecha = $request->input('fecha');
        $barbero_id = $request->input('barbero_id');

        // Verifica que los parámetros recibidos estén correctos
        Log::debug('Fecha recibida FUNCION GET HORARIO TRABAJO:', [$fecha]);
        Log::debug('Barbero ID recibido FUNCION GET HORARIO TRABAJO:', [$barbero_id]);

        // Obtener el día de la semana de la fecha seleccionada en español
        $diaSemana = Carbon::parse($fecha)->locale('es')->isoFormat('dddd');
        Log::debug('Día de la semana:', [$diaSemana]);

        // Obtener todas las horas de trabajo del barbero para el día seleccionado
        $horarioTrabajo = HorarioTrabajo::where('dia', $diaSemana)
            ->pluck('hora')
            ->toArray();

        if (!empty($horarioTrabajo)) {
            Log::debug('Horario de trabajo obtenido:', [$horarioTrabajo]);
            return response()->json([
                'success' => true,
                'data' => $horarioTrabajo,
            ]);
        } else {
            Log::debug('No se encontró horario de trabajo para el barbero y día seleccionado.');
            return response()->json([
                'success' => false,
                'message' => 'No se encontró horario de trabajo para el barbero y día seleccionado.',
            ], 404);
        }
    }

    
    // Función para guardar la cita
    public function store(Request $request)
    {
        $request->validate([
            'nombre_cli' => [
                'required',
                'string',
                'min:5',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿÑñ\s]+$/'
            ],
            'telefono' => [ 
                'nullable',
                'digits:10', 
            ],
            'barbero_id' => 'required|exists:barberos,id',
            'fecha' => 'required|date|after:yesterday',
            'hora' => 'required',
        ],[
            'nombre_cli.required' => 'El campo nombre del cliente es obligatorio.',
            'nombre_cli.min' => 'El nombre del cliente debe tener al menos 5 caracteres.',
            'nombre_cli.max' => 'El nombre del cliente no debe exceder los 50 caracteres',
            'nombre_cli.regex' => 'El nombre del cliente solo puede contener letras y espacios, sin números ni caracteres especiales.',

            'telefono.digits' => 'El teléfono deben ser 10 digitos',

            'barbero_id.required' => 'El campo barbero es obligatorio.',
            'fecha.required' => 'El campo fecha es obligatorio.',
            'hora.required' => 'El campo hora es obligatorio.',
        ]);
    
        return DB::transaction(function () use ($request) {
            $fecha = $request->fecha;
            $hora = $request->hora;
            $barberoId = $request->barbero_id;
    
            // Verificar si la hora sigue disponible
            $horaOcupada = Cita::where('fecha', $fecha)
                ->where('barbero_id', $barberoId)
                ->where('hora', $hora)
                ->exists();
    
            if ($horaOcupada) {
                throw ValidationException::withMessages(['hora' => 'La hora seleccionada ya no está disponible.']);
            }
            
            // Obtener los horarios reservados (bloqueos de horarios)
            $horariosReservado = DB::table('bloqueos_horarios')
                ->where('fecha', $fecha)
                ->where('expira_en', '>=', now()) // Filtrar solo los bloqueos aún vigentes
                ->where('hora', $hora)
                ->exists();
                
            if ($horariosReservado) {
                throw ValidationException::withMessages(['hora' => 'La hora seleccionada está siendo reservada por otro usuario.']);
            }
    
            // Crear la cita dentro de la transacción
            $cita = Cita::create([
                'nombre_cli' => $request->nombre_cli,
                'telefono' => $request->telefono,
                'barbero_id' => $barberoId,
                'fecha' => $fecha,
                'hora' => $hora,
                'google_event_id' => null, // Añade este campo a tu migración
            ]);
            
            // Sincronizar con Google Calendar
            try {
                $eventId = $this->googleCalendar->createEvent($cita);
                if ($eventId) {
                    $cita->google_event_id = $eventId;
                    $cita->save();
                }
            } catch (\Exception $e) {
                Log::error('Error al sincronizar con Google Calendar: ' . $e->getMessage());
                // No interrumpir el flujo por un error en Google Calendar
            }

            // Notificar al barbero
            // $barbero = Barbero::with('usuario')->find($request->barbero_id);
            // $cliente = Usuario::find($request->usuario_id);
            
            // if ($barbero && $barbero->usuario && $barbero->usuario->fcm_token) {
            //     $fechaFormateada = Carbon::parse($request->fecha)->format('d/m/Y');
            //     $horaFormateada = substr($request->hora, 0, 5); // Formato HH:MM
            
            //     $barbero->usuario->notify(new NuevaCitaNotification([
            //         'cliente_nombre' => $cliente->nombre,
            //         'fecha' => $fechaFormateada,
            //         'hora' => $horaFormateada,
            //         'cita_id' => $cita->id // Ahora sí existe $cita
            //     ]));
            // }

    
            return redirect()->route('citas.index')->with('success', 'Cita agendada correctamente.');
        });
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
        $cita = Cita::findOrFail($id);
        $barberos = Barbero::where('estado', 1)->get(); // Obtener solo los barberos con estado igual a ACTIVO
        $clientes = Usuario::where('rol', 'Cliente')->get();
        return view('citas.edit', compact('cita','barberos', 'clientes'));
    }    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre_cli' => [
                'required',
                'string',
                'min:5',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿÑñ\s]+$/'
            ],
            'telefono' => [
                'nullable',
                'digits:10', 
            ],
            'barbero_id' => 'required|exists:barberos,id',
            'fecha' => 'required|date',
            'hora' => 'required',
        ],[
            'nombre_cli.required' => 'El campo nombre del cliente es obligatorio.',
            'nombre_cli.min' => 'El nombre del cliente debe tener al menos 5 caracteres.',
            'nombre_cli.max' => 'El nombre del cliente no debe exceder los 50 caracteres',
            'nombre_cli.regex' => 'El nombre del cliente solo puede contener letras y espacios, sin números ni caracteres especiales.',

            'telefono.digits' => 'El teléfono deben ser 10 digitos',

            'barbero_id.required' => 'El campo barbero es obligatorio.',
            'fecha.required' => 'El campo fecha es obligatorio.',
            'hora.required' => 'El campo hora es obligatorio.',
        ]);
        
        return DB::transaction(function () use ($request, $id) {
            $cita = Cita::findOrFail($id);
            
            $fechaAnterior = $cita->fecha;
            $horaAnterior = $cita->hora;
        
            $fecha = $request->fecha;
            $hora = $request->hora;
            $barberoId = $request->barbero_id;
    
            // Verificar si la hora sigue disponible
            $horaOcupada = Cita::where('fecha', $fecha)
                ->where('barbero_id', $barberoId)
                ->where('hora', $hora)
                ->exists();
    
            if ($horaOcupada) {
                throw ValidationException::withMessages(['hora' => 'La hora seleccionada ya no está disponible.']);
            }
            
            // Obtener los horarios reservados (bloqueos de horarios)
            $horariosReservado = DB::table('bloqueos_horarios')
                ->where('fecha', $fecha)
                ->where('expira_en', '>=', now()) // Filtrar solo los bloqueos aún vigentes
                ->where('hora', $hora)
                ->exists();
                
            if ($horariosReservado) {
                throw ValidationException::withMessages(['hora' => 'La hora seleccionada está siendo reservada por otro usuario.']);
            }

            // // Actualizar la cita
            // $cita = Cita::find($id);
            // $cita->nombre_cli = $request->nombre_cli;
            // $cita->telefono = $request->telefono;
            // $cita->barbero_id = $request->barbero_id;
            // $cita->fecha = $request->fecha;
            // $cita->hora = $request->hora;
            // $cita->estado = 'Pendiente';
            // $cita->save();

            // Actualizar la cita
            $cita->update([
                'nombre_cli' => $request->nombre_cli,
                'telefono' => $request->telefono,
                'barbero_id' => $request->barbero_id,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'estado' => 'Pendiente',
            ]);

            // Sincronizar con Google Calendar si existe un evento
            if ($cita->google_event_id) {
                try {
                    $this->googleCalendar->updateEvent($cita->google_event_id, $cita);
                } catch (\Exception $e) {
                    Log::error('Error al actualizar evento en Google Calendar: ' . $e->getMessage());
                }
            } else {
                // Si no tiene evento, crear uno nuevo
                try {
                    $eventId = $this->googleCalendar->createEvent($cita);
                    if ($eventId) {
                        $cita->google_event_id = $eventId;
                        $cita->save();
                    }
                } catch (\Exception $e) {
                    Log::error('Error al crear evento en Google Calendar: ' . $e->getMessage());
                }
            }
            
            // Notificar al cliente si hubo cambios - Versión mejorada
            // if ($request->has('fecha') || $request->has('hora')) {
            //     $usuario = Usuario::find($cita->usuario_id);
            //     $barbero = Barbero::with('usuario')->find($cita->barbero_id);
                
            //     if ($usuario && $usuario->fcm_token) {
            //         $fechaFormateada = Carbon::parse($request->fecha ?? $cita->fecha)->format('d/m/Y');
            //         $horaFormateada = substr($request->hora ?? $cita->hora, 0, 5);
                    
            //         $datosNotificacion = [
            //             'fecha_nueva' => $fechaFormateada,
            //             'hora_nueva' => $horaFormateada,
            //             'barbero_nombre' => $barbero ? $barbero->usuario->nombre : 'el barbero',
            //             'cita_id' => $cita->id
            //         ];
                    
            //         // Agregar cambios si existen
            //         if (isset($cambios['fecha_anterior'])) {
            //             $datosNotificacion['fecha_anterior'] = Carbon::parse($cambios['fecha_anterior'])->format('d/m/Y');
            //         }
            //         if (isset($cambios['hora_anterior'])) {
            //             $datosNotificacion['hora_anterior'] = substr($cambios['hora_anterior'], 0, 5);
            //         }
                    
            //         $usuario->notify(new CitaReagendadaNotification($datosNotificacion));
            //     }
            // }
    
            return redirect()->route('citas.index')->with('success', 'Cita reagendada exitosamente');
        });
    }

    public function filtro(Request $request)
    {    
        // Obtener todos los barberos, incluyendo los eliminados con SoftDeletes
        // Obtener barberos que tienen ventas asociadas
        $barberos = Barbero::whereHas('citas', function($query) {
            $query->whereNotNull('fecha');
        })->withTrashed()->orderBy('nombre', 'asc')->get();

        $usuarios = Usuario::whereHas('citas', function($query) {
            $query->whereNotNull('fecha');
        })->withTrashed()->orderBy('nombre', 'asc')->get();

        $citas = [];

        // Convertir fechas para incluir todo el rango del día
        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();

        // Iniciar la consulta con el filtro de fechas
        $query = Cita::whereBetween('fecha', [$fecha_inicio, $fecha_fin]);

        // Aplicar filtro por cliente (si está presente)
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Aplicar filtro por barbero (si está presente)
        if ($request->filled('barbero_id')) {
            $query->where('barbero_id', $request->barbero_id);
        }

        // Aplicar filtro por estado (si está presente)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Obtener los datos filtrados
        $citas = $query->orderBy('fecha', 'desc') // Ordenar por fecha de la más reciente a la más antigua
                    ->paginate(10); // Paginar cada 10 elementos

        // Retornar la vista con los datos filtrados
        return view('citas.filtro', compact('barberos', 'usuarios', 'citas'));
    }

    
    public function exportarPDF(Request $request)
    {
        // Validar que las fechas están presentes
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
        ]);
    
        // Convertir las fechas al formato correcto
        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();
        
        // Inicializar la consulta para las ventas
        $citasQuery = Cita::whereBetween('fecha', [$fecha_inicio, $fecha_fin]);
    
        // Aplicar otros filtros si existen
        if ($request->filled('usuario_id')) {
            $citasQuery->where('usuario_id', $request->usuario_id);
        }
    
        if ($request->filled('barbero_id')) {
            $citasQuery->where('barbero_id', $request->barbero_id);
        }
    
        if ($request->filled('estado')) {
            $citasQuery->where('estado', $request->estado);
        }
    
        // Obtener las ventas filtradas
        $citas = $citasQuery->get();
    
    
        // // Calcular el total de ventas filtradas
        // $totalVentas = $ventas->sum('total');
    
        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;
    
        // Cargar la vista para el PDF con solo los resultados filtrados
        $pdf = Pdf::loadView('citas.pdf', compact('citas', 'fecha_inicio', 'fecha_fin', 'usuario'))
                  ->setPaper('a4', 'portrait');
    
        return $pdf->stream('reporte_citas.pdf');
    }
    

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cita = Cita::find($id);

        // Solo permite borrar si la cita está completada
        if ($cita->estado !== 'Completado') {
            return redirect()->route('citas.index')->with('error', 'Solo puedes eliminar citas completadas.');
        }

        // Eliminar evento de Google Calendar si existe
        if ($cita->google_event_id) {
            try {
                $this->googleCalendar->deleteEvent($cita->google_event_id);
            } catch (\Exception $e) {
                Log::error('Error al eliminar evento de Google Calendar: ' . $e->getMessage());
            }
        }

        $cita->delete();

        return redirect()->route('citas.index')->with('success', 'Cita eliminada correctamente.');
    }

    // private function obtenerHorariosDisponibles($barberoId, $fecha)
    // {
    //     // Obtener los horarios de trabajo del barbero
    //     $horariosTrabajo = HorarioTrabajo::where('barbero_id', $barberoId)->get();

    //     // Obtener las citas agendadas para la fecha y barbero seleccionados
    //     $citasOcupadas = Cita::where('barbero_id', $barberoId)
    //         ->where('fecha', $fecha)
    //         ->pluck('hora')
    //         ->toArray();

    //     // Calcular los horarios disponibles
    //     $horariosDisponibles = [];

    //     foreach ($horariosTrabajo as $horario) {
    //         $horaActual = strtotime($horario->hora_inicio);
    //         $horaFin = strtotime($horario->hora_fin);

    //         while ($horaActual < $horaFin) {
    //             $horaFormateada = date('H:i', $horaActual);

    //             // Verificar si la hora no está ocupada
    //             if (!in_array($horaFormateada, $citasOcupadas)) {
    //                 $horariosDisponibles[] = $horaFormateada;
    //             }

    //             // Añadir 30 minutos (o el intervalo deseado)
    //             $horaActual = strtotime('+30 minutes', $horaActual);
    //         }
    //     }

    //     return $horariosDisponibles;
    // }

    // public function obtenerHorariosDisponiblesAjax(Request $request)
    // {
    //     $barberoId = $request->query('barbero_id');
    //     $fecha = $request->query('fecha');

    //     if (!$barberoId || !$fecha) {
    //         return response()->json([]);
    //     }

    //     // Obtener los horarios disponibles
    //     $horariosDisponibles = $this->obtenerHorariosDisponibles($barberoId, $fecha);

    //     return response()->json($horariosDisponibles);
    // }

}
