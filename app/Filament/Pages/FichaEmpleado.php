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

    public $editingFichajeId = null;
    public $editingFecha = null;
    public $editingHoraEntrada = null;
    public $editingHoraSalida = null;

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

        // Auto-create mock employee for admin users who also have the Empleado role (or are testing)
        if (!$this->empleado && ($user->hasRole('Admin') || $user->hasRole('admin'))) {
            $this->empleado = Empleado::create([
                'nombre' => $user->name ?: 'jarodriguezbonilla',
                'apellidos' => '(Admin)',
                'dni' => 'ADMIN-' . substr(md5($user->email), 0, 8),
                'fecha_nacimiento' => '1990-01-01',
                'direccion' => 'Calle Administrador',
                'localidad' => 'Sevilla',
                'codigo_postal' => '41000',
                'provincia' => 'Sevilla',
                'telefono_principal' => $user->telefono ?: '600000000',
                'email' => $user->email,
            ]);
        }
        
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

    public function editFichaje($id): void
    {
        $fichaje = EmpleadoFichaje::find($id);
        if (!$fichaje || $fichaje->empleado_id !== $this->empleado->id) {
            return;
        }

        $this->editingFichajeId = $id;
        $this->editingFecha = $fichaje->fecha;
        $this->editingHoraEntrada = $fichaje->hora_entrada ? Carbon::parse($fichaje->hora_entrada)->format('H:i') : null;
        $this->editingHoraSalida = $fichaje->hora_salida ? Carbon::parse($fichaje->hora_salida)->format('H:i') : null;

        $this->dispatch('open-modal', id: 'edit-fichaje-modal');
    }

    public function updateFichaje(): void
    {
        $this->validate([
            'editingHoraEntrada' => 'nullable',
            'editingHoraSalida' => 'nullable',
        ]);

        $fichaje = EmpleadoFichaje::find($this->editingFichajeId);
        if (!$fichaje || $fichaje->empleado_id !== $this->empleado->id) {
            return;
        }

        $fichaje->update([
            'hora_entrada' => $this->editingHoraEntrada ?: null,
            'hora_salida' => $this->editingHoraSalida ?: null,
        ]);

        Notification::make()
            ->title('Fichaje Actualizado')
            ->body('El registro del día ' . Carbon::parse($fichaje->fecha)->format('d/m/Y') . ' ha sido modificado con éxito.')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'edit-fichaje-modal');
        $this->loadFichajes();
    }
}
