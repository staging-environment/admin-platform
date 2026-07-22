<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\EmpleadoVacacion;
use App\Models\EmpleadoAusencia;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class Aprobaciones extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Aprobación de Solicitudes';
    protected static ?string $title = 'Aprobación de Vacaciones y Bajas';

    protected static string|\UnitEnum|null $navigationGroup = 'Recursos humanos';
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.aprobaciones';

    public $vacacionesPendientes = [];
    public $bajasPendientes = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        $user->load('roles', 'permissions');
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        
        return $user->hasRole('Admin') || $user->can('aprobacion_vacaciones_bajas');
    }

    public function mount(): void
    {
        $this->loadPendientes();
    }

    public function loadPendientes(): void
    {
        $this->vacacionesPendientes = EmpleadoVacacion::with('empleado')
            ->where('estado', 'Pendiente')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->bajasPendientes = EmpleadoAusencia::with('empleado')
            ->where('estado', 'Pendiente')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function aprobarVacacion($id): void
    {
        $vac = EmpleadoVacacion::find($id);
        if ($vac) {
            $vac->update(['estado' => 'Aceptada']);
            
            if ($vac->empleado && $vac->empleado->email) {
                $user = User::where('email', $vac->empleado->email)->first();
                if ($user) {
                    Notification::make()
                        ->title("Solicitud de Vacaciones Aceptada")
                        ->body("Tu solicitud de vacaciones del " . Carbon::parse($vac->fecha_inicio)->format('d/m/Y') . " al " . Carbon::parse($vac->fecha_fin)->format('d/m/Y') . " ha sido aceptada.")
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->sendToDatabase($user);
                }
            }

            Notification::make()
                ->title('Vacaciones Aprobadas')
                ->success()
                ->send();
            $this->loadPendientes();
        }
    }

    public function denegarVacacion($id): void
    {
        $vac = EmpleadoVacacion::find($id);
        if ($vac) {
            $vac->update(['estado' => 'Rechazada']);
            
            if ($vac->empleado && $vac->empleado->email) {
                $user = User::where('email', $vac->empleado->email)->first();
                if ($user) {
                    Notification::make()
                        ->title("Solicitud de Vacaciones Rechazada")
                        ->body("Tu solicitud de vacaciones del " . Carbon::parse($vac->fecha_inicio)->format('d/m/Y') . " al " . Carbon::parse($vac->fecha_fin)->format('d/m/Y') . " ha sido rechazada.")
                        ->icon('heroicon-o-x-circle')
                        ->iconColor('danger')
                        ->sendToDatabase($user);
                }
            }

            Notification::make()
                ->title('Vacaciones Denegadas')
                ->success()
                ->send();
            $this->loadPendientes();
        }
    }

    public function aprobarBaja($id): void
    {
        $baja = EmpleadoAusencia::find($id);
        if ($baja) {
            $baja->update(['estado' => 'Aceptada']);

            if ($baja->empleado && $baja->empleado->email) {
                $user = User::where('email', $baja->empleado->email)->first();
                if ($user) {
                    Notification::make()
                        ->title("Solicitud de Baja Médica Aceptada")
                        ->body("Tu solicitud de baja médica iniciada el " . Carbon::parse($baja->fecha_inicio)->format('d/m/Y') . " ha sido aceptada.")
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->sendToDatabase($user);
                }
            }

            Notification::make()
                ->title('Baja Médica Aprobada')
                ->success()
                ->send();
            $this->loadPendientes();
        }
    }

    public function denegarBaja($id): void
    {
        $baja = EmpleadoAusencia::find($id);
        if ($baja) {
            $baja->update(['estado' => 'Rechazada']);

            if ($baja->empleado && $baja->empleado->email) {
                $user = User::where('email', $baja->empleado->email)->first();
                if ($user) {
                    Notification::make()
                        ->title("Solicitud de Baja Médica Rechazada")
                        ->body("Tu solicitud de baja médica iniciada el " . Carbon::parse($baja->fecha_inicio)->format('d/m/Y') . " ha sido rechazada.")
                        ->icon('heroicon-o-x-circle')
                        ->iconColor('danger')
                        ->sendToDatabase($user);
                }
            }

            Notification::make()
                ->title('Baja Médica Denegada')
                ->success()
                ->send();
            $this->loadPendientes();
        }
    }
}
