<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PagosTest extends TestCase
{

    /* comprobación de que devuelva los datos correctos 
     * cuando se efectue el proceso de pago correctamente
     * y no se entregue cambio
    */
    /** @test */
    public function pago_exitoso_sin_cambio()
    {
        $payload = [
            'monto' => 150000,
            'denominacion'  => [ 100000, 50000 ]
        ];
        $this->json('put', '/api/pagar', $payload)
        ->assertStatus(200)
        ->assertJson([
            'accion' => 'Pagar',
            'detalle' => 'Pago realizado.',
            'referencia' => $payload['monto']
        ]);
    }

    /* comprobación de que devuelva los datos correctos 
     * cuando se efectue el proceso de pago correctamente
     * y se entregue cambio
    */
    /** @test */
    public function pago_exitoso_con_cambio()
    {
        $payload = [
            'monto' => 10000,
            'denominacion'  => [ 50000 ]
        ];
        $this->json('put', '/api/pagar', $payload)
        ->assertStatus(200)
        ->assertJson([
            'accion' => 'Pagar',
        ]);
    }

   /* comprobación de que devuelva la estructura correcta
     * cuando en el proceso de pago no se agregue la deonominacion correcta
    */
    /** @test */
    public function pago_no_exitoso_denominacion_incorrecta()
    {
        $payload = [
            'monto' => 10000,
            'denominacion'  => [ 70000 ]
        ];
        $this->json('put', '/api/pagar', $payload)
        ->assertStatus(400)
        ->assertJson([
            'accion' => 'Failed',
            'detalle' => 'Error de transaccion.'
        ]);
    }

    
   /* comprobación de que devuelva la estructura correcta
     * cuando en el proceso de pago no haya cambio para la denominacion
    */
    /** @test */
    public function pago_no_exitoso_sin_cambios()
    {
        $payload = [
            'monto' => 200,
            'denominacion'  => [ 100000 ]
        ];
        $this->json('put', '/api/pagar', $payload)
        ->assertStatus(400)
        ->assertJson([
            'accion' => 'Failed',
            'detalle' => 'Error de transaccion.'
        ]);
    }
    
}
