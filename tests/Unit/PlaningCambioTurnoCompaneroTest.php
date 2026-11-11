<?php

namespace Tests\Unit;

use App\Models\Empleado;
use App\Models\EmpleadoVacacion;
use App\Models\PlaningCuadrante;
use App\Models\PlaningTurno;
use App\Services\PlaningTurnosService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaningCambioTurnoCompaneroTest extends TestCase
{
    use RefreshDatabase;

    public function test_creacion_solicitud_cambio_turno_y_alerta_en_companero(): void
    {
        $solicitante = Empleado::create([
            'nombre' => 'ANTONIO JESUS',
            'apellidos' => 'RODRIGUEZ ROMERO',
            'email' => 'ant.jesus@test.com',
            'estado' => 'Alta',
            'gasolinera_codigo' => 1,
        ]);

        $companero = Empleado::create([
            'nombre' => 'FELISA',
            'apellidos' => 'DIAZ',
            'email' => 'felisa@test.com',
            'estado' => 'Alta',
            'gasolinera_codigo' => 1,
        ]);

        $fechaSolicitada = Carbon::now()->addDays(5)->toDateString();
        $fechaDevolucion = Carbon::now()->addDays(7)->toDateString();

        // 1. Crear solicitud de cambio de turno
        $solicitud = EmpleadoVacacion::create([
            'empleado_id' => $solicitante->id,
            'tipo' => 'Cambio de Turno',
            'fecha_inicio' => $fechaSolicitada,
            'fecha_fin' => $fechaSolicitada,
            'fecha_devolucion' => $fechaDevolucion,
            'empleado_sustituto_id' => $companero->id,
            'dias_solicitados' => 1,
            'estado' => 'Pendiente',
            'comentario_empleado' => 'Tengo cita médica, te devuelvo el turno el jueves.',
            'respuesta_vista_solicitante' => false,
        ]);

        $this->assertEquals('Cambio de Turno', $solicitud->tipo);
        $this->assertEquals('Pendiente', $solicitud->estado);
        $this->assertEquals($companero->id, $solicitud->empleado_sustituto_id);

        // 2. Verificar que el compañero tiene la solicitud pendiente
        $pendientesCompanero = EmpleadoVacacion::where('tipo', 'Cambio de Turno')
            ->where('empleado_sustituto_id', $companero->id)
            ->where('estado', 'Pendiente')
            ->get();

        $this->assertTrue($pendientesCompanero->contains('id', $solicitud->id));

        // 3. Verificar que el solicitante NO la tiene como recibida
        $pendientesSolicitante = EmpleadoVacacion::where('tipo', 'Cambio de Turno')
            ->where('empleado_sustituto_id', $solicitante->id)
            ->where('estado', 'Pendiente')
            ->get();

        $this->assertFalse($pendientesSolicitante->contains('id', $solicitud->id));
    }

    public function test_companero_deniega_cambio_y_solicitante_recibe_alerta(): void
    {
        $solicitante = Empleado::create([
            'nombre' => 'ANTONIO JESUS',
            'apellidos' => 'RODRIGUEZ',
            'email' => 'ant.jesus2@test.com',
            'estado' => 'Alta',
            'gasolinera_codigo' => 1,
        ]);

        $companero = Empleado::create([
            'nombre' => 'FELISA',
            'apellidos' => 'DIAZ',
            'email' => 'felisa2@test.com',
            'estado' => 'Alta',
            'gasolinera_codigo' => 1,
        ]);

        $solicitud = EmpleadoVacacion::create([
            'empleado_id' => $solicitante->id,
            'tipo' => 'Cambio de Turno',
            'fecha_inicio' => Carbon::now()->addDays(5)->toDateString(),
            'fecha_fin' => Carbon::now()->addDays(5)->toDateString(),
            'fecha_devolucion' => Carbon::now()->addDays(7)->toDateString(),
            'empleado_sustituto_id' => $companero->id,
            'dias_solicitados' => 1,
            'estado' => 'Pendiente',
            'respuesta_vista_solicitante' => false,
        ]);

        // Simular que el compañero deniega
        $solicitud->estado = 'Rechazada';
        $solicitud->comentario_aprobador = 'No puedo ese día por motivos personales';
        $solicitud->respuesta_vista_solicitante = false;
        $solicitud->save();

        // El solicitante debe tener una alerta pendiente de ver
        $alertas = EmpleadoVacacion::where('tipo', 'Cambio de Turno')
            ->where('empleado_id', $solicitante->id)
            ->whereIn('estado', ['Aceptada', 'Rechazada'])
            ->where('respuesta_vista_solicitante', false)
            ->get();

        $this->assertTrue($alertas->contains('id', $solicitud->id));
        $this->assertEquals('Rechazada', $alertas->first()->estado);

        // Cuando el solicitante pulsa en cerrar aviso
        $solicitud->respuesta_vista_solicitante = true;
        $solicitud->save();

        $alertasRestantes = EmpleadoVacacion::where('tipo', 'Cambio de Turno')
            ->where('empleado_id', $solicitante->id)
            ->where('respuesta_vista_solicitante', false)
            ->count();

        $this->assertEquals(0, $alertasRestantes);
    }

    public function test_companero_acepta_cambio_y_planing_se_actualiza_con_colores_preservados(): void
    {
        $service = app(PlaningTurnosService::class);

        $solicitante = Empleado::create([
            'nombre' => 'ANTONIO JESUS',
            'apellidos' => 'RODRIGUEZ',
            'email' => 'ant.jesus3@test.com',
            'estado' => 'Alta',
            'gasolinera_codigo' => 1,
        ]);

        $companero = Empleado::create([
            'nombre' => 'FELISA',
            'apellidos' => 'DIAZ',
            'email' => 'felisa3@test.com',
            'estado' => 'Alta',
            'gasolinera_codigo' => 1,
        ]);

        $c = Carbon::now()->addMonth();
        $fechaSolicitada = Carbon::create($c->year, $c->month, 10)->toDateString();
        $fechaDevolucion = Carbon::create($c->year, $c->month, 12)->toDateString();

        $cuadrante = PlaningCuadrante::create([
            'gasolinera_codigo' => 1,
            'anio' => $c->year,
            'mes' => $c->month,
            'estado' => 'publicado',
        ]);

        // Turno del solicitante en el día 10 (Turno 1 - Rojo #ef4444)
        $turno1 = PlaningTurno::create([
            'planing_cuadrante_id' => $cuadrante->id,
            'fecha' => $fechaSolicitada,
            'puesto' => 'Puesto Único',
            'turno' => 'M',
            'empleado_id' => $solicitante->id,
            'empleado_titular_id' => $solicitante->id,
            'es_sustitucion' => false,
        ]);

        // Turno del compañero en el día 12 (Turno 3 - Azul #8ea9db)
        $turno2 = PlaningTurno::create([
            'planing_cuadrante_id' => $cuadrante->id,
            'fecha' => $fechaDevolucion,
            'puesto' => 'Puesto Único',
            'turno' => 'T',
            'empleado_id' => $companero->id,
            'empleado_titular_id' => $companero->id,
            'es_sustitucion' => false,
        ]);

        // Solicitud de cambio
        $solicitud = EmpleadoVacacion::create([
            'empleado_id' => $solicitante->id,
            'tipo' => 'Cambio de Turno',
            'fecha_inicio' => $fechaSolicitada,
            'fecha_fin' => $fechaSolicitada,
            'fecha_devolucion' => $fechaDevolucion,
            'empleado_sustituto_id' => $companero->id,
            'dias_solicitados' => 1,
            'estado' => 'Pendiente',
            'respuesta_vista_solicitante' => false,
        ]);

        // El compañero acepta y se aplica al Planing
        $solicitud->estado = 'Aceptada';
        $solicitud->save();

        $aplicado = $service->aplicarCambioTurno($solicitud);
        $this->assertTrue($aplicado);

        $turno1->refresh();
        $turno2->refresh();

        // Verificaciones Día Solicitado:
        // El compañero trabaja el turno del solicitante
        $this->assertEquals($companero->id, $turno1->empleado_id);
        $this->assertTrue($turno1->es_sustitucion);
        $this->assertStringContainsString('Cambio de turno', $turno1->motivo_sustitucion);
        // ¡REGLA CRÍTICA!: El color de fondo del turno sigue siendo el del titular (Rojo #ef4444)
        $this->assertEquals('#ef4444', $turno1->estilo_color['bg']);

        // Verificaciones Día Devolución:
        // El solicitante devuelve el día y trabaja el turno del compañero
        $this->assertEquals($solicitante->id, $turno2->empleado_id);
        $this->assertTrue($turno2->es_sustitucion);
        $this->assertStringContainsString('Devolución de cambio', $turno2->motivo_sustitucion);
        // ¡REGLA CRÍTICA!: El color de fondo del turno sigue siendo el del titular (Azul #8ea9db)
        $this->assertEquals('#8ea9db', $turno2->estilo_color['bg']);
    }
}
