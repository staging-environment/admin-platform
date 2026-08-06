<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') === 'password-updated')
                <div class="p-4 sm:p-6 bg-emerald-500/10 border-2 border-emerald-500/40 rounded-xl text-emerald-900 dark:text-emerald-200 flex items-start gap-4 shadow-md transition-all">
                    <div class="p-2 bg-emerald-500/20 rounded-lg text-emerald-600 dark:text-emerald-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-emerald-800 dark:text-emerald-300">
                            ¡Contraseña actualizada correctamente!
                        </h3>
                        <p class="text-sm mt-1 text-emerald-800/90 dark:text-emerald-200/90 leading-relaxed">
                            Tu contraseña ha sido cambiada con éxito. Ya puedes acceder y navegar libremente por la plataforma.
                        </p>
                    </div>
                </div>
            @endif

            @if (session('warning') || (auth()->check() && \Illuminate\Support\Facades\Hash::check('1234', auth()->user()->password)))
                <div class="p-4 sm:p-6 bg-amber-500/10 border-2 border-amber-500/40 rounded-xl text-amber-900 dark:text-amber-200 flex items-start gap-4 shadow-md transition-all">
                    <div class="p-2 bg-amber-500/20 rounded-lg text-amber-600 dark:text-amber-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-amber-800 dark:text-amber-300">
                            ⚠️ Cambio de contraseña requerido
                        </h3>
                        <p class="text-sm mt-1 text-amber-800/90 dark:text-amber-200/90 leading-relaxed">
                            {{ session('warning') ?? 'Tu cuenta actualmente utiliza la contraseña por defecto (1234). Por motivos de seguridad, debes actualizar tu contraseña por una clave personal antes de poder navegar y acceder al resto de secciones de la plataforma.' }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if(!auth()->user()->hasRole('Empleado') && !auth()->user()->hasRole('empleado'))
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
