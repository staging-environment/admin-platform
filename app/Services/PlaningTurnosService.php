<?php

namespace App\Services;

use App\Models\Empleado;
use App\Models\PlaningCuadrante;
use App\Models\PlaningTurno;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlaningTurnosService
{
    /**
     * Estructura de puestos por gasolinera
     */
    public const PUESTOS_DIURNOS = ['Puesto Único'];
    public const PUESTOS_RONDA_NORTE = ['Puesto 1', 'Puesto 2', 'Noche'];

    /**
     * Obtiene o crea el cuadrante mensual para una gasolinera
     */
    public function obtenerOCrearCuadrante(int $gasolineraCodigo, int $anio, int $mes): PlaningCuadrante
    {
        return PlaningCuadrante::firstOrCreate(
            [
                'gasolinera_codigo' => $gasolineraCodigo,
                'anio' => $anio,
                'mes' => $mes,
            ],
            [
                'estado' => 'publicado',
                'observaciones' => null,
            ]
        );
    }

    /**
     * Devuelve los empleados activos sugeridos o asignados a una estación
     */
    public function getEmpleadosEstacion(int $gasolineraCodigo): Collection
    {
        return Empleado::query()
            ->whereNull('deleted_at')
            ->where('estado', 'Alta')
            ->where(function ($q) use ($gasolineraCodigo) {
                $q->where('gasolinera_codigo', $gasolineraCodigo)
                  ->orWhereJsonContains('gasolineras_codigos', $gasolineraCodigo)
                  ->orWhereJsonContains('gasolineras_codigos', (string) $gasolineraCodigo);
            })
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Todos los empleados activos de la empresa para selector de suplencias
     */
    public function getTodosEmpleadosActivos(): Collection
    {
        return Empleado::query()
            ->whereNull('deleted_at')
            ->where('estado', 'Alta')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Determina la plantilla base (3 empleados) para una línea 4x2
     */
    public function resolverEquipoBase(int $gasolineraCodigo, string $linea = 'Puesto Único'): array
    {
        $empleados = $this->getEmpleadosEstacion($gasolineraCodigo);

        // Si la estación es Ronda Norte (Código 2) - 24 Horas
        if ($gasolineraCodigo === 2) {
            if ($linea === 'Puesto 1') {
                // Equipo Puesto 1 oficial: Mari Carmen Prieto (0), Jesús Arcos (1), Antonio Carvajal (2)
                $empMariCarmen = Empleado::where('nombre', 'like', '%CARMEN%')->where('apellidos', 'like', '%PRIETO%')->first();
                $empJesus      = Empleado::where('nombre', 'like', '%JESUS%')->where('apellidos', 'like', '%ARCOS%')->first();
                $empCarvajal   = Empleado::where('apellidos', 'like', '%CARVAJAL%')->first();

                $equipo = array_filter([$empMariCarmen, $empJesus, $empCarvajal]);
                if (count($equipo) === 3) {
                    return array_values($equipo);
                }

                return $this->buscarPorNombres($empleados, ['Prieto', 'Arcos', 'Carvajal'])
                    ?: $empleados->slice(0, 3)->values()->all();
            }

            if ($linea === 'Puesto 2') {
                // Equipo Puesto 2 oficial: Laura Linares (0), Israel Vega (1), Sergio Povea (2)
                $empLaura  = Empleado::where('nombre', 'like', '%LAURA%')->first();
                $empIsrael = Empleado::where('nombre', 'like', '%ISRAEL%')->first();
                $empSergio = Empleado::where('nombre', 'like', '%SERGIO%')->first();

                $equipo = array_filter([$empLaura, $empIsrael, $empSergio]);
                if (count($equipo) === 3) {
                    return array_values($equipo);
                }

                return $this->buscarPorNombres($empleados, ['Laura', 'Israel', 'Sergio'])
                    ?: $empleados->slice(3, 3)->values()->all();
            }

            if ($linea === 'Noche') {
                // Turno Noche oficial: Francisco Acaiña (0), Antonio Romero (1)
                $empFran   = Empleado::where('apellidos', 'like', '%ACAIÑA%')->first()
                    ?: Empleado::where('nombre', 'like', '%FRANCISCO%')->first();
                $empRomero = Empleado::where('id', 93)->first()
                    ?: Empleado::where('apellidos', 'like', '%ROMERO%')->where('nombre', 'like', '%ANTONIO%')->first();

                $equipo = array_filter([$empFran, $empRomero]);
                if (count($equipo) === 2) {
                    return array_values($equipo);
                }

                return $this->buscarPorNombres($empleados, ['Acaiña', 'Romero'])
                    ?: $empleados->slice(6, 2)->values()->all();
            }

            return $this->buscarPorNombres($empleados, ['Laura', 'Israel', 'Sergio'])
                ?: $empleados->take(3)->values()->all();
        }

        // Estaciones Diurnas (1, 3, 4)
        if ($gasolineraCodigo === 1) { // Vistalegre - Rotación oficial 4x2: Ant. Jesús (0), Miguel Ángel (1), Felisa (2)
            $empAntJesus    = Empleado::where('id', 84)->first()
                ?: Empleado::where('nombre', 'like', '%ANTONIO JESUS%')->first();
            $empMiguelAngel = Empleado::where('id', 67)->first()
                ?: Empleado::where('apellidos', 'like', '%BLANQUERO%')->first();
            $empFelisa      = Empleado::where('id', 95)->first()
                ?: Empleado::where('nombre', 'like', '%FELISA%')->first();

            $equipo = array_filter([$empAntJesus, $empMiguelAngel, $empFelisa]);
            if (count($equipo) === 3) {
                return array_values($equipo);
            }

            return $this->buscarPorNombres($empleados, ['Antonio Jesus', 'Blanquero', 'Felisa'])
                ?: $empleados->take(3)->values()->all();
        }

        if ($gasolineraCodigo === 3) { // Rodalabota - Rotación oficial 4x2: Marrufo (0), Emilio (1), Borja (2)
            $empMarrufo = Empleado::where('apellidos', 'like', '%MARRUFO%')->first();
            $empEmilio  = Empleado::where('nombre', 'like', '%EMILIO%')->first();
            $empBorja   = Empleado::where('nombre', 'like', '%BORJA%')->first();

            $equipo = array_filter([$empMarrufo, $empEmilio, $empBorja]);
            if (count($equipo) === 3) {
                return array_values($equipo);
            }

            return $this->buscarPorNombres($empleados, ['Marrufo', 'Emilio', 'Borja'])
                ?: $empleados->take(3)->values()->all();
        }

        if ($gasolineraCodigo === 4) { // Atenas - Rotación oficial 4x2: Toni (0), Joaquín (1), Zambrano (2)
            $empToni     = Empleado::where('id', 77)->first()
                ?: Empleado::where('apellidos', 'like', '%LLOMPART%')->first();
            $empJoaquin  = Empleado::where('id', 70)->first()
                ?: Empleado::where('nombre', 'like', '%JOAQUIN%')->first();
            $empZambrano = Empleado::where('id', 87)->first()
                ?: Empleado::where('apellidos', 'like', '%ZAMBRANO%')->first();

            $equipo = array_filter([$empToni, $empJoaquin, $empZambrano]);
            if (count($equipo) === 3) {
                return array_values($equipo);
            }

            return $this->buscarPorNombres($empleados, ['Toni', 'Joaquín', 'Zambrano'])
                ?: $empleados->take(3)->values()->all();
        }

        return $empleados->take(3)->values()->all();
    }

    /**
     * Busca empleados coincidentes con una lista de nombres de referencia
     */
    protected function buscarPorNombres(Collection $empleados, array $nombres): array
    {
        $encontrados = [];
        foreach ($nombres as $nombreRef) {
            $emp = $empleados->first(function ($e) use ($nombreRef) {
                $nombreCompleto = mb_strtolower($e->nombre . ' ' . $e->apellidos);
                return str_contains($nombreCompleto, mb_strtolower($nombreRef));
            });
            if ($emp) {
                $encontrados[] = $emp;
            }
        }

        // Si encontramos al menos 2, rellenamos con otros empleados si hiciera falta
        if (count($encontrados) >= 2) {
            foreach ($empleados as $e) {
                if (count($encontrados) >= count($nombres)) break;
                if (!in_array($e, $encontrados)) {
                    $encontrados[] = $e;
                }
            }
            return $encontrados;
        }

        return [];
    }

    /**
     * Calcula quién trabaja en Mañana y quién en Tarde para un día dado en el ciclo 4x2
     *
     * @param int $diaIndice Índice de días desde la fecha de referencia
     * @param array $equipo Array con 3 empleados [Emp0, Emp1, Emp2]
     * @return array ['M' => Empleado|null, 'T' => Empleado|null]
     */
    public function calcularTurnosDia4x2(int $diaIndice, array $equipo): array
    {
        if (count($equipo) < 3) {
            return [
                'M' => $equipo[0] ?? null,
                'T' => $equipo[1] ?? ($equipo[0] ?? null),
            ];
        }

        // Desfases de fase para ciclo rotativo 4x2 de 12 días
        // Emp0 (Laura / Ant. Jesús): offset 0
        // Emp1 (Israel / Miguel Ángel): offset 4
        // Emp2 (Sergio / Felisa): offset 8
        $offsets = [0 => 0, 1 => 4, 2 => 8];
        $turnos = ['M' => null, 'T' => null];

        foreach ([0, 1, 2] as $k) {
            $diaEnCiclo = ($diaIndice + $offsets[$k]) % 12;
            if ($diaEnCiclo < 0) {
                $diaEnCiclo += 12;
            }

            // 0..3: Mañana (4 días)
            // 4..5: Descanso (2 días)
            // 6..9: Tarde (4 días)
            // 10..11: Descanso (2 días)
            if ($diaEnCiclo <= 3) {
                $turnos['M'] = $equipo[$k];
            } elseif ($diaEnCiclo >= 6 && $diaEnCiclo <= 9) {
                $turnos['T'] = $equipo[$k];
            }
        }

        return $turnos;
    }

    /**
     * Genera o regenera el cuadrante teórico 4x2 para un mes completo.
     * Solo almacena los turnos de trabajo efectivos (M, T). Nunca descanso.
     */
    public function generarCuadranteTeorico(int $gasolineraCodigo, int $anio, int $mes, bool $sobrescribir = true): int
    {
        $cuadrante = $this->obtenerOCrearCuadrante($gasolineraCodigo, $anio, $mes);

        $diasEnMes = Carbon::create($anio, $mes, 1)->daysInMonth;
        // Epoch de referencia calibrado para sincronizar exactamente con el cuadrante real de Excel
        // En 2026-11-01 (día 311 desde 2025-12-25), (311 % 12) = 11:
        // Emp1 (Israel / Miguel Ángel) -> (11+4)%12 = 3 (Mañana)
        // Emp2 (Sergio / Felisa)       -> (11+8)%12 = 7 (Tarde)
        // Emp0 (Laura / Ant. Jesús)    -> (11+0)%12 = 11 (Descanso)
        $fechaRefEpoch = Carbon::create(2025, 12, 25);

        $turnosInsertados = 0;

        DB::transaction(function () use (
            $cuadrante, $gasolineraCodigo, $anio, $mes, $diasEnMes, $fechaRefEpoch, $sobrescribir, &$turnosInsertados
        ) {
            // Si es sobrescritura total, eliminamos turnos ordinarios previos manteniendo notas/sustituciones manuales si se prefiere
            if ($sobrescribir) {
                PlaningTurno::where('planing_cuadrante_id', $cuadrante->id)
                    ->where('es_sustitucion', false)
                    ->delete();
            }

            if ($gasolineraCodigo === 2) {
                // Ronda Norte (24 Horas): 2 personas en Mañana (P1 y P2), 2 en Tarde (P1 y P2), 1 en Noche
                $equipoPuesto1 = $this->resolverEquipoBase(2, 'Puesto 1');
                $equipoPuesto2 = $this->resolverEquipoBase(2, 'Puesto 2');
                $equipoNoche   = $this->resolverEquipoBase(2, 'Noche');

                for ($d = 1; $d <= $diasEnMes; $d++) {
                    $fecha = Carbon::create($anio, $mes, $d);
                    $diaIndice = $fechaRefEpoch->diffInDays($fecha, false);

                    // Puesto 1 (M y T) - rotación 4x2
                    $turnosP1 = $this->calcularTurnosDia4x2($diaIndice + 2, $equipoPuesto1);
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto 1', 'M', $turnosP1['M']?->id, $sobrescribir);
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto 1', 'T', $turnosP1['T']?->id, $sobrescribir);

                    // Puesto 2 (M y T) - rotación 4x2 calibrada 100% con Excel oficial
                    $turnosP2 = $this->calcularTurnosDia4x2($diaIndice, $equipoPuesto2);
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto 2', 'M', $turnosP2['M']?->id, $sobrescribir);
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto 2', 'T', $turnosP2['T']?->id, $sobrescribir);

                    // Turno Noche (N) - Ciclo rotativo oficial de 6 días: 4 días Fran (0), 2 días Romero (1)
                    $empNoche = null;
                    if (count($equipoNoche) >= 2) {
                        $mod6 = $diaIndice % 6;
                        if ($mod6 < 0) {
                            $mod6 += 6;
                        }
                        // En 2026-11-01 (diaIndice = 311, mod6 = 5) -> Fran
                        // En 2026-11-02 y 03 (mod6 = 0, 1) -> Romero
                        // En 2026-11-04..07 (mod6 = 2..5) -> Fran
                        $empNoche = ($mod6 === 0 || $mod6 === 1) ? $equipoNoche[1] : $equipoNoche[0];
                    } elseif (count($equipoNoche) === 1) {
                        $empNoche = $equipoNoche[0];
                    }
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Noche', 'N', $empNoche?->id, $sobrescribir);

                    $turnosInsertados += 5;
                }
            } else {
                // Estaciones Diurnas (1, 3, 4) -> 1 Puesto, 2 Turnos diarios (M, T)
                $equipo = $this->resolverEquipoBase($gasolineraCodigo, 'Puesto Único');

                $empConso = ($gasolineraCodigo === 1)
                    ? (Empleado::where('id', 88)->first() ?: Empleado::where('nombre', 'like', '%CONSOLACION%')->first())
                    : null;
                $empMiguelAngel = ($gasolineraCodigo === 1 && count($equipo) > 1) ? $equipo[1] : null;

                for ($d = 1; $d <= $diasEnMes; $d++) {
                    $fecha = Carbon::create($anio, $mes, $d);
                    $diaIndice = $fechaRefEpoch->diffInDays($fecha, false);

                    $turnosDia = $this->calcularTurnosDia4x2($diaIndice, $equipo);

                    // En Vistalegre, del 5 de noviembre en adelante de forma permanente, Consolación sustituye a Miguel Ángel según directriz oficial
                    $esConsoActiva = ($gasolineraCodigo === 1 && $empConso && $empMiguelAngel) && (
                        $anio > 2026 ||
                        ($anio === 2026 && $mes > 11) ||
                        ($anio === 2026 && $mes === 11 && $d >= 5)
                    );
                    if ($esConsoActiva) {
                        if ($turnosDia['M']?->id === $empMiguelAngel->id) {
                            $turnosDia['M'] = $empConso;
                        }
                        if ($turnosDia['T']?->id === $empMiguelAngel->id) {
                            $turnosDia['T'] = $empConso;
                        }
                    }

                    // Turno Mañana
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto Único', 'M', $turnosDia['M']?->id, $sobrescribir);

                    // Turno Tarde
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto Único', 'T', $turnosDia['T']?->id, $sobrescribir);

                    $turnosInsertados += 2;
                }
            }
        });

        return $turnosInsertados;
    }

    /**
     * Guarda un turno teórico en el cuadrante si no existe ya una sustitución manual
     */
    protected function guardarTurnoTeorico(int $cuadranteId, string $fecha, string $puesto, string $turno, ?int $empleadoId, bool $sobrescribir = true): void
    {
        $existente = PlaningTurno::where('planing_cuadrante_id', $cuadranteId)
            ->where('fecha', $fecha)
            ->where('puesto', $puesto)
            ->where('turno', $turno)
            ->first();

        if ($existente) {
            if (!$sobrescribir) {
                return;
            }

            // Si ya existe y es sustitución manual, no sobrescribimos el trabajador real, solo el titular
            if ($existente->es_sustitucion) {
                $existente->empleado_titular_id = $empleadoId;
                $existente->save();
            } else {
                $existente->empleado_id = $empleadoId;
                $existente->empleado_titular_id = $empleadoId;
                $existente->save();
            }
        } else {
            PlaningTurno::create([
                'planing_cuadrante_id' => $cuadranteId,
                'fecha' => $fecha,
                'puesto' => $puesto,
                'turno' => $turno,
                'empleado_id' => $empleadoId,
                'empleado_titular_id' => $empleadoId,
                'es_sustitucion' => false,
            ]);
        }
    }

    /**
     * Comprueba si una estación tiene empleados activos asignados para poder rotar
     */
    public function tieneEmpleados(int $gasolineraCodigo): bool
    {
        return $this->getEmpleadosEstacion($gasolineraCodigo)->count() > 0;
    }

    /**
     * Asegura que exista el cuadrante del mes y tenga turnos generados si la estación tiene empleados.
     */
    public function asegurarCuadranteMes(int $gasolineraCodigo, int $anio, int $mes, bool $sobrescribir = false): int
    {
        if (!$this->tieneEmpleados($gasolineraCodigo)) {
            return 0;
        }

        $cuadrante = $this->obtenerOCrearCuadrante($gasolineraCodigo, $anio, $mes);
        $turnosCount = PlaningTurno::where('planing_cuadrante_id', $cuadrante->id)->count();

        if ($turnosCount === 0 || $sobrescribir) {
            return $this->generarCuadranteTeorico($gasolineraCodigo, $anio, $mes, $sobrescribir);
        }

        return 0;
    }

    /**
     * Genera automáticamente los cuadrantes para los próximos X meses (por defecto 12)
     * a partir del mes actual para todas las gasolineras con empleados (o una específica).
     *
     * @param int|null $gasolineraCodigo Código de la gasolinera (1, 2, 3, 4) o null para todas
     * @param int $meses Cantidad de meses hacia adelante desde el mes actual (rolling 12-month)
     * @param bool $sobrescribir Si se deben regenerar meses que ya contengan turnos
     * @param bool $preservarMesActual Si se debe proteger el mes en curso contra sobrescrituras si ya tiene turnos
     * @return array Resumen de operaciones realizadas
     */
    public function asegurarHorizonte12Meses(?int $gasolineraCodigo = null, int $meses = 12, bool $sobrescribir = false, bool $preservarMesActual = true): array
    {
        $estaciones = $gasolineraCodigo ? [$gasolineraCodigo] : [1, 2, 3, 4];
        $mesActual = Carbon::now()->startOfMonth();
        $resultado = [
            'total_meses_generados' => 0,
            'total_turnos_generados' => 0,
            'estaciones' => [],
        ];

        foreach ($estaciones as $codigo) {
            $nombreEstacion = match ($codigo) {
                1 => 'Vistalegre',
                2 => 'Ronda Norte',
                3 => 'Rodalabota',
                4 => 'Atenas',
                default => "Estación #{$codigo}",
            };

            $empleados = $this->getEmpleadosEstacion($codigo);
            if ($empleados->isEmpty()) {
                $resultado['estaciones'][$codigo] = [
                    'nombre' => $nombreEstacion,
                    'estado' => 'sin_empleados',
                    'mensaje' => 'Omitido: sin empleados asignados',
                    'meses_procesados' => 0,
                ];
                continue;
            }

            $mesesGenerados = 0;
            $turnosGeneradosEstacion = 0;

            for ($i = 0; $i <= $meses; $i++) {
                $fechaCursor = $mesActual->copy()->addMonths($i);
                $anio = (int) $fechaCursor->format('Y');
                $mes = (int) $fechaCursor->format('n');

                $cuadrante = $this->obtenerOCrearCuadrante($codigo, $anio, $mes);
                $turnosCount = PlaningTurno::where('planing_cuadrante_id', $cuadrante->id)->count();

                // Si es el mes en curso y se solicita preservar, no se sobrescribe si ya tiene turnos
                $debeSobrescribir = $sobrescribir && !($i === 0 && $preservarMesActual && $turnosCount > 0);

                if ($turnosCount === 0 || $debeSobrescribir) {
                    $insertados = $this->generarCuadranteTeorico($codigo, $anio, $mes, $debeSobrescribir);
                    $mesesGenerados++;
                    $turnosGeneradosEstacion += $insertados;
                }
            }

            $resultado['total_meses_generados'] += $mesesGenerados;
            $resultado['total_turnos_generados'] += $turnosGeneradosEstacion;
            $resultado['estaciones'][$codigo] = [
                'nombre' => $nombreEstacion,
                'estado' => 'ok',
                'empleados_activos' => $empleados->count(),
                'meses_generados' => $mesesGenerados,
                'turnos_generados' => $turnosGeneradosEstacion,
            ];
        }

        return $resultado;
    }

    /**
     * Registra o actualiza una asignación manual / sustitución
     */
    public function asignarTurnoManual(
        int $cuadranteId,
        string $fecha,
        string $puesto,
        string $turno,
        ?int $empleadoId,
        bool $esSustitucion = false,
        ?string $motivo = null,
        ?string $notas = null
    ): PlaningTurno {
        $turnoModel = PlaningTurno::firstOrNew([
            'planing_cuadrante_id' => $cuadranteId,
            'fecha' => $fecha,
            'puesto' => $puesto,
            'turno' => $turno,
        ]);

        if (!$turnoModel->exists) {
            $turnoModel->empleado_titular_id = $empleadoId;
        }

        $turnoModel->empleado_id = $empleadoId;
        $turnoModel->es_sustitucion = $esSustitucion;
        $turnoModel->motivo_sustitucion = $esSustitucion ? $motivo : null;
        $turnoModel->notas = $notas;
        $turnoModel->save();

        return $turnoModel;
    }
}

