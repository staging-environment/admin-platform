<?php

namespace Tests\Unit;

use App\Models\Empleado;
use App\Models\PlaningCuadrante;
use App\Models\PlaningTurno;
use App\Services\PlaningTurnosService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaningTurnosColorTurnoTest extends TestCase
{
    use RefreshDatabase;

    public function test_colores_turnos_esquema_estandar_vistalegre(): void
    {
        $service = app(PlaningTurnosService::class);

        $empAntJesus = Empleado::create([
            'nombre' => 'ANTONIO JESUS',
            'apellidos' => 'RODRIGUEZ ROMERO',
            'email' => 'ant.jesus@test.com',
            'estado' => 'Alta',
        ]);

        $empFelisa = Empleado::create([
            'nombre' => 'FELISA',
            'apellidos' => 'DIAZ',
            'email' => 'felisa@test.com',
            'estado' => 'Alta',
        ]);

        $empConso = Empleado::create([
            'nombre' => 'CONSOLACION',
            'apellidos' => 'ZAPATA',
            'email' => 'conso@test.com',
            'estado' => 'Alta',
        ]);

        // Vistalegre (Estación 1)
        $colorTurno1 = $service->resolverColorTurno(1, 'Puesto Único', 'M', $empAntJesus->id);
        $colorTurno2 = $service->resolverColorTurno(1, 'Puesto Único', 'M', $empConso->id);
        $colorTurno3 = $service->resolverColorTurno(1, 'Puesto Único', 'T', $empFelisa->id);

        $this->assertEquals('#ef4444', $colorTurno1['bg'], 'Turno 1 debe ser Rojo');
        $this->assertEquals('#a9d08e', $colorTurno2['bg'], 'Turno 2 debe ser Verde');
        $this->assertEquals('#8ea9db', $colorTurno3['bg'], 'Turno 3 debe ser Azul');
    }

    public function test_colores_turnos_ronda_norte_puesto_2_usa_esquema_estandar(): void
    {
        $service = app(PlaningTurnosService::class);

        $empSergio = Empleado::create([
            'nombre' => 'SERGIO',
            'apellidos' => 'POVEA',
            'email' => 'sergio@test.com',
            'estado' => 'Alta',
        ]);

        $empIsrael = Empleado::create([
            'nombre' => 'ISRAEL',
            'apellidos' => 'VEGA',
            'email' => 'israel@test.com',
            'estado' => 'Alta',
        ]);

        $empLaura = Empleado::create([
            'nombre' => 'LAURA',
            'apellidos' => 'LINARES',
            'email' => 'laura@test.com',
            'estado' => 'Alta',
        ]);

        // Ronda Norte Puesto 2 debe usar Rojo, Verde y Azul
        $colorSergio = $service->resolverColorTurno(2, 'Puesto 2', 'M', $empSergio->id);
        $colorIsrael = $service->resolverColorTurno(2, 'Puesto 2', 'T', $empIsrael->id);
        $colorLaura  = $service->resolverColorTurno(2, 'Puesto 2', 'M', $empLaura->id);

        $this->assertEquals('#ef4444', $colorSergio['bg'], 'Sergio en P2 debe ser Rojo');
        $this->assertEquals('#a9d08e', $colorIsrael['bg'], 'Israel en P2 debe ser Verde');
        $this->assertEquals('#8ea9db', $colorLaura['bg'], 'Laura en P2 debe ser Azul');
    }

    public function test_excepcion_ronda_norte_puesto_1_y_noche_mantiene_colores_especificos(): void
    {
        $service = app(PlaningTurnosService::class);

        $empMariCarmen = Empleado::create([
            'nombre' => 'MARIA DEL CARMEN',
            'apellidos' => 'PRIETO',
            'email' => 'maricarmen@test.com',
            'estado' => 'Alta',
        ]);

        $empCarvajal = Empleado::create([
            'nombre' => 'ANTONIO',
            'apellidos' => 'CARVAJAL',
            'email' => 'carvajal@test.com',
            'estado' => 'Alta',
        ]);

        $empJesus = Empleado::create([
            'nombre' => 'JESUS',
            'apellidos' => 'ARCOS',
            'email' => 'jesus@test.com',
            'estado' => 'Alta',
        ]);

        $empFran = Empleado::create([
            'nombre' => 'FRANCISCO',
            'apellidos' => 'ACAIÑA',
            'email' => 'fran@test.com',
            'estado' => 'Alta',
        ]);

        $empRomero = Empleado::create([
            'nombre' => 'ANTONIO',
            'apellidos' => 'ROMERO MARTIN',
            'email' => 'romero@test.com',
            'estado' => 'Alta',
        ]);

        // Puesto 1
        $colorMariCarmen = $service->resolverColorTurno(2, 'Puesto 1', 'M', $empMariCarmen->id);
        $colorCarvajal   = $service->resolverColorTurno(2, 'Puesto 1', 'T', $empCarvajal->id);
        $colorJesus      = $service->resolverColorTurno(2, 'Puesto 1', 'M', $empJesus->id);

        // Noche
        $colorFran   = $service->resolverColorTurno(2, 'Noche', 'N', $empFran->id);
        $colorRomero = $service->resolverColorTurno(2, 'Noche', 'N', $empRomero->id);

        $this->assertEquals('#8ea9db', $colorMariCarmen['bg'], 'Mari Carmen en P1 debe ser Azul');
        $this->assertEquals('#a9d08e', $colorCarvajal['bg'], 'Carvajal en P1 debe ser Verde');
        $this->assertEquals('#ed7d31', $colorJesus['bg'], 'Jesús en P1 debe ser Naranja');
        $this->assertEquals('#7030a0', $colorFran['bg'], 'Fran en Noche debe ser Púrpura');
        $this->assertEquals('#ffff00', $colorRomero['bg'], 'Romero en Noche debe ser Amarillo');
    }

    public function test_sustitucion_puntual_mantiene_color_fondo_del_turno_y_solo_cambia_empleado(): void
    {
        $cuadrante = PlaningCuadrante::create([
            'gasolinera_codigo' => 1, // Vistalegre
            'anio' => 2026,
            'mes' => 11,
            'estado' => 'publicado',
        ]);

        $titularFelisa = Empleado::create([
            'nombre' => 'FELISA',
            'apellidos' => 'DIAZ',
            'email' => 'felisa.titular@test.com',
            'estado' => 'Alta',
        ]);

        $sustitutoCampos = Empleado::create([
            'nombre' => 'ANTONIO',
            'apellidos' => 'CAMPOS',
            'email' => 'campos.sustituto@test.com',
            'estado' => 'Alta',
        ]);

        // Turno donde Felisa es titular (Turno 3 - Azul #8ea9db)
        $turno = PlaningTurno::create([
            'planing_cuadrante_id' => $cuadrante->id,
            'fecha' => '2026-11-03',
            'puesto' => 'Puesto Único',
            'turno' => 'T',
            'empleado_id' => $titularFelisa->id,
            'empleado_titular_id' => $titularFelisa->id,
            'es_sustitucion' => false,
        ]);

        // Antes de la sustitución: color Azul del turno
        $this->assertEquals('#8ea9db', $turno->estilo_color['bg']);
        $this->assertEquals('FELISA', $turno->empleado->nombre_corto);

        // Se realiza una sustitución puntual por Antonio Campos
        $turno->empleado_id = $sustitutoCampos->id;
        $turno->es_sustitucion = true;
        $turno->motivo_sustitucion = 'Baja médica de Felisa';
        $turno->save();
        $turno->refresh();

        // El empleado asignado ahora es CAMPOS
        $this->assertEquals('CAMPOS', $turno->empleado->nombre_corto);

        // ¡REGLA CRÍTICA!: El color de fondo del turno NO DEBE CAMBIAR, sigue siendo el Azul (#8ea9db) del turno de Felisa
        $this->assertEquals('#8ea9db', $turno->estilo_color['bg'], 'El color de fondo debe mantenerse invariable tras sustitución puntual');
    }

    public function test_leyenda_de_turnos_para_estaciones_diurnas_contiene_rojo_verde_azul(): void
    {
        $service = app(PlaningTurnosService::class);
        $leyendaVistalegre = $service->obtenerLeyendaTurnos(1);

        $this->assertCount(3, $leyendaVistalegre);
        $this->assertEquals('Rojo', $leyendaVistalegre[0]['color_nombre']);
        $this->assertEquals('Verde', $leyendaVistalegre[1]['color_nombre']);
        $this->assertEquals('Azul', $leyendaVistalegre[2]['color_nombre']);
    }
}

