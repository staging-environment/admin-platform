<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class EmpleadoFichaje extends Model
{
    use SoftDeletes;

    protected $table = 'empleado_fichajes';

    protected $guarded = [];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'server_checkin_at' => 'datetime',
        'server_checkout_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    /**
     * Devuelve la lista de alertas si hay una discrepancia mayor a 5 minutos (300 seg)
     * entre la hora marcada por el empleado y la hora real capturada por el sistema.
     */
    public function getAlertasDescuadreAttribute(): array
    {
        $alertas = [];
        $fechaStr = $this->fecha ? ($this->fecha instanceof Carbon ? $this->fecha->format('Y-m-d') : substr((string)$this->fecha, 0, 10)) : null;

        if ($fechaStr && $this->hora_entrada && $this->server_checkin_at) {
            $horaEntradaStr = substr((string)$this->hora_entrada, 0, 5);
            $userCheckin = Carbon::parse($fechaStr . ' ' . $horaEntradaStr, 'Europe/Madrid');
            $serverCheckin = $this->server_checkin_at->copy()->timezone('Europe/Madrid');
            $diffSecondsIn = abs($serverCheckin->diffInSeconds($userCheckin, false));
            $diffMinutesIn = (int) round($diffSecondsIn / 60);

            if ($diffSecondsIn > 300) {
                $alertas[] = (object)[
                    'tipo' => 'entrada',
                    'titulo' => 'Descuadre en Hora de Entrada',
                    'descripcion' => 'Hora indicada: ' . $userCheckin->format('H:i') . ' | Hora real del sistema: ' . $serverCheckin->format('H:i:s') . ' (Diferencia de ' . $diffMinutesIn . ' min)',
                    'diferencia_minutos' => $diffMinutesIn,
                    'hora_usuario' => $userCheckin->format('H:i'),
                    'hora_servidor' => $serverCheckin->format('H:i:s'),
                    'fecha' => $fechaStr,
                ];
            }
        }

        if ($fechaStr && $this->hora_salida && $this->server_checkout_at) {
            $horaSalidaStr = substr((string)$this->hora_salida, 0, 5);
            $horaEntradaStr = $this->hora_entrada ? substr((string)$this->hora_entrada, 0, 5) : null;
            $userCheckout = Carbon::parse($fechaStr . ' ' . $horaSalidaStr, 'Europe/Madrid');
            if ($horaEntradaStr && $horaSalidaStr < $horaEntradaStr) {
                $userCheckout->addDay();
            }
            $serverCheckout = $this->server_checkout_at->copy()->timezone('Europe/Madrid');
            $diffSecondsOut = abs($serverCheckout->diffInSeconds($userCheckout, false));
            $diffMinutesOut = (int) round($diffSecondsOut / 60);

            if ($diffSecondsOut > 300) {
                $alertas[] = (object)[
                    'tipo' => 'salida',
                    'titulo' => 'Descuadre en Hora de Salida',
                    'descripcion' => 'Hora indicada: ' . $userCheckout->format('H:i') . ' | Hora real del sistema: ' . $serverCheckout->format('H:i:s') . ' (Diferencia de ' . $diffMinutesOut . ' min)',
                    'diferencia_minutos' => $diffMinutesOut,
                    'hora_usuario' => $userCheckout->format('H:i'),
                    'hora_servidor' => $serverCheckout->format('H:i:s'),
                    'fecha' => $fechaStr,
                ];
            }
        }

        return $alertas;
    }

    public function hasAlertasDescuadre(): bool
    {
        return count($this->alertas_descuadre) > 0;
    }

    public function isEntradaDescuadre(): bool
    {
        $fechaStr = $this->fecha ? ($this->fecha instanceof Carbon ? $this->fecha->format('Y-m-d') : substr((string)$this->fecha, 0, 10)) : null;
        if (!$fechaStr || !$this->hora_entrada || !$this->server_checkin_at) {
            return false;
        }
        $userCheckin = Carbon::parse($fechaStr . ' ' . substr((string)$this->hora_entrada, 0, 5), 'Europe/Madrid');
        $serverCheckin = $this->server_checkin_at->copy()->timezone('Europe/Madrid');
        return abs($serverCheckin->diffInSeconds($userCheckin, false)) > 300;
    }

    public function isSalidaDescuadre(): bool
    {
        $fechaStr = $this->fecha ? ($this->fecha instanceof Carbon ? $this->fecha->format('Y-m-d') : substr((string)$this->fecha, 0, 10)) : null;
        if (!$fechaStr || !$this->hora_salida || !$this->server_checkout_at) {
            return false;
        }
        $horaSalidaStr = substr((string)$this->hora_salida, 0, 5);
        $horaEntradaStr = $this->hora_entrada ? substr((string)$this->hora_entrada, 0, 5) : null;
        $userCheckout = Carbon::parse($fechaStr . ' ' . $horaSalidaStr, 'Europe/Madrid');
        if ($horaEntradaStr && $horaSalidaStr < $horaEntradaStr) {
            $userCheckout->addDay();
        }
        $serverCheckout = $this->server_checkout_at->copy()->timezone('Europe/Madrid');
        return abs($serverCheckout->diffInSeconds($userCheckout, false)) > 300;
    }
}
