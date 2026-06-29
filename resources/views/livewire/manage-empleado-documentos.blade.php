<div class="space-y-6">
    {{-- Mensajes de estado --}}
    @if (session()->has('message'))
        <div class="p-4 rounded-lg bg-green-50 dark:bg-green-950/30 text-green-800 dark:text-green-400 text-sm border border-green-200 dark:border-green-800/30 shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-lg bg-red-50 dark:bg-red-950/30 text-red-800 dark:text-red-400 text-sm border border-red-200 dark:border-red-800/30 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulario para subir documentos (solo si tiene permisos de edición) --}}
    @if (auth()->user()->can('editar_documentacion_empleados'))
        <div class="p-5 border rounded-xl bg-gray-50 dark:bg-white/5 dark:border-white/10 space-y-4 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin='round' stroke-width='2' d='M12 4v16m8-8H4' />
                </svg>
                Añadir Nuevo Documento
            </h3>
            
            <form wire:submit.prevent="uploadDocument" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Tipo de Documento</label>
                    <select wire:model="tipo" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm">
                        <option value="DNI">DNI / NIE</option>
                        <option value="Certificados">Certificados</option>
                        <option value="Contratos">Contratos</option>
                        <option value="Titulaciones">Titulaciones</option>
                        <option value="Carnets">Carnets</option>
                        <option value="Otros">Otros documentos</option>
                    </select>
                    @error('tipo') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nombre del Documento</label>
                    <input type="text" wire:model="nombre" placeholder="Ej. DNI Cara A" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                    @error('nombre') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Archivo</label>
                        <input type="file" wire:model="file" class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-amber-50 dark:file:bg-amber-950/20 file:text-amber-700 dark:file:text-amber-400 hover:file:bg-amber-100 transition-all cursor-pointer" />
                        @error('file') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm h-[38px] min-w-[80px]">
                        <span wire:loading.remove wire:target="file">Subir</span>
                        <span wire:loading wire:target="file" class="flex items-center gap-1">
                            <svg class="animate-spin h-4.5 w-4.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Listado de documentos --}}
    <div class="border rounded-xl bg-white dark:bg-white/5 dark:border-white/10 overflow-hidden shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10 text-left text-sm">
            <thead class="bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-3.5">Nombre</th>
                    <th scope="col" class="px-6 py-3.5">Tipo</th>
                    <th scope="col" class="px-6 py-3.5">Fecha de Subida</th>
                    <th scope="col" class="px-6 py-3.5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-white/10 text-gray-800 dark:text-gray-200">
                @forelse ($this->documentos as $doc)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-all">
                        <td class="px-6 py-4 font-medium flex items-center gap-3">
                            {{-- Icono según tipo --}}
                            <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400">
                                @if ($doc->tipo === 'Contratos')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @elseif ($doc->tipo === 'DNI')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.333 0 4 .667 4 2v1H9v-1c0-1.333 2.667-2 4-2z" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                @endif
                            </div>
                            <span>{{ $doc->nombre }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/10 text-gray-800 dark:text-gray-200">
                                {{ $doc->tipo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">
                            {{ $doc->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-1.5">
                            {{-- Previsualizar --}}
                            @php
                                $extension = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                $url = route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]);
                            @endphp
                            
                            <a href="{{ $url }}" target="_blank" class="inline-flex items-center justify-center p-1.5 text-cyan-600 dark:text-cyan-400 hover:bg-cyan-50 dark:hover:bg-cyan-950/20 rounded-lg transition-all" title="Previsualizar">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            {{-- Descargar --}}
                            <a href="{{ route('admin.recursos_humanos.descargar_archivo', ['path' => $doc->file_path]) }}" target="_blank" class="inline-flex items-center justify-center p-1.5 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-950/20 rounded-lg transition-all" title="Descargar">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>

                            {{-- Eliminar --}}
                            @if (auth()->user()->can('editar_documentacion_empleados'))
                                <button type="button" wire:click="deleteDocument({{ $doc->id }})" wire:confirm="¿Estás seguro de que deseas eliminar este documento?" class="inline-flex items-center justify-center p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-lg transition-all" title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v4.5" />
                                </svg>
                                <span class="text-sm font-medium">No hay documentos registrados para este empleado.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
