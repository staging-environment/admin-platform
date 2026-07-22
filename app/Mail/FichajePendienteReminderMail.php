<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FichajePendienteReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $fechaFormateada;

    public function __construct($nombre, $fechaFormateada)
    {
        $this->nombre = $nombre;
        $this->fechaFormateada = $fechaFormateada;
    }

    public function build()
    {
        return $this->subject('⏰ Recordatorio: Registro de Fichaje Incompleto o Faltante')
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
            <title>Recordatorio de Fichaje</title>
        </head>
        <body style=\"font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px;\">
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #f3f4f6;'>
                <!-- Header -->
                <tr>
                    <td style='background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 40px 20px; text-align: center;'>
                        <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.025em;'>Control de Fichaje</h1>
                        <p style='color: #fef3c7; margin: 10px 0 0 0; font-size: 14px; font-weight: 500;'>Recordatorio de jornada laborable</p>
                    </td>
                </tr>
                <!-- Content -->
                <tr>
                    <td style='padding: 40px 30px;'>
                        <p style='margin: 0 0 16px 0; font-size: 16px; line-height: 24px; color: #1f2937; font-weight: 600;'>Hola, {$this->nombre}:</p>
                        <p style='margin: 0 0 24px 0; font-size: 14px; line-height: 22px; color: #4b5563;'>
                            Te recordamos que no hemos detectado el registro completo de tu **entrada** y/o **salida** para el día laborable <strong>{$this->fechaFormateada}</strong> en nuestro sistema de fichajes.
                        </p>
                        <p style='margin: 0 0 24px 0; font-size: 14px; line-height: 22px; color: #4b5563;'>
                            Es importante que registres tanto el inicio como el fin de tu jornada para mantener al día los registros obligatorios de control horario.
                        </p>
                        <!-- Button -->
                        <table align='center' border='0' cellpadding='0' cellspacing='0' style='margin: 30px auto;'>
                            <tr>
                                <td align='center' style='border-radius: 12px; background-color: #d97706;'>
                                    <a href='{$url}' target='_blank' style='display: inline-block; padding: 14px 30px; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 12px;'>
                                        Registrar Fichaje en el Portal
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <p style='margin: 0; font-size: 12px; line-height: 18px; color: #9ca3af; text-align: center;'>
                            Si estabas de baja, vacaciones o corresponde a una incidencia autorizada, por favor contacta con el administrador.
                        </p>
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
