<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\StatusCaja;

class CajaController extends Controller
{

    public function store(Request $request)
    {
        

        $caja = Caja::create
        ([
            'denominacion' => $request->denominacion,
            'cantidad' => $request->cantidad,
        ]);

       return response()->json($caja, 201);
    }

    public function show(Request $request)
    {
        
       $caja = StatusCaja::all();

       return response()->json($caja, 201);
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
