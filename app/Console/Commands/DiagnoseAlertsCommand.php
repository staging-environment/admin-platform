<?php

namespace App\Console\Commands;

use App\Models\Empleado;
use Illuminate\Console\Command;

class DiagnoseAlertsCommand extends Command
{
    protected $signature = 'empleados:diagnose-alerts {--update}';
    protected $description = 'Diagnose and recalculate employee alerts and contracts';

    public function handle(): int
    {
        $this->info("=== EMPLEADOS EN BAJA Y SUS ALERTAS ===");
        $bajas = Empleado::where('estado', 'Baja')->get();
        foreach ($bajas as $e) {
            $e->actualizarAlertas();
            $alertas = $e->alertas()->pluck('tipo')->toArray();
            $hasDoc = !empty($e->documento_baja_path) || $e->documentos()->where('tipo', 'Documento de Baja')->exists();
            $this->line(sprintf(
                'ID: %d | %s %s | HasDoc: %s | Alertas: [%s]',
                $e->id,
                $e->nombre,
                $e->apellidos,
                $hasDoc ? 'YES' : 'NO',
                implode(', ', $alertas)
            ));
        }
        return 0;
    }
}
