<?php

namespace App\Console\Commands;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class GenerarPropuestaMultiempresaPdfCommand extends Command
{
    protected $signature = 'multiempresa:generar-propuesta-pdf {--output= : Ruta donde guardar el archivo PDF}';
    protected $description = 'Genera el PDF con la propuesta de arquitectura multiempresa (Utrecar y Trieste) para Dirección';

    public function handle(): int
    {
        $this->info("Renderizando vista de arquitectura multiempresa con DomPDF...");

        $pdf = Pdf::loadView('pdf.propuesta-arquitectura-multiempresa', [
            'fecha' => now()->format('d/m/Y'),
        ])->setPaper('a4', 'portrait');

        $outputPath = $this->option('output') ?: base_path('Propuesta_Arquitectura_Multiempresa_Utrecar_Trieste.pdf');

        $pdf->save($outputPath);

        $this->info("¡Documento PDF generado con éxito en: {$outputPath}");

        return 0;
    }
}

