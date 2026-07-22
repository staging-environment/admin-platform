<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Empleado;
use App\Models\EmpleadoFichaje;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class FichaEmpleado extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Ficha de Empleado';
    protected static ?string $title = 'Portal de Empleado';

    protected string $view = 'filament.pages.ficha-empleado';

    public $empleado;
    public $fichajeDelDia;
    public $recentFichajes;

    public $hora_entrada;
    public $hora_salida;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        $user->load('roles', 'permissions');
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        
        return $user->hasRole('Admin') || $user->can('acceder_ficha_empleado');
    }

    public function mount(): void
    {
        $user = auth()->user();
        $this->empleado = Empleado::where('email', $user->email)->first();
        
        // Initialize default input times to current server time
        $this->hora_entrada = Carbon::now()->format('H:i');
        $this->hora_salida = Carbon::now()->format('H:i');

        $this->loadFichajes();
    }

    public function loadFichajes(): void
    {
        if (!$this->empleado) {
            $this->fichajeDelDia = null;
            $this->recentFichajes = collect();
            return;
        }

        $this->fichajeDelDia = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
            ->where('fecha', Carbon::today()->format('Y-m-d'))
            ->first();

        $this->recentFichajes = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
            ->orderBy('fecha', 'desc')
            ->limit(30)
            ->get();
    }

    public function checkIn(): void
    {
        if (!$this->empleado) {
            Notification::make()
                ->title('Error')
                ->body('Tu usuario no está asociado a ningún registro de empleado.')
                ->danger()
                ->send();
            return;
        }

        $this->validate([
            'hora_entrada' => 'required|date_format:H:i',
        ]);

        $today = Carbon::today()->format('Y-m-d');

        EmpleadoFichaje::updateOrCreate(
            [
                'empleado_id' => $this->empleado->id,
                'fecha' => $today,
            ],
            [
                'hora_entrada' => $this->hora_entrada,
                'server_checkin_at' => Carbon::now(),
            ]
        );

        Notification::make()
            ->title('Check-in Registrado')
            ->body('Has registrado tu entrada a las ' . $this->hora_entrada . ' (Hora del servidor: ' . Carbon::now()->format('H:i:s') . ').')
            ->success()
            ->send();

        $this->loadFichajes();
    }

    public function checkOut(): void
    {
        if (!$this->empleado) {
            Notification::make()
                ->title('Error')
                ->body('Tu usuario no está asociado a ningún registro de empleado.')
                ->danger()
                ->send();
            return;
        }

        $this->validate([
            'hora_salida' => 'required|date_format:H:i',
        ]);

        $today = Carbon::today()->format('Y-m-d');

        $fichaje = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
            ->where('fecha', $today)
            ->first();

        if (!$fichaje) {
            Notification::make()
                ->title('Error')
                ->body('No puedes registrar salida sin haber registrado entrada primero.')
                ->danger()
                ->send();
            return;
        }

        $fichaje->update([
            'hora_salida' => $this->hora_salida,
            'server_checkout_at' => Carbon::now(),
        ]);

        Notification::make()
            ->title('Check-out Registrado')
            ->body('Has registrado tu salida a las ' . $this->hora_salida . ' (Hora del servidor: ' . Carbon::now()->format('H:i:s') . ').')
            ->success()
            ->send();

        $this->loadFichajes();
    }
}
