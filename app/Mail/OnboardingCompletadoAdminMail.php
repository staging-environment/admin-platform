<?php

namespace App\Mail;

use App\Models\Empleado;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class OnboardingCompletadoAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public Empleado $empleado;

    public function __construct(Empleado $empleado)
    {
        $this->empleado = $empleado;
    }

    public function build()
    {
        $nombreCompleto = trim("{$this->empleado->nombre} {$this->empleado->apellidos}");
        return $this->subject("🎉 Incorporación Completada - {$nombreCompleto}")
            ->html($this->getEmailContent());
    }

    private function getEmailContent()
    {
        $nombreCompleto = htmlspecialchars(trim("{$this->empleado->nombre} {$this->empleado->apellidos}"));
        $email = htmlspecialchars($this->empleado->email ?? 'No especificado');
        $telefono = htmlspecialchars($this->empleado->telefono_principal ?? 'No especificado');
        $dni = htmlspecialchars($this->empleado->dni ?? 'No especificado');
        $fecha = Carbon::now()->format('d/m/Y H:i');
        $url = url('/admin/recursos-humanos/' . $this->empleado->id);

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Incorporación Completada</title>
        </head>
        <body style=\"font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px;\">
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #f3f4f6;'>
                <!-- Header -->
                <tr>
                    <td style='background: linear-gradient(135deg, #059669 0%, #047857 100%); padding: 40px 20px; text-align: center;'>
                        <div style='font-size: 32px; margin-bottom: 8px;'>🎉</div>
                        <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.025em;'>Onboarding Completado</h1>
                        <p style='color: #d1fae5; margin: 10px 0 0 0; font-size: 14px; font-weight: 500;'>Expediente pendiente de validación por RRHH</p>
                    </td>
                </tr>
                <!-- Content -->
                <tr>
                    <td style='padding: 40px 30px;'>
                        <p style='margin: 0 0 16px 0; font-size: 16px; line-height: 24px; color: #1f2937; font-weight: 600;'>Hola, Administrador / Gestor:</p>
                        <p style='margin: 0 0 24px 0; font-size: 14px; line-height: 22px; color: #4b5563;'>
                            El empleado <strong>{$nombreCompleto}</strong> ha completado satisfactoriamente todos los pasos del asistente de incorporación inicial (datos personales, documentación y aceptación de normativas).
                        </p>
                        
                        <!-- Details Table -->
                        <table border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color: #f9fafb; border-radius: 16px; padding: 20px; margin-bottom: 24px; border: 1px solid #f3f4f6;'>
                            <tr>
                                <td style='padding: 8px 0; font-size: 13px; color: #9ca3af; font-weight: 600; width: 140px; text-transform: uppercase;'>Empleado:</td>
                                <td style='padding: 8px 0; font-size: 14px; color: #1f2937; font-weight: 700;'>{$nombreCompleto}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-size: 13px; color: #9ca3af; font-weight: 600; text-transform: uppercase;'>Email:</td>
                                <td style='padding: 8px 0; font-size: 14px; color: #1f2937; font-weight: 600;'>{$email}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-size: 13px; color: #9ca3af; font-weight: 600; text-transform: uppercase;'>DNI / NIE:</td>
                                <td style='padding: 8px 0; font-size: 14px; color: #1f2937; font-weight: 600; font-family: monospace;'>{$dni}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-size: 13px; color: #9ca3af; font-weight: 600; text-transform: uppercase;'>Teléfono:</td>
                                <td style='padding: 8px 0; font-size: 14px; color: #1f2937; font-weight: 600;'>{$telefono}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-size: 13px; color: #9ca3af; font-weight: 600; text-transform: uppercase;'>Fecha y Hora:</td>
                                <td style='padding: 8px 0; font-size: 14px; color: #1f2937; font-weight: 600; font-family: monospace;'>{$fecha}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-size: 13px; color: #9ca3af; font-weight: 600; text-transform: uppercase;'>Estado:</td>
                                <td style='padding: 8px 0; font-size: 13px; color: #059669; font-weight: 700;'>Pendiente de Validación Final</td>
                            </tr>
                        </table>

                        <p style='margin: 0 0 24px 0; font-size: 14px; line-height: 22px; color: #4b5563;'>
                            Ya puedes acceder a la ficha del trabajador para revisar la documentación adjunta y aprobar el expediente mediante el checklist de validación.
                        </p>

                        <!-- Button -->
                        <table align='center' border='0' cellpadding='0' cellspacing='0' style='margin: 30px auto;'>
                            <tr>
                                <td align='center' style='border-radius: 12px; background-color: #059669;'>
                                    <a href='{$url}' target='_blank' style='display: inline-block; padding: 14px 30px; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 12px;'>
                                        Revisar Ficha del Empleado
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- Footer -->
                <tr>
                    <td style='background-color: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #f3f4f6;'>
                        <p style='margin: 0; font-size: 12px; color: #9ca3af;'>&copy; " . date('Y') . " Utrecar. Todos los derechos reservados.</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
    }
}
