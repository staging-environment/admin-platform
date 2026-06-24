@php
    $record = $getRecord();
    $state = $getState();
    $downloadUrl = route('admin.recursos_humanos.descargar_archivo', ['path' => $record->file_path]);
    $canEdit = auth()->user()->can('editar_documentacion_empleados');
@endphp

<span class="flex items-center justify-between gap-x-2 w-full" style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; width: 100% !important; min-width: 0 !important; flex-wrap: nowrap !important;">
    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate" title="{{ $state }}" style="flex-grow: 1 !important; min-width: 0 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; display: inline-block !important;">
        {{ $state }}
    </span>
    <span class="flex items-center gap-x-1 flex-shrink-0" style="display: flex !important; flex-direction: row !important; align-items: center !important; gap: 4px !important; flex-shrink: 0 !important; flex-wrap: nowrap !important;">
        <!-- Previsualizar -->
        <button type="button" 
                wire:click="mountTableAction('preview', '{{ $record->id }}')" 
                class="p-1 rounded-lg text-sky-600 hover:bg-sky-50 dark:text-sky-400 dark:hover:bg-sky-950/30 transition-colors" 
                style="display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                title="Previsualizar">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
        </button>

        <!-- Descargar -->
        <a href="{{ $downloadUrl }}" 
           target="_blank" 
           class="p-1 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950/30 transition-colors" 
           style="display: inline-flex !important; align-items: center !important; justify-content: center !important;"
           title="Descargar">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
        </a>

        @if($canEdit)
            <!-- Editar -->
            <button type="button" 
                    wire:click="mountTableAction('edit', '{{ $record->id }}')" 
                    class="p-1 rounded-lg text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-950/30 transition-colors" 
                    style="display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                    title="Editar Documento">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.83 20.082a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
            </button>

            <!-- Borrar -->
            <button type="button" 
                    wire:click="mountTableAction('delete', '{{ $record->id }}')" 
                    class="p-1 rounded-lg text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30 transition-colors" 
                    style="display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                    title="Borrar Documento">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 9m-4.72 0-.34-9m9.02-3.25a29.071 29.071 0 0 0-7.811-.57l-1.557-1.91a1.5 1.5 0 0 0-1.154-.532H9.28a1.5 1.5 0 0 0-1.154.532l-1.557 1.91a29.07 29.07 0 0 0-7.812.57m13.886 0L19 19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L5.34 6.22m13.886 0H3" />
                </svg>
            </button>
        @endif
    </span>
</span>
