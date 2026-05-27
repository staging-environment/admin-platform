<div class="p-6 text-center space-y-4">
    <div class="inline-flex items-center justify-center p-3 rounded-full bg-danger-50 dark:bg-danger-950/30 text-danger-600 dark:text-danger-400">
        <x-heroicon-o-exclamation-triangle class="w-12 h-12" />
    </div>
    <div class="space-y-1">
        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Error al cargar la previsualización</h4>
        <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">
            No se pudo leer el archivo seleccionado. Es posible que el archivo esté corrupto o que no tengas los permisos necesarios.
        </p>
    </div>
    @if(isset($error))
        <div class="p-3 bg-danger-500/10 text-danger-600 dark:text-danger-400 text-xs font-mono rounded-lg max-w-md mx-auto truncate">
            {{ $error }}
        </div>
    @endif
</div>
