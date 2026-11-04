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

        // Si la estación es Ronda Norte (Código 2)
        if ($gasolineraCodigo === 2) {
            if ($linea === 'Puesto 1') {
                return $this->buscarPorNombres($empleados, ['Mari Carmen', 'Carvajal', 'Jesús'])
                    ?: $empleados->slice(0, 3)->values()->all();
            }
            if ($linea === 'Puesto 2') {
                return $this->buscarPorNombres($empleados, ['Laura', 'Sergio', 'Israel'])
                    ?: $empleados->slice(3, 3)->values()->all();
            }
            if ($linea === 'Noche') {
                return $this->buscarPorNombres($empleados, ['Fran', 'Romero'])
                    ?: $empleados->slice(6, 2)->values()->all();
            }
        }

        // Estaciones Diurnas (1, 3, 4)
        if ($gasolineraCodigo === 1) { // Vistalegre
            $equipo = $this->buscarPorNombres($empleados, ['Jesús', 'Felisa', 'Miguel Ángel']);
            return $equipo ?: $empleados->take(3)->values()->all();
        }

        if ($gasolineraCodigo === 3) { // Rodalabota
            $equipo = $this->buscarPorNombres($empleados, ['Marrufo', 'Borja', 'Emilio']);
            return $equipo ?: $empleados->take(3)->values()->all();
        }

        if ($gasolineraCodigo === 4) { // Atenas
            $equipo = $this->buscarPorNombres($empleados, ['Toni', 'Zambrano', 'Joaquín']);
            return $equipo ?: $empleados->take(3)->values()->all();
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

        // Desfases de fase para ciclo de 12 días
        // Emp0: offset 0
        // Emp1: offset 8
        // Emp2: offset 4
        $offsets = [0 => 0, 1 => 8, 2 => 4];
        $turnos = ['M' => null, 'T' => null];

        foreach ([0, 1, 2] as $k) {
            $diaEnCiclo = ($diaIndice + $offsets[$k]) % 12;
            if ($diaEnCiclo < 0) {
                $diaEnCiclo += 12;
            }

            // 0..3: Mañana
            // 4..5: Descanso
            // 6..9: Tarde
            // 10..11: Descanso
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
     * Solo almacena los turnos de trabajo efectivos (M, T, N). Nunca descanso.
     */
    public function generarCuadranteTeorico(int $gasolineraCodigo, int $anio, int $mes, bool $sobrescribir = true): int
    {
        $cuadrante = $this->obtenerOCrearCuadrante($gasolineraCodigo, $anio, $mes);

        $diasEnMes = Carbon::create($anio, $mes, 1)->daysInMonth;
        $fechaRefEpoch = Carbon::create(2026, 1, 1); // Epoch de referencia matemática

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
                // Ronda Norte (24H)
                $equipoPuesto1 = $this->resolverEquipoBase(2, 'Puesto 1');
                $equipoPuesto2 = $this->resolverEquipoBase(2, 'Puesto 2');
                $equipoNoche   = $this->resolverEquipoBase(2, 'Noche');

                for ($d = 1; $d <= $diasEnMes; $d++) {
                    $fecha = Carbon::create($anio, $mes, $d);
                    $diaIndice = $fechaRefEpoch->diffInDays($fecha, false);

                    // Puesto 1 (M y T)
                    $turnosP1 = $this->calcularTurnosDia4x2($diaIndice, $equipoPuesto1);
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto 1', 'M', $turnosP1['M']?->id);
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto 1', 'T', $turnosP1['T']?->id);

                    // Puesto 2 (M y T) - desfase de 2 días para no sincronizar exactamente con Puesto 1
                    $turnosP2 = $this->calcularTurnosDia4x2($diaIndice + 2, $equipoPuesto2);
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto 2', 'M', $turnosP2['M']?->id);
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto 2', 'T', $turnosP2['T']?->id);

                    // Turno Noche (N) - Alternancia entre 2 personas
                    $empNoche = null;
                    if (count($equipoNoche) > 0) {
                        $idxNoche = (int) floor($diaIndice / 2) % count($equipoNoche);
                        $empNoche = $equipoNoche[$idxNoche] ?? $equipoNoche[0];
                    }
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Noche', 'N', $empNoche?->id);

                    $turnosInsertados += 5;
                }
            } else {
                // Estaciones Diurnas (1, 3, 4) -> 1 Puesto, 2 Turnos diarios (M, T)
                $equipo = $this->resolverEquipoBase($gasolineraCodigo, 'Puesto Único');

                for ($d = 1; $d <= $diasEnMes; $d++) {
                    $fecha = Carbon::create($anio, $mes, $d);
                    $diaIndice = $fechaRefEpoch->diffInDays($fecha, false);

                    $turnosDia = $this->calcularTurnosDia4x2($diaIndice, $equipo);

                    // Turno Mañana
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto Único', 'M', $turnosDia['M']?->id);

                    // Turno Tarde
                    $this->guardarTurnoTeorico($cuadrante->id, $fecha->toDateString(), 'Puesto Único', 'T', $turnosDia['T']?->id);

                    $turnosInsertados += 2;
                }
            }
        });

        return $turnosInsertados;
    }

    /**
     * Guarda un turno teórico en el cuadrante si no existe ya una sustitución manual
     */
    protected function guardarTurnoTeorico(int $cuadranteId, string $fecha, string $puesto, string $turno, ?int $empleadoId): void
    {
        $existente = PlaningTurno::where('planing_cuadrante_id', $cuadranteId)
            ->where('fecha', $fecha)
            ->where('puesto', $puesto)
            ->where('turno', $turno)
            ->first();

        if ($existente) {
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
