<?php

namespace App\Console\Commands;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class GenerarPropuestaPlaningPdfCommand extends Command
{
    protected $signature = 'planing:generar-propuesta-pdf {--output= : Ruta donde guardar el archivo PDF}';
    protected $description = 'Genera el PDF con la propuesta ejecutiva del módulo de Planing de turnos para Dirección';

    public function handle(): int
    {
        $this->info("Renderizando vista de propuesta ejecutiva con DomPDF...");

        $pdf = Pdf::loadView('pdf.propuesta-planing-turnos', [
            'fecha' => now()->format('d/m/Y'),
        ])->setPaper('a4', 'portrait');

        $outputPath = $this->option('output') ?: base_path('Propuesta_Modulo_Planing_Turnos_Utrecar.pdf');

        $pdf->save($outputPath);

        $this->info("¡Documento PDF generado con éxito en: {$outputPath}");

        return 0;
    }
}

