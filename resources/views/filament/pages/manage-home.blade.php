<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap gap-3 justify-end">
            <x-filament::button type="submit">
                Guardar Cambios
            </x-filament::button>
        </div>
    </form>

    <div class="pt-6 border-t border-gray-200 dark:border-white/10 space-y-4">
        <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">Mensajes de Contacto Globales</h2>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
