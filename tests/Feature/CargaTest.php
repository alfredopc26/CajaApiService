<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CargaTest extends TestCase
{

/* comprobación de que devueva la estructura correcta cuando se efectue el proceso de carga correctamente */
    /** @test */
    public function carga_denominaciones_correctas()
    {
        $payload = [
            'denominacion' => 20000,
            'cantidad'  => 3
        ];
        $this->json('post', '/api/carga', $payload)
        ->assertStatus(200)
        ->assertJsonStructure([
            'accion',
            'detalle',
            'referencia' => [
                    'denominacion',
                    'cantidad',
                    'updated_at',
                    'created_at',
                    'id'
             ]
            ]);
    }

    /* comprobación de que devueva los datos correctos cuando se efectue el proceso de carga incorrectamente por denominacion */
    /** @test */
    public function carga_denominaciones_incorrectas()
    {
        $payload = [
            'denominacion' => 30000,
            'cantidad'  => 3
        ];
        $this->json('post', '/api/carga', $payload)
        ->assertStatus(400)
        ->assertJson([
            'accion' => 'Failed',
            'detalle' => 'Error de transaccion.',
            'referencia' => "La denominación no es válida. DEN: ".$payload['denominacion']
        ]);
    }

    /* comprobación de que devueva los datos correctos cuando se efectue el proceso de carga incorrectamente por cantidad*/
        /** @test */
        public function carga_cantidades_incorrectas()
        {
            $payload = [
                'denominacion' => 20000,
                'cantidad'  => 100.4
            ];
            $this->json('post', '/api/carga', $payload)
            ->assertStatus(400)
            ->assertJson([
                'accion' => 'Failed',
                'detalle' => 'Error de transaccion.',
                'referencia' => "La cantidad no es válida. CANT: ".$payload['cantidad']
            ]);
        }
}
