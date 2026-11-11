<?php

namespace Tests\Unit;

use App\Models\Empleado;
use App\Models\EmpleadoAusencia;
use App\Services\PlaningTurnosService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaningTurnosBajasFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_empleado_dado_de_baja_no_aparece_en_empleados_activos(): void
    {
        $activo = Empleado::create([
            'nombre' => 'JUAN',
            'apellidos' => 'ACTIVO',
            'email' => 'juan.activo@test.com',
            'estado' => 'Alta',
        ]);

        $baja = Empleado::create([
            'nombre' => 'PEDRO',
            'apellidos' => 'BAJA',
            'email' => 'pedro.baja@test.com',
            'estado' => 'Baja',
        ]);

        $service = app(PlaningTurnosService::class);
        $disponibles = $service->getTodosEmpleadosActivos('2026-11-10');

        $this->assertTrue($disponibles->contains('id', $activo->id));
        $this->assertFalse($disponibles->contains('id', $baja->id));
        $this->assertTrue($baja->estaDeBajaEnFecha('2026-11-10'));
        $this->assertFalse($activo->estaDeBajaEnFecha('2026-11-10'));
    }

    public function test_empleado_con_fecha_baja_contractual_es_excluido_a_partir_de_esa_fecha(): void
    {
        $empleado = Empleado::create([
            'nombre' => 'CARLOS',
            'apellidos' => 'FIN CONTRATO',
            'email' => 'carlos.fin@test.com',
            'estado' => 'Alta',
            'fecha_baja' => '2026-11-05',
        ]);

        $service = app(PlaningTurnosService::class);

        // En fecha posterior a la fecha_baja -> no debe aparecer
        $disponiblesDespues = $service->getTodosEmpleadosActivos('2026-11-06');
        $this->assertFalse($disponiblesDespues->contains('id', $empleado->id));
        $this->assertTrue($empleado->estaDeBajaEnFecha('2026-11-06'));

        // En fecha anterior a la fecha_baja -> sí está disponible
        $disponiblesAntes = $service->getTodosEmpleadosActivos('2026-11-01');
        $this->assertTrue($disponiblesAntes->contains('id', $empleado->id));
        $this->assertFalse($empleado->estaDeBajaEnFecha('2026-11-01'));
    }

    public function test_empleado_con_baja_medica_activa_no_aparece_en_sustitucion(): void
    {
        $empleado = Empleado::create([
            'nombre' => 'MARIA',
            'apellidos' => 'BAJA MEDICA',
            'email' => 'maria.baja@test.com',
            'estado' => 'Alta',
        ]);

        EmpleadoAusencia::create([
            'empleado_id' => $empleado->id,
            'tipo' => 'Bajas médicas',
            'fecha_inicio' => '2026-11-01',
            'fecha_fin' => '2026-11-10',
            'estado' => 'Aceptada',
        ]);

        $service = app(PlaningTurnosService::class);

        // Durante la baja médica (2026-11-05) -> No disponible
        $disponiblesDurante = $service->getTodosEmpleadosActivos('2026-11-05');
        $this->assertFalse($disponiblesDurante->contains('id', $empleado->id));
        $this->assertTrue($empleado->estaDeBajaEnFecha('2026-11-05'));

        // Tras el alta médica (2026-11-15) -> Disponible
        $disponiblesDespues = $service->getTodosEmpleadosActivos('2026-11-15');
        $this->assertTrue($disponiblesDespues->contains('id', $empleado->id));
        $this->assertFalse($empleado->estaDeBajaEnFecha('2026-11-15'));
    }

    public function test_empleado_con_baja_medica_abierta_no_aparece(): void
    {
        $empleado = Empleado::create([
            'nombre' => 'ROSARIO',
            'apellidos' => 'BAJA INDEFINIDA',
            'email' => 'rosario.baja@test.com',
            'estado' => 'Alta',
        ]);

        EmpleadoAusencia::create([
            'empleado_id' => $empleado->id,
            'tipo' => 'Bajas médicas',
            'fecha_inicio' => '2026-11-06',
            'fecha_fin' => null, // Abierta / sin alta
            'estado' => 'Aceptada',
        ]);

        $service = app(PlaningTurnosService::class);

        $disponibles = $service->getTodosEmpleadosActivos('2026-11-11');
        $this->assertFalse($disponibles->contains('id', $empleado->id));
        $this->assertTrue($empleado->estaDeBajaEnFecha('2026-11-11'));
    }
}
