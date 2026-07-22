<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FichajeFaltanteAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $empleadoNombre;
    public $empleadoEmail;
    public $fechaFormateada;

    public function __construct($empleadoNombre, $empleadoEmail, $fechaFormateada)
    {
        $this->empleadoNombre = $empleadoNombre;
        $this->empleadoEmail = $empleadoEmail;
        $this->fechaFormateada = $fechaFormateada;
    }

    public function build()
    {
        return $this->subject("⚠️ Incidencia: Fichaje Incompleto - {$this->empleadoNombre}")
            ->html($this->getEmailContent());
    }

    private function getEmailContent()
    {
        $url = url('/admin/ficha-empleado');
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Incidencia de Fichaje</title>
        </head>
        <body style=\"font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px;\">
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #f3f4f6;'>
                <!-- Header -->
                <tr>
                    <td style='background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); padding: 40px 20px; text-align: center;'>
                        <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.025em;'>Alerta de Fichaje Incompleto</h1>
                        <p style='color: #fee2e2; margin: 10px 0 0 0; font-size: 14px; font-weight: 500;'>Incidencia de control horario</p>
                    </td>
                </tr>
                <!-- Content -->
                <tr>
                    <td style='padding: 40px 30px;'>
                        <p style='margin: 0 0 16px 0; font-size: 16px; line-height: 24px; color: #1f2937; font-weight: 600;'>Hola, Administrador:</p>
                        <p style='margin: 0 0 24px 0; font-size: 14px; line-height: 22px; color: #4b5563;'>
                            Te informamos que el empleado <strong>{$this->empleadoNombre}</strong> ({$this->empleadoEmail}) no ha cumplido con el registro obligatorio de su entrada y/o salida para el día laborable <strong>{$this->fechaFormateada}</strong>.
                        </p>
                        <p style='margin: 0 0 24px 0; font-size: 14px; line-height: 22px; color: #4b5563;'>
                            Se ha enviado automáticamente un correo electrónico de aviso al empleado para que regularice su situación. Puedes revisar su ficha o editar el registro desde el Panel de Control.
                        </p>
                        <!-- Button -->
                        <table align='center' border='0' cellpadding='0' cellspacing='0' style='margin: 30px auto;'>
                            <tr>
                                <td align='center' style='border-radius: 12px; background-color: #b91c1c;'>
                                    <a href='{$url}' target='_blank' style='display: inline-block; padding: 14px 30px; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 12px;'>
                                        Ver Control de Fichajes
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
