<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudEstadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $tipo;
    public $fechaInicio;
    public $fechaFin;
    public $estado;
    public $comentario;

    public function __construct($nombre, $tipo, $fechaInicio, $fechaFin, $estado, $comentario = null)
    {
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->estado = $estado;
        $this->comentario = $comentario;
    }

    public function build()
    {
        $color = $this->estado === 'Aceptada' ? '#16a34a' : '#dc2626';
        $estadoTxt = $this->estado === 'Aceptada' ? 'APROBADA' : 'DENEGADA';

        return $this->subject("Estado de tu solicitud: {$this->tipo} - {$estadoTxt}")
            ->html("
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 12px;'>
                    <div style='text-align: center; margin-bottom: 20px;'>
                        <h2 style='color: #1e3a8a;'>Portal de Empleado - Utrecar</h2>
                    </div>
                    <div style='background-color: #f9fafb; padding: 20px; border-radius: 8px;'>
                        <p>Hola, <strong>{$this->nombre}</strong>,</p>
                        <p>Te informamos que tu solicitud de <strong>{$this->tipo}</strong> ha sido <strong><span style='color: {$color};'>{$estadoTxt}</span></strong>.</p>
                        <hr style='border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 6px 0; color: #4b5563; font-weight: bold;'>Tipo:</td>
                                <td style='padding: 6px 0; text-align: right;'>{$this->tipo}</td>
                            </tr>
                            <tr>
                                <td style='padding: 6px 0; color: #4b5563; font-weight: bold;'>Fecha Inicio:</td>
                                <td style='padding: 6px 0; text-align: right;'>{$this->fechaInicio}</td>
                            </tr>
                            " . ($this->fechaFin ? "
                            <tr>
                                <td style='padding: 6px 0; color: #4b5563; font-weight: bold;'>Fecha Fin:</td>
                                <td style='padding: 6px 0; text-align: right;'>{$this->fechaFin}</td>
                            </tr>
                            " : "") . "
                            <tr>
                                <td style='padding: 6px 0; color: #4b5563; font-weight: bold;'>Estado:</td>
                                <td style='padding: 6px 0; text-align: right; font-weight: bold; color: {$color};'>{$estadoTxt}</td>
                            </tr>
                        </table>
                        " . ($this->comentario ? "
                        <div style='margin-top: 15px; padding: 12px; background-color: #f3f4f6; border-left: 4px solid {$color}; border-radius: 4px; font-style: italic; color: #374151;'>
                            <strong>Motivo/Comentario del aprobador:</strong> {$this->comentario}
                        </div>
                        " : "") . "
                    </div>
                    <p style='font-size: 11px; color: #9ca3af; margin-top: 20px; text-align: center;'>Este es un mensaje automático enviado desde el portal de administración de Utrecar. Por favor, no respondas a este correo.</p>
                </div>
            ");
    }
}
