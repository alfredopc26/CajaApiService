<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\StatusCaja;
use App\Http\Controllers\CambioController;

class CajaController extends Controller
{

    public function store(Request $request)
    {
        

        $caja = Caja::create
        ([
            'denominacion' => $request->denominacion,
            'cantidad' => $request->cantidad,
        ]);

       return response()->json($caja, 200);
    }

    public function show(Request $request)
    {
        
       $caja = StatusCaja::all();

       return response()->json($caja, 200);
    }

    public function pago(Request $request)
    {
        
       $cambio = array_sum($request->denominacion) - $request->monto;

       if($cambio < 0){
           $response = "El dinero ingresado no alcanza para pagar el monto ingresado.";
           return response()->json($response, 400);
       };

       if($cambio > 0){

        if(!CambioController::validarCambio(Caja::all(), $cambio)){
            $response = "No hay cambio para la denominación ingresada. 1";
           return response()->json($response, 400);
       }

       $validarDenominacion = CambioController::validarDenominacion(StatusCaja::all(), $cambio);

       if(!$validarDenominacion){
            $response = "No hay cambio para la denominación ingresada. 2";
            return response()->json($response, 400);
        }

        $result = CambioController::updateDenominacion($validarDenominacion);
       }


        foreach($request->denominacion as $den){
            $result = Caja::create([
                'denominacion' => $den,
                'cantidad' => 1
            ]);
        }

       return response()->json($result, 200);
    }

    public function truncate()
    {
        

        Caja::truncate();
        $response = array(
            "mensaje" => "La caja se ha vaciado."
        );

       return response()->json($response, 201);
    }
}
