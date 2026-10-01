<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Prueba QA: Verificar disponibilidad de la página pública.
     */
    public function test_la_pagina_publica_carga_sin_errores(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        
        // Verifica que la página contenga la palabra clave del negocio
        $response->assertSeeText('Barbería');
    }
}
