<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevaSolicitudAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $empleadoNombre;
    public $empleadoEmail;
    public $solicitudTipo;
    public $fechaInicio;
    public $fechaFin;
    public $comentarioEmpleado;

    public function __construct($empleadoNombre, $empleadoEmail, $solicitudTipo, $fechaInicio, $fechaFin, $comentarioEmpleado)
    {
        $this->empleadoNombre = $empleadoNombre;
        $this->empleadoEmail = $empleadoEmail;
        $this->solicitudTipo = $solicitudTipo;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->comentarioEmpleado = $comentarioEmpleado;
    }

    public function build()
    {
        $isAlta = $this->solicitudTipo === 'Bajas médicas (Alta)';
        $isBaja = $this->solicitudTipo === 'Bajas médicas';
        
        if ($isAlta) {
            $subject = "🔔 Alta Médica Registrada - {$this->empleadoNombre}";
        } elseif ($isBaja) {
            $subject = "🔔 Baja Médica Registrada - {$this->empleadoNombre}";
        } else {
            $subject = "🔔 Nueva Solicitud de {$this->solicitudTipo} - {$this->empleadoNombre}";
        }
        
        return $this->subject($subject)->html($this->getEmailContent());
    }

    private function getEmailContent()
    {
        $isAlta = $this->solicitudTipo === 'Bajas médicas (Alta)';
        $isBaja = $this->solicitudTipo === 'Bajas médicas';
        
        $url = ($isBaja || $isAlta) ? url('/admin/recursos-humanos') : url('/admin/aprobaciones');
        
        if ($isAlta) {
            $subjectText = "Alta Médica Registrada";
            $subtitleText = "Reincorporación al Trabajo";
            $introText = "El empleado <strong>{$this->empleadoNombre}</strong> ({$this->empleadoEmail}) ha registrado su alta médica en el sistema:";
            $footerText = "Esta alta médica ha sido registrada de forma directa y no requiere de aprobación por tu parte.";
            $btnText = "Ver en el Panel de Administración";
        } elseif ($isBaja) {
            $subjectText = "Baja Médica Registrada";
            $subtitleText = "Notificación de Ausencia";
            $introText = "El empleado <strong>{$this->empleadoNombre}</strong> ({$this->empleadoEmail}) ha registrado una baja médica en el sistema:";
            $footerText = "Esta baja médica ha sido registrada de forma directa y no requiere de aprobación por tu parte.";
            $btnText = "Ver en el Panel de Administración";
        } else {
            $subjectText = "Nueva Solicitud Recibida";
            $subtitleText = "Pendiente de Aprobación o Denegación";
            $introText = "El empleado <strong>{$this->empleadoNombre}</strong> ({$this->empleadoEmail}) ha registrado una nueva solicitud en el sistema:";
            $footerText = "Puedes evaluar, autorizar o denegar esta solicitud inmediatamente a través del Portal de Aprobaciones.";
            $btnText = "Autorizar o Denegar en el Portal";
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>{$subjectText}</title>
        </head>
        <body style=\"font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px;\">
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #f3f4f6;'>
                <!-- Header -->
                <tr>
                    <td style='background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); padding: 40px 20px; text-align: center;'>
                        <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.025em;'>{$subjectText}</h1>
                        <p style='color: #e0e7ff; margin: 10px 0 0 0; font-size: 14px; font-weight: 500;'>{$subtitleText}</p>
                    </td>
                </tr>
                <!-- Content -->
                <tr>
                    <td style='padding: 40px 30px;'>
                        <p style='margin: 0 0 16px 0; font-size: 16px; line-height: 24px; color: #1f2937; font-weight: 600;'>Hola, Administrador:</p>
                        <p style='margin: 0 0 24px 0; font-size: 14px; line-height: 22px; color: #4b5563;'>
                            {$introText}
                        </p>
                        <!-- Details Table -->
                        <table border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color: #f9fafb; border-radius: 16px; padding: 20px; margin-bottom: 24px; border: 1px solid #f3f4f6;'>
                            <tr>
                                <td style='padding: 6px 0; font-size: 13px; color: #9ca3af; font-weight: 600; width: 140px; text-transform: uppercase;'>Tipo:</td>
                                <td style='padding: 6px 0; font-size: 14px; color: #1f2937; font-weight: 700;'>{$this->solicitudTipo}</td>
                            </tr>
                            <tr>
                                <td style='padding: 6px 0; font-size: 13px; color: #9ca3af; font-weight: 600; text-transform: uppercase;'>Fecha de Inicio:</td>
                                <td style='padding: 6px 0; font-size: 14px; color: #1f2937; font-weight: 700; font-family: monospace;'>{$this->fechaInicio}</td>
                            </tr>
                            <tr>
                                <td style='padding: 6px 0; font-size: 13px; color: #9ca3af; font-weight: 600; text-transform: uppercase;'>Fecha Fin:</td>
                                <td style='padding: 6px 0; font-size: 14px; color: #1f2937; font-weight: 700; font-family: monospace;'>{$this->fechaFin}</td>
                            </tr>
                            " . ($this->comentarioEmpleado ? "
                            <tr>
                                <td style='padding: 6px 0; font-size: 13px; color: #9ca3af; font-weight: 600; text-transform: uppercase; vertical-align: top;'>Explicación:</td>
                                <td style='padding: 6px 0; font-size: 14px; color: #4b5563; font-style: italic;'>\"{$this->comentarioEmpleado}\"</td>
                            </tr>
                            " : "") . "
                        </table>
                        
                        <p style='margin: 0 0 24px 0; font-size: 14px; line-height: 22px; color: #4b5563;'>
                            {$footerText}
                        </p>
                        <!-- Button -->
                        <table align='center' border='0' cellpadding='0' cellspacing='0' style='margin: 30px auto;'>
                            <tr>
                                <td align='center' style='border-radius: 12px; background-color: #4f46e5;'>
                                    <a href='{$url}' target='_blank' style='display: inline-block; padding: 14px 30px; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 12px;'>
                                        {$btnText}
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
