<?php

namespace App\Console\Commands;

use App\Services\OnboardingReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerarInformeOnboardingCommand extends Command
{
    protected $signature = 'empleados:informe-onboarding {--output= : Ruta donde guardar el archivo PDF}';
    protected $description = 'Genera un informe PDF con todos los usuarios con rol Empleado que tienen el proceso de onboarding pendiente';

    public function handle(OnboardingReportService $reportService): int
    {
        $this->info("Recopilando datos de empleados con rol Empleado y onboarding pendiente...");

        $data = $reportService->getReportData();
        $stats = $data['stats'];

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total Empleados Evaluados', $stats['total_evaluados']],
                ['Onboarding Completado', $stats['total_completados']],
                ['Onboarding Pendiente', $stats['total_pendientes']],
                ['-- En Alta (Activos)', $stats['total_alta']],
                ['-- En Baja', $stats['total_baja']],
            ]
        );

        $outputPath = $this->option('output') ?: base_path('informe_empleados_onboarding_pendiente_' . Carbon::now()->format('Ymd_His') . '.pdf');

        $this->info("Generando archivo PDF con DomPDF...");
        $reportService->savePdfToFile($outputPath);

        $this->info("¡Informe generado con éxito en: {$outputPath}");

        return 0;
    }
}
