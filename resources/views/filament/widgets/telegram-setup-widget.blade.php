<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-2">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 rounded-lg">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        ⚠️ Configuración de Telegram Pendiente
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Tienes activo el permiso para recibir alertas de precios de la competencia, pero aún no has vinculado tu cuenta de Telegram. Sigue estos sencillos pasos:
                    </p>
                    <ol class="mt-2 list-decimal list-inside space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <li>Asegúrate de tener la app de <b>Telegram instalada</b> en tu móvil.</li>
                        <li>Entra en tu <a href="{{ route('profile.edit') }}" class="text-amber-600 dark:text-amber-400 underline font-semibold hover:text-amber-500">Perfil de Usuario</a> y añade tu número de teléfono móvil.</li>
                        <li>Busca el bot <b>@utrecar_alertas_bot</b> en Telegram (o pulsa en <a href="https://t.me/utrecar_alertas_bot" target="_blank" class="text-amber-600 dark:text-amber-400 underline font-semibold hover:text-amber-500">t.me/utrecar_alertas_bot</a>).</li>
                        <li>Inicia el bot y pulsa en <b>📱 Compartir Teléfono</b>.</li>
                    </ol>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
