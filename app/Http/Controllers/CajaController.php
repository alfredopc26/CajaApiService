<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;

class CajaController extends Controller
{

    public function store(Request $request)
    {
        

        $caja = Caja::create
        ([
            'denominacion' => $request->denominacion,
            'cantidad' => $request->cantidad,
        ]);

       return response()->json($caja, 201);;
    }
}
