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
        $today = now()->startOfDay();
        $this->info("Fecha actual del sistema: " . $today->format('d/m/Y'));

        if ($this->option('update')) {
            $this->info("Actualizando no_tiene_discapacidad para empleados dados de alta anteriores...");
            $updatedNoDisc = Empleado::where('estado', 'Alta')
                ->where('tiene_discapacidad', false)
                ->where('tiene_incapacidad', false)
                ->where('no_tiene_discapacidad', false)
                ->update(['no_tiene_discapacidad' => true]);
            $this->info("Empleados actualizados a 'no_tiene_discapacidad': {$updatedNoDisc}");

            $this->info("Recalculando alertas para todos los empleados...");
            foreach (Empleado::all() as $e) {
                $e->actualizarAlertas();
            }
            $this->info("Alertas recalculadas correctamente.");
        }

        $this->info("
=== RESUMEN DE EMPLEADOS Y ALERTAS ===");
        $empleados = Empleado::orderBy('apellidos')->get();
        foreach ($empleados as $e) {
            $latest = $e->documentos()
                ->where('tipo', 'Contratos')
                ->orderByRaw('COALESCE(fecha_inicio_contrato, "1970-01-01") DESC')
                ->orderBy('id', 'desc')
                ->first();
            
            $venc = $latest?->fecha_vencimiento_contrato ?: $e->fecha_vencimiento_contrato;
            $tipo = $latest?->tipo_contrato ?: $e->tipo_contrato;
            $alertas = $e->alertas()->pluck('tipo')->toArray();

            $contratoStr = $tipo ?? 'SIN_CONTRATO';
            if ($venc) {
                $isExpired = $venc->endOfDay()->isPast();
                $contratoStr .= " (Fin: " . $venc->format('d/m/Y') . ($isExpired ? ' [VENCIDO]' : '') . ")";
            }

            $alertaBadge = empty($alertas) ? 'OK (Sin alertas)' : 'ALERTA: [' . implode(', ', $alertas) . ']';

            $this->line(sprintf(
                '[%s] ID: %d | %s %s | Contrato: %s | %s',
                $e->estado,
                $e->id,
                $e->apellidos,
                $e->nombre,
                $contratoStr,
                $alertaBadge
            ));
        }

        return 0;
    }
}
