<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    public function create()
    {
        // FIX de Seguridad: Garantizar que el respaldo solo inicie si el rol está verificado.
        if (auth()->user()->rol !== 'Administrador') {
            abort(403, 'Acceso denegado: Solo los administradores pueden generar respaldos.');
        }
        
        if (auth()->check() && auth()->user()->rol === 'Administrador') {
            try {
                // Nombre del archivo de backup
                $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
                $filePath = storage_path('app/' . $filename);

                // Obtener todas las tablas de la base de datos
                $tables = DB::select('SHOW TABLES');

                // Crear el archivo de backup
                $handle = fopen($filePath, 'w+');

                foreach ($tables as $table) {
                    $tableName = reset($table); // Obtener el nombre de la tabla

                    // Obtener la estructura de la tabla
                    $createTable = DB::selectOne("SHOW CREATE TABLE $tableName");
                    fwrite($handle, $createTable->{'Create Table'} . ";\n\n");

                    // Obtener los datos de la tabla
                    $rows = DB::table($tableName)->get();
                    foreach ($rows as $row) {
                        $values = array_map(function ($value) {
                            return "'" . addslashes($value) . "'";
                        }, (array) $row);
                        fwrite($handle, "INSERT INTO $tableName VALUES (" . implode(', ', $values) . ");\n");
                    }
                    fwrite($handle, "\n");
                }

                fclose($handle);

                // Forzar la descarga del archivo
                return Response::download($filePath)->deleteFileAfterSend(true);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error al realizar el backup: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'No tienes permisos para realizar esta acción.');
    }
}
