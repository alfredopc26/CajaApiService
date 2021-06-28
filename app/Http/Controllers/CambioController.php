<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Http\Controllers\LogsController;

class CambioController extends Controller
{
    /** 
     * Validar si hay cambio para entregar o no hay.
     * @return boolean
     * 
    */
    public static function validarCambio($caja, $cambio)
    {
        
        foreach($caja as $registro){
            $monto[] = $registro->cantidad * $registro->denominacion;
        }

        $monto = array_sum($monto);

        if($monto < $cambio){
            return false;
        }

       return true;
    }

    /** 
     * Validar si dentro de las denominaciones se puede entregar el cambio.
     * 
     * @return array|int
     * 
     * 
    */
    public static function validarDenominacion($caja, $cambio)
    {
        $sumDenominacion = 0;
        foreach($caja as $registro){
           
            if($registro->denominacion == $cambio){
               return $registro->denominacion;
            }

            if($registro->denominacion < $cambio){
                for($i = 1; $i <= $registro->total; $i++ ){

                    $selectDenominacion[] = $registro->denominacion;
                    $sumDenominacion = array_sum($selectDenominacion);
                    if($sumDenominacion > $cambio){
                        array_pop($selectDenominacion);
                        break;
                    }

                    if( $sumDenominacion == $cambio){
                        return $selectDenominacion;
                    }

                    
                }
            }

        }

       return false;
    }


    /** 
     * Actualiza las cantidades en la BD cuando se entrega un cambio
     * @return void
     * 
    */
    public static function updateDenominacion($validacionDenominacion)
    {
        if(is_array($validacionDenominacion)){
            $contar = array_count_values($validacionDenominacion);
            $unicoValor = array_unique($validacionDenominacion);

            foreach($unicoValor as $denominacion){

                CambioController::quitarCambio($denominacion, $contar[$denominacion]);
                $data = LogsController::prepare('Cambio', $contar[$denominacion] * $denominacion);
            }

        }else{
            CambioController::quitarCambio($validacionDenominacion, 1);
            $data = LogsController::prepare('Borrar', $validacionDenominacion);
        }
       

        LogsController::save($data);
             
    }

    /** 
     * Calcula valor excedente de la cantidad a cambiar
     * @return boolean
     * 
    */
    private static function calcularExceded($denominacion, $cantidad, $cambio, $selectDenominacion = 0)
    {
        $exceded = false;

        $calc = ($denominacion * $cantidad);

        if($calc > $cambio){
            $exceded = true;
        }

        return $exceded;
    }

    /** 
     * Elimina los registros con denominaciones cuando estas ya no tienen cantidades
     * @return void
     * 
    */
    private static function quitarCambio($denominacion, $cantidad)
    {

        $denominacion = Caja::where('denominacion', $denominacion )->first();
        $calculo = $denominacion->cantidad - $cantidad;
        if($calculo > 0){
            $query = Caja::where('id', $denominacion->id )
                   ->update(['cantidad' => $calculo ]);

            $data = LogsController::prepare('Actualizar', $denominacion->id);
            LogsController::save($data);
            return $query;
        }

        if($calculo < 0){

            $query = Caja::where('id', $denominacion->id )
                ->delete();
            $data = LogsController::prepare('Borrar', $denominacion->id);
            LogsController::save($data);
            CambioController::updateDenominacion($denominacion, abs($calculo));
            
        }

        $query = Caja::where('id', $denominacion->id )
                ->delete();

        $data = LogsController::prepare('Borrar', $denominacion->id);
        LogsController::save($data);
    }

}
