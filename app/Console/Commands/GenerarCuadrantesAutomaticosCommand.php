<?php

namespace App\Console\Commands;

use App\Services\PlaningTurnosService;
use Illuminate\Console\Command;

class GenerarCuadrantesAutomaticosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planing:generar-cuadrantes 
                            {--estacion= : Código específico de estación (1=Vistalegre, 2=Ronda Norte, 3=Rodalabota, 4=Atenas)}
                            {--meses=12 : Horizonte de meses a calcular hacia adelante desde el mes actual}
                            {--force : Sobrescribir turnos ordinarios existentes (conserva sustituciones manuales)}
                            {--incluir-actual : Sobrescribir también el mes en curso si se usa --force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calcula y asegura automáticamente los turnos de cuadrante 4x2 para los próximos 12 meses en todas las estaciones con empleados';

    /**
     * Execute the console command.
     */
    public function handle(PlaningTurnosService $service): int
    {
        $estacion = $this->option('estacion') ? (int) $this->option('estacion') : null;
        $meses = max(1, (int) ($this->option('meses') ?: 12));
        $force = (bool) $this->option('force');
        $preservarActual = ! (bool) $this->option('incluir-actual');

        $this->info("Iniciando generación automática de cuadrantes (Horizonte: {$meses} meses, Forzar: " . ($force ? 'Sí' : 'No') . ", Preservar Mes Actual: " . ($preservarActual ? 'Sí' : 'No') . ")...");

        $resultado = $service->asegurarHorizonte12Meses($estacion, $meses, $force, $preservarActual);

        $filasTabla = [];
        foreach ($resultado['estaciones'] as $codigo => $info) {
            $filasTabla[] = [
                'ID' => $codigo,
                'Estación' => $info['nombre'],
                'Estado' => $info['estado'] === 'ok' ? '<fg=green>OK</>' : '<fg=yellow>Omitido</>',
                'Empleados' => $info['empleados_activos'] ?? 0,
                'Meses Gen.' => $info['meses_generados'] ?? 0,
                'Turnos Gen.' => $info['turnos_generados'] ?? 0,
                'Detalle' => $info['mensaje'] ?? 'Completado',
            ];
        }

        $this->table(['ID', 'Estación', 'Estado', 'Empleados', 'Meses Gen.', 'Turnos Gen.', 'Detalle'], $filasTabla);

        $this->info("✓ Proceso completado con éxito: {$resultado['total_meses_generados']} meses procesados ({$resultado['total_turnos_generados']} turnos asignados).");

        return Command::SUCCESS;
    }
}
