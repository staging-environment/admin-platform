<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\EmpleadoFichaje;
use Carbon\Carbon;

class EmpleadoFichajeHorasTest extends TestCase
{
    public function test_calcula_horas_en_turno_diurno_mismo_dia(): void
    {
        $fichaje = new EmpleadoFichaje([
            'fecha' => '2026-11-05',
            'hora_entrada' => '08:00:00',
            'hora_salida' => '16:30:00',
        ]);

        $this->assertEquals(510, $fichaje->minutos_trabajados);
        $this->assertEquals('8h 30m', $fichaje->horas_trabajadas_formateadas);
        $this->assertEquals('8h 30m', $fichaje->horas_trabajadas);
    }

    public function test_calcula_horas_en_turno_nocturno_que_cruza_medianoche(): void
    {
        // Caso exacto de la incidencia del usuario: 22:28 a 06:57
        $fichaje1 = new EmpleadoFichaje([
            'fecha' => '2026-11-05',
            'hora_entrada' => '22:28:00',
            'hora_salida' => '06:57:00',
            'server_checkin_at' => Carbon::parse('2026-11-05 22:28:49'),
            'server_checkout_at' => Carbon::parse('2026-11-06 06:57:33'),
        ]);

        $this->assertEquals(509, $fichaje1->minutos_trabajados);
        $this->assertEquals('8h 29m', $fichaje1->horas_trabajadas_formateadas);

        // Caso 2: 22:28 a 07:38
        $fichaje2 = new EmpleadoFichaje([
            'fecha' => '2026-11-04',
            'hora_entrada' => '22:28:00',
            'hora_salida' => '07:38:00',
            'server_checkin_at' => Carbon::parse('2026-11-04 22:28:58'),
            'server_checkout_at' => Carbon::parse('2026-11-05 07:39:05'),
        ]);

        $this->assertEquals(550, $fichaje2->minutos_trabajados);
        $this->assertEquals('9h 10m', $fichaje2->horas_trabajadas_formateadas);

        // Caso 3: 22:42 a 06:56
        $fichaje3 = new EmpleadoFichaje([
            'fecha' => '2026-11-01',
            'hora_entrada' => '22:42:00',
            'hora_salida' => '06:56:00',
            'server_checkin_at' => Carbon::parse('2026-11-01 22:42:53'),
            'server_checkout_at' => Carbon::parse('2026-11-02 06:56:14'),
        ]);

        $this->assertEquals(494, $fichaje3->minutos_trabajados);
        $this->assertEquals('8h 14m', $fichaje3->horas_trabajadas_formateadas);
    }

    public function test_fichaje_incompleto_devuelve_null_y_guion(): void
    {
        $fichaje = new EmpleadoFichaje([
            'fecha' => '2026-11-06',
            'hora_entrada' => '08:00:00',
            'hora_salida' => null,
        ]);

        $this->assertNull($fichaje->minutos_trabajados);
        $this->assertEquals('-', $fichaje->horas_trabajadas_formateadas);
    }
}
