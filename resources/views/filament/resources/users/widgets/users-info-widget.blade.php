<x-filament-widgets::widget>
    <div style="display: flex; align-items: flex-start; gap: 12px; padding: 16px;" class="bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800/30 rounded-2xl text-sm text-blue-800 dark:text-blue-400">
        <svg style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;" class="text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div style="display: block;">
            <strong class="font-bold" style="display: block; margin-bottom: 4px;">Información de gestión de usuarios:</strong>
            <span style="display: block; margin-bottom: 4px;">• <strong>Borrado Físico:</strong> Si elimina un usuario desde este listado, el borrado se realizará de forma <u>definitiva y física</u>, eliminando tanto el usuario como su perfil de empleado y todos los documentos adjuntos (DNI, contratos, etc.) de forma irreversible del servidor.</span>
            <span style="display: block;">• <strong>Restauración:</strong> Solo es posible restaurar aquellos usuarios con rol de <u>Empleado</u> que hayan sido dados de baja (borrado lógico) desde el menú de Recursos Humanos.</span>
        </div>
    </div>
</x-filament-widgets::widget>
