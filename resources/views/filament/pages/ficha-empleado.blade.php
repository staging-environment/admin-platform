<x-filament-panels::page>
    <div class="space-y-6">
        @if(!$empleado)
            <div class="p-6 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900 rounded-2xl flex items-start gap-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-900 dark:text-red-200">Asociación de empleado no encontrada</h3>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">Tu usuario actual ({{ auth()->user()->email }}) no está asociado a ningún registro de empleado en la base de datos de Recursos Humanos. Contacta con tu administrador para que vincule tu correo electrónico.</p>
                </div>
            </div>
        @else
            <!-- Header Profile Info -->
            <div class="relative overflow-hidden p-6 rounded-3xl bg-gradient-to-r from-amber-500/10 to-orange-500/10 dark:from-amber-950/20 dark:to-orange-950/20 border border-amber-500/20 dark:border-amber-500/10 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-amber-500/20 text-amber-700 dark:text-amber-300 rounded-2xl">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white">{{ $empleado->nombre }} {{ $empleado->apellidos }}</h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <span class="font-medium text-amber-600 dark:text-amber-400">{{ $empleado->puesto ?? 'Empleado' }}</span>
                            <span class="h-1.5 w-1.5 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                            <span>{{ $empleado->gasolinera?->Nombre ?? 'Sin gasolinera asignada' }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Fecha de hoy</span>
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white">{{ \Carbon\Carbon::today()->translatedFormat('l, d \d\e F \d\e Y') }}</h3>
                </div>
            </div>

            <!-- Fichaje Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Check-in Card -->
                <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="p-2 bg-green-500/10 text-green-600 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                </span>
                                Entrada / Check-In
                            </h3>
                            <span class="text-xs text-gray-400">Registro de entrada</span>
                        </div>

                        <div class="py-6 flex flex-col items-center justify-center min-h-[160px]">
                            @if($fichajeDelDia && $fichajeDelDia->hora_entrada)
                                <div class="text-center space-y-2">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-300 font-bold rounded-full text-sm border border-green-200 dark:border-green-900">
                                        <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                                        Entrada Registrada
                                    </div>
                                    <h2 class="text-4xl font-black text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($fichajeDelDia->hora_entrada)->format('H:i') }}</h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Hora real de registro: {{ $fichajeDelDia->server_checkin_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @else
                                 <div class="w-full max-w-xs space-y-4" x-data="{
                                     loading: false,
                                     doCheckIn() {
                                         this.loading = true;
                                         if (navigator.geolocation) {
                                             navigator.geolocation.getCurrentPosition(
                                                 (position) => {
                                                     $wire.checkIn(position.coords.latitude, position.coords.longitude)
                                                         .finally(() => this.loading = false);
                                                 },
                                                 (error) => {
                                                     console.warn('Geolocation error:', error);
                                                     $wire.checkIn(null, null)
                                                         .finally(() => this.loading = false);
                                                 },
                                                 { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                                             );
                                         } else {
                                             $wire.checkIn(null, null)
                                                 .finally(() => this.loading = false);
                                         }
                                     }
                                 }">
                                     <div>
                                         <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Selecciona Hora de Entrada</label>
                                         <input type="time" wire:model="hora_entrada" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                     </div>
                                     <button @click="doCheckIn" x-bind:disabled="loading" class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-600/10 hover:shadow-green-700/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                                         <span x-show="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                         <span x-text="loading ? 'Obteniendo ubicación...' : 'Registrar Entrada'"></span>
                                     </button>
                                 </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Check-out Card -->
                <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="p-2 bg-orange-500/10 text-orange-600 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </span>
                                Salida / Check-Out
                            </h3>
                            <span class="text-xs text-gray-400">Registro de salida</span>
                        </div>

                        <div class="py-6 flex flex-col items-center justify-center min-h-[160px]">
                            @if($fichajeDelDia && $fichajeDelDia->hora_salida)
                                <div class="text-center space-y-2">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300 font-bold rounded-full text-sm border border-amber-200 dark:border-amber-900">
                                        <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                                        Salida Registrada
                                    </div>
                                    <h2 class="text-4xl font-black text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($fichajeDelDia->hora_salida)->format('H:i') }}</h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Hora real de registro: {{ $fichajeDelDia->server_checkout_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @elseif(!$fichajeDelDia || !$fichajeDelDia->hora_entrada)
                                <div class="text-center space-y-2 max-w-xs text-gray-400">
                                    <svg class="w-12 h-12 mx-auto stroke-current" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <p class="text-sm font-medium">Debes registrar tu entrada primero para poder registrar la salida.</p>
                                </div>
                            @else
                                 <div class="w-full max-w-xs space-y-4" x-data="{
                                     loading: false,
                                     doCheckOut() {
                                         this.loading = true;
                                         if (navigator.geolocation) {
                                             navigator.geolocation.getCurrentPosition(
                                                 (position) => {
                                                     $wire.checkOut(position.coords.latitude, position.coords.longitude)
                                                         .finally(() => this.loading = false);
                                                 },
                                                 (error) => {
                                                     console.warn('Geolocation error:', error);
                                                     $wire.checkOut(null, null)
                                                         .finally(() => this.loading = false);
                                                 },
                                                 { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                                             );
                                         } else {
                                             $wire.checkOut(null, null)
                                                 .finally(() => this.loading = false);
                                         }
                                     }
                                 }">
                                     <div>
                                         <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Selecciona Hora de Salida</label>
                                         <input type="time" wire:model="hora_salida" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                     </div>
                                     <button @click="doCheckOut" x-bind:disabled="loading" class="w-full py-3 px-4 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg shadow-orange-600/10 hover:shadow-orange-700/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                                         <span x-show="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                         <span x-text="loading ? 'Obteniendo ubicación...' : 'Registrar Salida'"></span>
                                     </button>
                                 </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fichajes History -->
            <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-2 bg-amber-500/10 text-amber-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        Historial de Fichajes (Últimos 30 días)
                    </h3>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-white/5">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/5">
                                <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                                <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Hora Entrada</th>
                                <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Hora Salida</th>
                                <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Horas Trabajadas</th>
                                <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @forelse($recentFichajes as $fichaje)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                    <td class="p-4 text-sm font-bold text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($fichaje->fecha)->translatedFormat('l, d \d\e F') }}
                                    </td>
                                    <td class="p-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $fichaje->hora_entrada ? \Carbon\Carbon::parse($fichaje->hora_entrada)->format('H:i') : '-' }}
                                    </td>
                                    <td class="p-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $fichaje->hora_salida ? \Carbon\Carbon::parse($fichaje->hora_salida)->format('H:i') : '-' }}
                                    </td>
                                    <td class="p-4 text-sm font-semibold text-amber-600 dark:text-amber-400">
                                        @if($fichaje->hora_entrada && $fichaje->hora_salida)
                                            @php
                                                $in = \Carbon\Carbon::parse($fichaje->hora_entrada);
                                                $out = \Carbon\Carbon::parse($fichaje->hora_salida);
                                                $mins = $in->diffInMinutes($out);
                                                $hours = floor($mins / 60);
                                                $remMins = $mins % 60;
                                                echo "{$hours}h {$remMins}m";
                                            @endphp
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-4 text-sm text-right">
                                        <button wire:click="editFichaje({{ $fichaje->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            Editar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Aún no has registrado ningún fichaje.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <x-filament::modal id="edit-fichaje-modal" width="md">
                <x-slot name="heading">
                    Editar Fichaje del {{ $editingFecha ? \Carbon\Carbon::parse($editingFecha)->format('d/m/Y') : '' }}
                </x-slot>

                <div class="space-y-4 py-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Hora de Entrada</label>
                        <input type="time" wire:model="editingHoraEntrada" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Hora de Salida</label>
                        <input type="time" wire:model="editingHoraSalida" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'edit-fichaje-modal' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="warning" wire:click="updateFichaje">
                            Guardar Cambios
                        </x-filament::button>
                    </div>
                </x-slot>
            </x-filament::modal>
        @endif
    </div>
</x-filament-panels::page>
