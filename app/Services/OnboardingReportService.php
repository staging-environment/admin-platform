<?php

namespace App\Services;

use App\Models\User;
use App\Models\Empleado;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfInstance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class OnboardingReportService
{
    /**
     * Obtiene los datos consolidados de todos los usuarios con rol Empleado
     * filtrando aquellos que no han completado el proceso de onboarding.
     */
    public function getReportData(): array
    {
        $allUsers = User::role('Empleado')
            ->with(['empleado.gasolinera', 'empleado.documentos', 'empleado.ausencias'])
            ->get();

        $totalEvaluados = $allUsers->count();
        $pendingList = [];

        foreach ($allUsers as $u) {
            $emp = $u->empleado;
            $isCompleted = $emp ? (bool) $emp->onboarding_completado : false;

            if ($isCompleted) {
                continue;
            }

            $hasDefaultPass = Hash::check('1234', $u->password);
            $hasValidDniIban = $emp && !empty($emp->dni) && !str_starts_with($emp->dni, 'PENDIENTE') && !empty($emp->iban);
            $hasDniDoc = $emp && $emp->documentos()->where('tipo', 'DNI')->exists();
            $hasPolicies = $emp && ($emp->politicas_aceptadas_at !== null);

            $estado = $emp ? ($emp->estado ?? 'Baja') : 'Sin Ficha';

            // Nombre y apellidos formateados
            $nombre = $emp?->nombre ? trim($emp->nombre) : trim($u->name);
            $apellidos = $emp?->apellidos ? trim($emp->apellidos) : '';

            $estaciones = ($emp?->estaciones_nombres && $emp?->estaciones_nombres !== '—')
                ? $emp?->estaciones_nombres
                : ($emp?->gasolinera?->Nombre ?? '—');

            $pendingList[] = [
                'user_id' => $u->id,
                'user_name' => $u->name,
                'user_email' => $u->email,
                'user_phone' => $u->telefono ?: ($emp?->telefono_principal ?: ($emp?->telefono_secundario ?: '—')),
                'emp_id' => $emp?->id,
                'emp_nombre' => $nombre,
                'emp_apellidos' => $apellidos,
                'emp_dni' => $emp?->dni ?? '—',
                'emp_estado' => $estado,
                'emp_puesto' => $emp?->puesto ?? '—',
                'emp_estaciones' => $estaciones,
                'paso_actual' => $emp?->onboarding_paso_actual ?? 1,
                'porcentaje' => $emp ? $emp->getOnboardingProgressPercentage() : 0,
                'step1_password_cambiada' => !$hasDefaultPass,
                'step2_datos_iban' => $hasValidDniIban,
                'step3_doc_dni' => $hasDniDoc,
                'step4_politicas' => $hasPolicies,
            ];
        }

        // Ordenar: primero los empleados en Alta, luego en Baja; dentro de cada grupo por apellidos y nombre
        usort($pendingList, function ($a, $b) {
            if ($a['emp_estado'] === 'Alta' && $b['emp_estado'] !== 'Alta') {
                return -1;
            }
            if ($a['emp_estado'] !== 'Alta' && $b['emp_estado'] === 'Alta') {
                return 1;
            }
            $cmpApellidos = strcasecmp($a['emp_apellidos'], $b['emp_apellidos']);
            if ($cmpApellidos !== 0) {
                return $cmpApellidos;
            }
            return strcasecmp($a['emp_nombre'], $b['emp_nombre']);
        });

        $totalPendientes = count($pendingList);
        $totalCompletados = $totalEvaluados - $totalPendientes;
        $totalAlta = count(array_filter($pendingList, fn($e) => $e['emp_estado'] === 'Alta'));
        $totalBaja = $totalPendientes - $totalAlta;

        return [
            'empleados' => $pendingList,
            'stats' => [
                'total_evaluados' => $totalEvaluados,
                'total_completados' => $totalCompletados,
                'total_pendientes' => $totalPendientes,
                'total_alta' => $totalAlta,
                'total_baja' => $totalBaja,
                'progreso_promedio' => 50.0,
            ],
            'generatedAt' => Carbon::now()->timezone('Europe/Madrid')->format('d/m/Y H:i'),
        ];
    }

    /**
     * Genera la instancia PDF lista para renderizar o descargar.
     */
    public function generatePdf(): DomPdfInstance
    {
        $data = $this->getReportData();

        return Pdf::loadView('pdf.informe-onboarding-pendiente', $data)
            ->setPaper('a4', 'landscape');
    }

    /**
     * Guarda el archivo PDF en una ruta absoluta o relativa.
     */
    public function savePdfToFile(string $filePath): string
    {
        $pdf = $this->generatePdf();
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($filePath, $pdf->output());
        return $filePath;
    }
}
