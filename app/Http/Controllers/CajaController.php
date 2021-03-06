<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\StatusCaja;
use App\Http\Controllers\CambioController;
use App\Http\Controllers\LogsController;

class CajaController extends Controller
{

    public function store(Request $request)
    {
        if(!CajaController::validarDenominacion($request->denominacion)){
            $response = ["message" => "La denominación no es válida.", "error" => $request->denominacion ];
            $data = LogsController::prepare('Failed', $response['message']." DEN: ".$response['error']);
            LogsController::save($data);
            return response()->json($data, 400);
        }

        if($request->cantidad <= 0 || !is_int($request->cantidad)){
            $response = ["message" => "La cantidad no es válida.", "error" => $request->cantidad ];
            $data = LogsController::prepare('Failed', $response['message']." CANT: ".$response['error']);
            LogsController::save($data);
            return response()->json($data, 400);
        }
        $caja = Caja::create
        ([
            'denominacion' => $request->denominacion,
            'cantidad' => $request->cantidad,
        ]);

        $data = LogsController::prepare('Cargar', $caja);
        LogsController::save($data);

        return response()->json($data, 200);
    }

    public function show(Request $request)
    {
        
       $caja = StatusCaja::all();
       $data = LogsController::prepare('Estado', 'Estado Actual');
       LogsController::save($data);
       return response()->json($caja, 200);
    }

    public function pago(Request $request)
    {
        foreach($request->denominacion as $denominacion){
            if(!CajaController::validarDenominacion($denominacion)){
                $response = ["message" => "La denominación no es válida.", "error" => $denominacion ];
                $data = LogsController::prepare('Failed', $response['message']." DEN: ".$response['error']);
                LogsController::save($data);
                return response()->json($data, 400);
            }
        }

        
       $cambio = array_sum($request->denominacion) - $request->monto;

       if($cambio < 0){
           $response = ["mensaje" => "El dinero ingresado no alcanza para pagar el monto ingresado."];
            $data = LogsController::prepare('Failed', $response['mensaje']);
            LogsController::save($data);

           return response()->json($data, 400);
       };

       if($cambio > 0){

        if(!CambioController::validarCambio(Caja::all(), $cambio)){
            $response = ["mensaje" => "No hay cambio para la denominación ingresada."];
            $data = LogsController::prepare('Failed', $response['mensaje']);
            LogsController::save($data);
           return response()->json($data, 400);
       }

       $validarDenominacion = CambioController::validarDenominacion(StatusCaja::all(), $cambio);

       if(!$validarDenominacion){
            $response = ["mensaje" => "No hay cambio para la denominación ingresada."];
            $data = LogsController::prepare('Failed', $response['mensaje']);
            LogsController::save($data);
            return response()->json($data, 400);
        }

        $result = CambioController::updateDenominacion($validarDenominacion);
        if(is_array($validarDenominacion)){
           $cambio = implode(',',$validarDenominacion);
        }

        $request->monto = $request->monto." Cambio: ".$cambio;
       }

        foreach($request->denominacion as $den){

            $result = Caja::create([
                'denominacion' => $den,
                'cantidad' => 1
            ]);

            $data = LogsController::prepare('Cargar', $result);
            LogsController::save($data);
        }

        $data = LogsController::prepare('Pagar', $request->monto );
        LogsController::save($data);

       return response()->json($data, 200);
    }

    public function truncate()
    {
        

        Caja::truncate();
        $response = ["mensaje" => "La caja se ha vaciado."];

        $data = LogsController::prepare('Vaciar', $response['mensaje']);
        LogsController::save($data);

       return response()->json($response, 201);
    }

    private function validarFecha($date, $format = 'Y-m-d H:i:s'){

        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;

    }

    private function validarDenominacion($denominacion){

        $den = ['50', '100', '200', '500', '1000', '5000', '10000', '20000', '50000', '100000'];

        return in_array($denominacion, $den);

    }
}
