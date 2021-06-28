<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

class LogsController extends Controller
{
    //

    public function show()
    {        
        $logs = Logs::all();

       return response()->json($logs, 200);
    }


    public static function save($data)
    {
        
        $logs = Logs::create
        ([
            'accion' => $data['accion'],
            'detalle' => $data['detalle'],
            'referencia' => $data['referencia'],
        ]);


        return response()->json($logs, 200);
    }

    public static function prepare($indice, $referencia)
    {        
       
        $detalle = [ 
            'Cargar' => 'Cargar base a la caja.', 
            'Pagar' => 'Pago realizado.', 
            'Cambio' => 'Cambio entregado.',
            'Estado' => 'Consulta de estado.',
            'Vaciar' => 'Retiro de todo el dinero.',
            'Actualizar' => 'Actualizacion de base.',
            'Borrar' => 'Borrar Base.',
            'Failed' => 'Error de transaccion.',
        ];

        $data = [
            'accion' => $indice,
            'detalle' => $detalle[$indice],
            'referencia' => $referencia
        ];

       return $data;
    }

}
