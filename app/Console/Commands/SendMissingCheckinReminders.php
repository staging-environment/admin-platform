<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Empleado;
use App\Models\EmpleadoFichaje;
use App\Models\EmpleadoVacacion;
use App\Models\EmpleadoAusencia;
use App\Mail\FichajePendienteReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendMissingCheckinReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fichajes:send-missing-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email reminders to employees who have not completed their entry and exit check-ins on the last weekday';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting checks for missing check-ins...");

        // Determine the target date: last weekday
        $checkDate = Carbon::yesterday();
        while ($checkDate->isWeekend()) {
            $checkDate->subDay();
        }
        $dateStr = $checkDate->format('Y-m-d');
        $formattedDate = $checkDate->translatedFormat('l, d \d\e F \d\e Y');

        $this->info("Target date to verify: {$dateStr} ({$formattedDate})");

        // Load all administrators/notified users
        $admins = User::all()->filter(function($user) {
            $user->load('roles');
            return $user->hasRole('Admin') 
                || $user->hasRole('admin') 
                || $user->email === 'jarodriguezbonilla@gmail.com' 
                || $user->id === 1
                || $user->can('recibir_notificaciones_recursos_humanos');
        });

        // Load all users
        $users = User::all()->filter(function($user) {
            $user->load('roles');
            // Must have Empleado role, but NOT Admin role
            $hasEmpleado = $user->hasRole('Empleado') || $user->hasRole('empleado');
            $isAdmin = $user->hasRole('Admin') || $user->hasRole('admin') || $user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1;
            return $hasEmpleado && !$isAdmin;
        });

        $this->info("Found " . $users->count() . " active employees to analyze.");
        $this->info("Found " . $admins->count() . " administrators to notify of incidences.");

        $emailsSentCount = 0;

        foreach ($users as $user) {
            $empleado = Empleado::where('email', $user->email)->first();
            if (!$empleado) {
                $this->warn("User {$user->email} has Empleado role but no linked Empleado record. Skipping.");
                continue;
            }

            // 1. Check if approved vacation covers this date
            $hasVacacion = EmpleadoVacacion::where('empleado_id', $empleado->id)
                ->where('estado', 'Aceptada')
                ->where('fecha_inicio', '<=', $dateStr)
                ->where('fecha_fin', '>=', $dateStr)
                ->exists();

            if ($hasVacacion) {
                $this->info("Employee {$empleado->nombre} {$empleado->apellidos} is on APPROVED VACATION. Skipping.");
                continue;
            }

            // 2. Check if approved absence/sick leave covers this date
            $hasAusencia = EmpleadoAusencia::where('empleado_id', $empleado->id)
                ->where('estado', 'Aceptada')
                ->where('fecha_inicio', '<=', $dateStr)
                ->where(function($q) use ($dateStr) {
                    $q->where('fecha_fin', '>=', $dateStr)
                      ->orWhereNull('fecha_fin');
                })
                ->exists();

            if ($hasAusencia) {
                $this->info("Employee {$empleado->nombre} {$empleado->apellidos} is on APPROVED SICK LEAVE. Skipping.");
                continue;
            }

            // 3. Check check-ins
            $fichaje = EmpleadoFichaje::where('empleado_id', $empleado->id)
                ->where('fecha', $dateStr)
                ->first();

            $completedControl = $fichaje && $fichaje->hora_entrada && $fichaje->hora_salida;

            if (!$completedControl) {
                $this->info("Employee {$empleado->nombre} has incomplete check-in control. Sending email reminder to {$user->email}...");
                
                try {
                    Mail::to($user->email)->send(new FichajePendienteReminderMail(
                        $empleado->nombre,
                        $formattedDate
                    ));
                    $emailsSentCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
                }

                // Notify administrators
                foreach ($admins as $admin) {
                    try {
                        Mail::to($admin->email)->send(new \App\Mail\FichajeFaltanteAdminMail(
                            "{$empleado->nombre} {$empleado->apellidos}",
                            $user->email,
                            $formattedDate
                        ));
                        $this->info("Notified admin {$admin->email} about incomplete check-in for {$empleado->nombre}.");
                    } catch (\Exception $e) {
                        $this->error("Failed to send email to admin {$admin->email}: " . $e->getMessage());
                    }
                }
            } else {
                $this->info("Employee {$empleado->nombre} has fully completed check-in control.");
            }
        }

        $this->info("Reminder run completed. {$emailsSentCount} emails delivered.");
    }
}
