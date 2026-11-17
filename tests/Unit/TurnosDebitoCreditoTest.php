<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Empleado;
use App\Models\EmpleadoVacacion;
use App\Models\PlaningCuadrante;
use App\Models\PlaningTurno;
use App\Services\PlaningTurnosService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TurnosDebitoCreditoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'aprobacion_vacaciones_bajas', 'guard_name' => 'web']);
    }

    public function test_solicitud_sin_fecha_devolucion_genera_debito_y_credito_al_aceptar()
    {
        $empleadoA = Empleado::create([
            'nombre' => 'Empleado',
            'apellidos' => 'A',
            'email' => 'emp.a@test.com',
            'gasolinera_codigo' => 1,
            'estado' => 'Alta',
        ]);

        $empleadoB = Empleado::create([
            'nombre' => 'Empleado',
            'apellidos' => 'B',
            'email' => 'emp.b@test.com',
            'gasolinera_codigo' => 1,
            'estado' => 'Alta',
        ]);

        $fecha = Carbon::today()->addDays(5)->toDateString();

        // Cuadrante y turno de origen para A
        $cuadrante = PlaningCuadrante::create([
            'gasolinera_codigo' => 1,
            'anio' => Carbon::parse($fecha)->year,
            'mes' => Carbon::parse($fecha)->month,
        ]);

        $turnoA = PlaningTurno::create([
            'planing_cuadrante_id' => $cuadrante->id,
            'fecha' => $fecha,
            'puesto' => 'Puesto Único',
            'turno' => 'M',
            'empleado_id' => $empleadoA->id,
            'empleado_titular_id' => $empleadoA->id,
        ]);

        // A solicita a B sin fecha de devolución
        $solicitud = EmpleadoVacacion::create([
            'empleado_id' => $empleadoA->id,
            'empleado_sustituto_id' => $empleadoB->id,
            'tipo' => 'Cambio de Turno',
            'tipo_cambio' => 'solicitud_cubrir',
            'empleado_deudor_id' => $empleadoA->id,
            'empleado_acreedor_id' => $empleadoB->id,
            'fecha_inicio' => $fecha,
            'fecha_fin' => $fecha,
            'fecha_devolucion' => null,
            'dias_solicitados' => 1,
            'estado' => 'Pendiente',
            'comentario_empleado' => 'Sin fecha devolución',
        ]);

        $this->assertEquals('Pendiente', $solicitud->estado);
        $this->assertNull($solicitud->fecha_devolucion);

        // B acepta la solicitud
        $service = app(PlaningTurnosService::class);
        $solicitud->estado = 'Aceptada';
        $res = $service->aplicarCambioTurno($solicitud);

        $this->assertTrue($res);

        // Turno en Planing actualizado: B cubre a A
        $turnoActualizado = PlaningTurno::find($turnoA->id);
        $this->assertEquals($empleadoB->id, $turnoActualizado->empleado_id);
        $this->assertTrue($turnoActualizado->es_sustitucion);

        // Saldo: A debe 1 turno a B, B tiene 1 crédito con A
        $debitosA = EmpleadoVacacion::where('tipo', 'Cambio de Turno')
            ->where('empleado_deudor_id', $empleadoA->id)
            ->where('deuda_saldada', false)
            ->whereIn('estado', ['Aceptada', 'Aprobada'])
            ->count();
        $this->assertEquals(1, $debitosA);

        $creditosB = EmpleadoVacacion::where('tipo', 'Cambio de Turno')
            ->where('empleado_acreedor_id', $empleadoB->id)
            ->where('deuda_saldada', false)
            ->whereIn('estado', ['Aceptada', 'Aprobada'])
            ->count();
        $this->assertEquals(1, $creditosB);

        // Ahora B cobra el crédito solicitando que A le cubra en otra fecha
        $fechaCobro = Carbon::today()->addDays(10)->toDateString();
        $turnoB = PlaningTurno::create([
            'planing_cuadrante_id' => $cuadrante->id,
            'fecha' => $fechaCobro,
            'puesto' => 'Puesto Único',
            'turno' => 'T',
            'empleado_id' => $empleadoB->id,
            'empleado_titular_id' => $empleadoB->id,
        ]);

        $solicitudCobro = EmpleadoVacacion::create([
            'empleado_id' => $empleadoB->id,
            'empleado_sustituto_id' => $empleadoA->id,
            'tipo' => 'Cambio de Turno',
            'tipo_cambio' => 'cobro_credito',
            'empleado_deudor_id' => $empleadoA->id,
            'empleado_acreedor_id' => $empleadoB->id,
            'fecha_inicio' => $fechaCobro,
            'fecha_fin' => $fechaCobro,
            'fecha_devolucion' => null,
            'dias_solicitados' => 1,
            'estado' => 'Pendiente',
        ]);

        // A acepta el cobro
        $solicitudCobro->estado = 'Aceptada';
        $resCobro = $service->aplicarCambioTurno($solicitudCobro);
        $this->assertTrue($resCobro);

        // Planing del día de cobro: A cubre a B
        $turnoBCubierto = PlaningTurno::find($turnoB->id);
        $this->assertEquals($empleadoA->id, $turnoBCubierto->empleado_id);
        $this->assertTrue($turnoBCubierto->es_sustitucion);

        // Saldo después del cobro: la deuda queda saldada (0 turnos a débito y 0 a crédito)
        $debitosA_restantes = EmpleadoVacacion::where('tipo', 'Cambio de Turno')
            ->where('empleado_deudor_id', $empleadoA->id)
            ->where('deuda_saldada', false)
            ->whereIn('estado', ['Aceptada', 'Aprobada'])
            ->count();
        $this->assertEquals(0, $debitosA_restantes);

        $creditosB_restantes = EmpleadoVacacion::where('tipo', 'Cambio de Turno')
            ->where('empleado_acreedor_id', $empleadoB->id)
            ->where('deuda_saldada', false)
            ->whereIn('estado', ['Aceptada', 'Aprobada'])
            ->count();
        $this->assertEquals(0, $creditosB_restantes);
    }

    public function test_solicitud_con_fecha_devolucion_no_modifica_debito_ni_credito()
    {
        $empleadoA = Empleado::create([
            'nombre' => 'Empleado',
            'apellidos' => 'A',
            'email' => 'emp.a2@test.com',
            'gasolinera_codigo' => 1,
            'estado' => 'Alta',
        ]);
        $empleadoB = Empleado::create([
            'nombre' => 'Empleado',
            'apellidos' => 'B',
            'email' => 'emp.b2@test.com',
            'gasolinera_codigo' => 1,
            'estado' => 'Alta',
        ]);

        $fecha1 = Carbon::today()->addDays(5)->toDateString();
        $fecha2 = Carbon::today()->addDays(6)->toDateString();

        $cuadrante = PlaningCuadrante::create([
            'gasolinera_codigo' => 1,
            'anio' => Carbon::parse($fecha1)->year,
            'mes' => Carbon::parse($fecha1)->month,
        ]);

        $turno1 = PlaningTurno::create([
            'planing_cuadrante_id' => $cuadrante->id,
            'fecha' => $fecha1,
            'puesto' => 'Puesto Único',
            'turno' => 'M',
            'empleado_id' => $empleadoA->id,
            'empleado_titular_id' => $empleadoA->id,
        ]);

        $turno2 = PlaningTurno::create([
            'planing_cuadrante_id' => $cuadrante->id,
            'fecha' => $fecha2,
            'puesto' => 'Puesto Único',
            'turno' => 'M',
            'empleado_id' => $empleadoB->id,
            'empleado_titular_id' => $empleadoB->id,
        ]);

        $solicitud = EmpleadoVacacion::create([
            'empleado_id' => $empleadoA->id,
            'empleado_sustituto_id' => $empleadoB->id,
            'tipo' => 'Cambio de Turno',
            'tipo_cambio' => 'directo',
            'fecha_inicio' => $fecha1,
            'fecha_fin' => $fecha1,
            'fecha_devolucion' => $fecha2,
            'dias_solicitados' => 1,
            'estado' => 'Aceptada',
        ]);

        $service = app(PlaningTurnosService::class);
        $res = $service->aplicarCambioTurno($solicitud);

        $this->assertTrue($res);

        // Turno 1 cubierto por B
        $this->assertEquals($empleadoB->id, PlaningTurno::find($turno1->id)->empleado_id);
        // Turno 2 devuelto y cubierto por A
        $this->assertEquals($empleadoA->id, PlaningTurno::find($turno2->id)->empleado_id);

        // Cero turnos a débito y crédito
        $this->assertEquals(0, EmpleadoVacacion::whereNotNull('empleado_deudor_id')->where('deuda_saldada', false)->count());
        $this->assertEquals(0, EmpleadoVacacion::whereNotNull('empleado_acreedor_id')->where('deuda_saldada', false)->count());
    }

    public function test_devolucion_debito_iniciada_por_deudor_salda_deuda()
    {
        $empleadoA = Empleado::create([
            'nombre' => 'Empleado',
            'apellidos' => 'A3',
            'email' => 'emp.a3@test.com',
            'gasolinera_codigo' => 1,
            'estado' => 'Alta',
        ]);
        $empleadoB = Empleado::create([
            'nombre' => 'Empleado',
            'apellidos' => 'B3',
            'email' => 'emp.b3@test.com',
            'gasolinera_codigo' => 1,
            'estado' => 'Alta',
        ]);

        $cuadrante = PlaningCuadrante::create([
            'gasolinera_codigo' => 1,
            'anio' => 2026,
            'mes' => 11,
        ]);

        // Deuda inicial: A le debe a B
        $deudaPrevia = EmpleadoVacacion::create([
            'empleado_id' => $empleadoA->id,
            'empleado_sustituto_id' => $empleadoB->id,
            'tipo' => 'Cambio de Turno',
            'tipo_cambio' => 'solicitud_cubrir',
            'empleado_deudor_id' => $empleadoA->id,
            'empleado_acreedor_id' => $empleadoB->id,
            'fecha_inicio' => '2026-11-01',
            'fecha_fin' => '2026-11-01',
            'dias_solicitados' => 1,
            'estado' => 'Aceptada',
            'deuda_saldada' => false,
        ]);

        $this->assertEquals(1, EmpleadoVacacion::where('empleado_deudor_id', $empleadoA->id)->where('deuda_saldada', false)->count());

        // B tiene turno el 2026-11-20
        $turnoB = PlaningTurno::create([
            'planing_cuadrante_id' => $cuadrante->id,
            'fecha' => '2026-11-20',
            'puesto' => 'Puesto Único',
            'turno' => 'M',
            'empleado_id' => $empleadoB->id,
            'empleado_titular_id' => $empleadoB->id,
        ]);

        // A inicia la devolución de débito ofreciendo cubrir a B el 2026-11-20
        $solicitudDevolucion = EmpleadoVacacion::create([
            'empleado_id' => $empleadoA->id,
            'empleado_sustituto_id' => $empleadoB->id,
            'tipo' => 'Cambio de Turno',
            'tipo_cambio' => 'devolucion_debito',
            'empleado_deudor_id' => $empleadoA->id,
            'empleado_acreedor_id' => $empleadoB->id,
            'fecha_inicio' => '2026-11-20',
            'fecha_fin' => '2026-11-20',
            'dias_solicitados' => 1,
            'estado' => 'Pendiente',
        ]);

        $service = app(PlaningTurnosService::class);
        $solicitudDevolucion->estado = 'Aceptada';
        $res = $service->aplicarCambioTurno($solicitudDevolucion);

        $this->assertTrue($res);

        // Turno de B en 2026-11-20 ahora lo cubre A
        $this->assertEquals($empleadoA->id, PlaningTurno::find($turnoB->id)->empleado_id);

        // Deuda saldada
        $this->assertEquals(0, EmpleadoVacacion::where('empleado_deudor_id', $empleadoA->id)->where('deuda_saldada', false)->count());
        $this->assertEquals(0, EmpleadoVacacion::where('empleado_acreedor_id', $empleadoB->id)->where('deuda_saldada', false)->count());
    }
}
