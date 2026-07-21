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

    @if ($family === 'incapacidad')
        <div class="p-5 border rounded-xl bg-gray-50 dark:bg-white/5 dark:border-white/10 space-y-4 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Seleccionar Tipo de Incapacidad
            </h3>
            
            <div class="flex flex-col md:flex-row gap-6 items-start md:items-center justify-between">
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200 cursor-pointer">
                        <input type="checkbox" wire:model="selectedIncapacidad" value="Físico" class="rounded border-gray-300 dark:border-white/10 text-amber-600 focus:ring-amber-500 shadow-sm" />
                        <span>Física</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200 cursor-pointer">
                        <input type="checkbox" wire:model="selectedIncapacidad" value="Psíquico" class="rounded border-gray-300 dark:border-white/10 text-amber-600 focus:ring-amber-500 shadow-sm" />
                        <span>Psíquica</span>
                    </label>
                </div>
                
                @if (auth()->user()->can('editar_documentacion_empleados'))
                    <button type="button" wire:click="saveIncapacidad" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                        Guardar Cambios
                    </button>
                @endif
            </div>
        </div>
    @else
        {{-- Formulario para subir documentos (solo si tiene permisos de edición) --}}
        @if (auth()->user()->can('editar_documentacion_empleados'))
            <div class="p-5 border rounded-xl bg-gray-50 dark:bg-white/5 dark:border-white/10 space-y-4 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Añadir Nuevo Documento
                </h3>
                
                @php
                    $options = [];
                    if ($family === 'dni') {
                        $options = ['DNI' => 'DNI'];
                    } elseif ($family === 'contratos') {
                        $options = ['Contratos' => 'Contrato'];
                    } elseif ($family === 'formacion') {
                        $options = [
                            'Prevención de riesgos laborales' => 'Prevención de riesgos laborales',
                            'Manipulación de alimentos' => 'Manipulación de alimentos',
                            'Otros' => 'Otros',
                        ];
                    } elseif ($family === 'discapacidad') {
                        $options = [
                            'Resolución Discapacidad' => 'Resolución Discapacidad',
                            'Dictamen Técnico' => 'Dictamen Técnico Facultativo',
                            'Certificado Discapacidad' => 'Certificado Discapacidad',
                        ];
                    } else {
                        $options = [
                            'DNI' => 'DNI',
                            'Contratos' => 'Contrato',
                            'Certificados' => 'Certificado',
                            'Titulaciones' => 'Titulación',
                            'Carnets' => 'Carnet',
                            'Resolución Discapacidad' => 'Resolución Discapacidad',
                            'Dictamen Técnico' => 'Dictamen Técnico',
                            'Certificado Discapacidad' => 'Certificado Discapacidad',
                            'Otros' => 'Otros documentos',
                        ];
                    }
                @endphp
                
                @if ($family === 'dni')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div class="flex flex-col">
                            <span class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Archivo</span>
                            <label class="inline-flex items-center justify-center px-4 py-2 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-semibold cursor-pointer hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all border border-gray-300 dark:border-white/10 h-[38px] w-full">
                                Seleccionar archivo
                                <input type="file" wire:model="file" class="hidden" />
                            </label>
                            @if ($file)
                                <span class="text-[10px] text-green-600 dark:text-green-400 mt-1 truncate max-w-[150px] font-medium">✓ Archivo cargado</span>
                            @endif
                            @error('file') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Fecha de caducidad</label>
                            <input type="date" wire:model="fecha_caducidad_dni" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                            @error('fecha_caducidad_dni') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <button type="button" wire:click="uploadDocument" class="w-full inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm h-[38px]">
                                <span wire:loading.remove wire:target="file">Subir</span>
                                <span wire:loading wire:target="file" class="flex items-center gap-1">
                                    <svg class="animate-spin h-4.5 w-4.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                @elseif ($family === 'contratos')
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 items-end w-full">
                        <div class="flex flex-col">
                            <span class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Archivo</span>
                            <label class="inline-flex items-center justify-center px-4 py-2 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-semibold cursor-pointer hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all border border-gray-300 dark:border-white/10 h-[38px] w-full">
                                Seleccionar archivo
                                <input type="file" wire:model="file" class="hidden" />
                            </label>
                            @if ($file)
                                <span class="text-[10px] text-green-600 dark:text-green-400 mt-1 truncate max-w-[150px] font-medium">✓ Archivo cargado</span>
                            @endif
                            @error('file') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Tipo de Contrato</label>
                            <select wire:model.live="tipo_contrato" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm">
                                <option value="">Selecciona tipo...</option>
                                <option value="Indefinido">Fijo</option>
                                <option value="Eventual">Eventual</option>
                            </select>
                            @error('tipo_contrato') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Fecha de Inicio <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="fecha_inicio_contrato" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                            @error('fecha_inicio_contrato') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        @if ($tipo_contrato === 'Eventual')
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Fecha de Finalización <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="fecha_vencimiento_contrato" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('fecha_vencimiento_contrato') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Tipo de Jornada</label>
                            <select wire:model.live="tipo_jornada" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm">
                                <option value="Jornada completa">Jornada completa</option>
                                <option value="Media Jornada">Media Jornada</option>
                                <option value="Otros">Otros</option>
                            </select>
                            @error('tipo_jornada') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        @if ($tipo_jornada === 'Otros')
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Información adicional</label>
                                <input type="text" wire:model="tipo_jornada_otro" placeholder="Detalle de jornada" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('tipo_jornada_otro') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Ubicación de trabajo <span class="text-red-500">*</span></label>
                            <select wire:model="gasolinera_codigo" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm">
                                <option value="">Selecciona ubicación...</option>
                                @foreach(\App\Models\Gasolinera::pluck('Nombre', 'Codigo') as $codigo => $nombre)
                                    <option value="{{ $codigo }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                            @error('gasolinera_codigo') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Puesto <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="puesto" placeholder="Ej: Expendedor, Encargado..." class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                            @error('puesto') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <button type="button" wire:click="uploadDocument" class="w-full inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm h-[38px]">
                                <span wire:loading.remove wire:target="file">Subir</span>
                                <span wire:loading wire:target="file" class="flex items-center gap-1">
                                    <svg class="animate-spin h-4.5 w-4.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                @else
                    @if ($family === 'formacion')
                        <div class="grid grid-cols-1 {{ $tipo === 'Otros' ? 'md:grid-cols-4' : 'md:grid-cols-3' }} gap-4 items-end">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Tipo de Curso</label>
                                <select wire:model.live="tipo" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm">
                                    @foreach ($options as $val => $lbl)
                                        <option value="{{ $val }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                @error('tipo') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            @if ($tipo === 'Otros')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nombre del Curso</label>
                                    <input type="text" wire:model="nombre" placeholder="Ej. Curso Prevención" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                    @error('nombre') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Fecha de Realización</label>
                                <input type="date" wire:model="fecha_realizacion" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('fecha_realizacion') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex-1 flex flex-col">
                                    <span class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Archivo</span>
                                    <label class="inline-flex items-center justify-center px-4 py-2 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-semibold cursor-pointer hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all border border-gray-300 dark:border-white/10 h-[38px] w-full">
                                        Seleccionar archivo
                                        <input type="file" wire:model="file" class="hidden" />
                                    </label>
                                    @if ($file)
                                        <span class="text-[10px] text-green-600 dark:text-green-400 mt-1 truncate max-w-[150px] font-medium">✓ Archivo cargado</span>
                                    @endif
                                    @error('file') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <button type="button" wire:click="uploadDocument" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm h-[38px] min-w-[80px]">
                                    <span wire:loading.remove wire:target="file">Subir</span>
                                    <span wire:loading wire:target="file" class="flex items-center gap-1">
                                        <svg class="animate-spin h-4.5 w-4.5 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Tipo de Documento</label>
                                <select wire:model="tipo" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm">
                                    @foreach ($options as $val => $lbl)
                                        <option value="{{ $val }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                @error('tipo') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nombre del Documento</label>
                                <input type="text" wire:model="nombre" placeholder="Ej. Certificado formación" class="w-full rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                @error('nombre') <span class="text-xs text-red-500 mt-1">{{ $message }} @enderror
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex-1 flex flex-col">
                                    <span class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Archivo</span>
                                    <label class="inline-flex items-center justify-center px-4 py-2 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-semibold cursor-pointer hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all border border-gray-300 dark:border-white/10 h-[38px] w-full">
                                        Seleccionar archivo
                                        <input type="file" wire:model="file" class="hidden" />
                                    </label>
                                    @if ($file)
                                        <span class="text-[10px] text-green-600 dark:text-green-400 mt-1 truncate max-w-[150px] font-medium">✓ Archivo cargado</span>
                                    @endif
                                    @error('file') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <button type="button" wire:click="uploadDocument" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm h-[38px] min-w-[80px]">
                                    <span wire:loading.remove wire:target="file">Subir</span>
                                    <span wire:loading wire:target="file" class="flex items-center gap-1">
                                        <svg class="animate-spin h-4.5 w-4.5 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        @endif

        {{-- Listado de documentos --}}
        <div class="border rounded-xl bg-white dark:bg-white/5 dark:border-white/10 overflow-hidden shadow-sm">
            <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-white/10 text-left text-xs">
                <thead class="bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    <tr>
                        @if ($family !== 'contratos' && $family !== 'dni')
                            <th scope="col" class="px-4 py-3 whitespace-nowrap w-full">Nombre</th>
                        @endif
                        @if ($family === 'contratos')
                            <th scope="col" class="px-2 py-2.5 whitespace-nowrap w-[10%]">Tipo</th>
                            <th scope="col" class="px-2 py-2.5 whitespace-nowrap w-[18%]">Inicio</th>
                            <th scope="col" class="px-2 py-2.5 whitespace-nowrap w-[20%]">Finalización</th>
                            <th scope="col" class="px-2 py-2.5 whitespace-nowrap w-[22%]">Jornada</th>
                        @else
                            @if ($family !== 'dni')
                                <th scope="col" class="px-4 py-3 whitespace-nowrap">Tipo</th>
                            @endif
                            <th scope="col" class="px-4 py-3 whitespace-nowrap {{ $family === 'dni' ? 'w-full' : '' }}">
                                @if ($family === 'dni')
                                    Fecha de Caducidad
                                @elseif ($family === 'formacion')
                                    Fecha de Realización
                                @else
                                    Fecha de Subida
                                @endif
                            </th>
                        @endif
                        <th scope="col" class="px-2.5 py-2.5 text-right whitespace-nowrap w-[30%]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10 text-gray-800 dark:text-gray-200">
                    @forelse ($this->documentos as $doc)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-all">
                            @if ($family !== 'contratos' && $family !== 'dni')
                                <td class="px-6 py-4 font-medium" style="width: 100%; min-width: 300px;">
                                    <div class="flex items-center gap-3">
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
                                        @if ($editingDocumentId === $doc->id && $family === 'formacion')
                                            @if ($edit_tipo === 'Otros')
                                                <div class="flex flex-col space-y-1.5 w-full">
                                                    <label class="block text-[10px] font-semibold text-gray-600 dark:text-gray-400">Nombre del Curso</label>
                                                    <input type="text" wire:model="edit_nombre" class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1 w-full" />
                                                    @error('edit_nombre') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-500">Auto ({{ $edit_tipo }})</span>
                                            @endif
                                        @else
                                            <span class="whitespace-nowrap truncate block max-w-[320px]" title="{{ $family === 'dni' ? $this->empleado->nombre . ' ' . $this->empleado->apellidos : $doc->nombre }}">
                                                @if ($family === 'dni')
                                                    {{ $this->empleado->nombre }} {{ $this->empleado->apellidos }}
                                                @else
                                                    {{ $doc->nombre }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            @endif
                            @if ($family === 'contratos')
                                <td class="px-3.5 py-3">
                                    @if ($editingDocumentId === $doc->id)
                                        <div class="flex flex-col space-y-2.5">
                                            <div>
                                                <label class="block text-[10px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Tipo</label>
                                                <select wire:model.live="edit_tipo_contrato" class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1 w-full font-medium">
                                                    <option value="Indefinido">Fijo</option>
                                                    <option value="Eventual">Eventual</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Archivo actual: <span class="font-normal text-gray-500">{{ basename($doc->file_path) }}</span></label>
                                                <label class="inline-flex items-center justify-center px-3 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-lg text-[10px] font-semibold cursor-pointer hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all border border-gray-300 dark:border-white/10">
                                                    Cambiar archivo (opcional)
                                                    <input type="file" wire:model="edit_file" class="hidden" />
                                                </label>
                                                @if ($edit_file)
                                                    <span class="text-[10px] text-green-600 dark:text-green-400 block mt-1">✓ Nuevo archivo seleccionado</span>
                                                @endif
                                                @error('edit_file') <span class="text-[10px] text-red-500 block mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($doc->tipo_contrato ?? $this->empleado->tipo_contrato) === 'Eventual' ? 'bg-amber-100 dark:bg-amber-950/30 text-amber-800 dark:text-amber-400' : 'bg-green-100 dark:bg-green-950/30 text-green-800 dark:text-green-400' }}">
                                            {{ ($doc->tipo_contrato ?? $this->empleado->tipo_contrato) === 'Eventual' ? 'Eventual' : 'Fijo' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                    @if ($editingDocumentId === $doc->id)
                                        <input type="date" wire:model="edit_fecha_inicio_contrato" class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1" />
                                        @error('edit_fecha_inicio_contrato') <span class="text-[10px] text-red-500 block mt-1">{{ $message }}</span> @enderror
                                    @else
                                        {{ $doc->fecha_inicio_contrato ? $doc->fecha_inicio_contrato->format('d/m/Y') : 'No especificada' }}
                                    @endif
                                </td>
                                <td class="px-3.5 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                    @if ($editingDocumentId === $doc->id)
                                        @if ($edit_tipo_contrato === 'Eventual')
                                            <input type="date" wire:model="edit_fecha_vencimiento_contrato" class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1" />
                                        @else
                                            -
                                        @endif
                                    @else
                                        @if (($doc->tipo_contrato ?? $this->empleado->tipo_contrato) === 'Eventual')
                                            @php
                                                $date = $doc->fecha_vencimiento_contrato ?? $this->empleado->fecha_vencimiento_contrato;
                                            @endphp
                                            {{ $date ? (\Carbon\Carbon::parse($date)->format('d/m/Y')) : 'No especificada' }}
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                                <td class="px-3.5 py-3">
                                    @if ($editingDocumentId === $doc->id)
                                        <div class="flex flex-col space-y-1.5">
                                            <select wire:model.live="edit_tipo_jornada" class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1">
                                                <option value="Jornada completa">Jornada completa</option>
                                                <option value="Media Jornada">Media Jornada</option>
                                                <option value="Otros">Otros</option>
                                            </select>
                                            @if ($edit_tipo_jornada === 'Otros')
                                                <input type="text" wire:model="edit_tipo_jornada_otro" placeholder="Detalle de jornada" class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1" />
                                            @endif
                                            <div class="pt-1 border-t border-gray-200 dark:border-gray-700">
                                                <label class="block text-[10px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Ubicación de trabajo <span class="text-red-500">*</span></label>
                                                <select wire:model="edit_gasolinera_codigo" class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1 w-full">
                                                    <option value="">Selecciona ubicación...</option>
                                                    @foreach(\App\Models\Gasolinera::pluck('Nombre', 'Codigo') as $codigo => $nombre)
                                                        <option value="{{ $codigo }}">{{ $nombre }}</option>
                                                    @endforeach
                                                </select>
                                                @error('edit_gasolinera_codigo') <span class="text-[10px] text-red-500 block mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Puesto <span class="text-red-500">*</span></label>
                                                <input type="text" wire:model="edit_puesto" placeholder="Ej: Expendedor, Encargado..." class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1 w-full" />
                                                @error('edit_puesto') <span class="text-[10px] text-red-500 block mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-700 dark:text-gray-300">
                                            @if (($doc->tipo_jornada ?? '') === 'Otros')
                                                {{ $doc->tipo_jornada_otro ?: 'Otros (no especificado)' }}
                                            @else
                                                {{ $doc->tipo_jornada ?: 'No especificada' }}
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            @else
                                @if ($family !== 'dni')
                                    <td class="px-6 py-4" style="width: auto !important; min-width: 90px;">
                                        @if ($editingDocumentId === $doc->id && $family === 'formacion')
                                            <select wire:model.live="edit_tipo" class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1 w-full font-medium">
                                                @foreach ($options as $val => $lbl)
                                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                                @endforeach
                                            </select>
                                            @error('edit_tipo') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-white/5 text-gray-800 dark:text-gray-300">
                                                {{ $doc->tipo }}
                                            </span>
                                        @endif
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs" style="{{ $family === 'dni' ? 'width: 100%;' : 'width: 140px !important; min-width: 140px !important;' }}">
                                    @if ($family === 'dni')
                                        @if ($editingDocumentId === $doc->id)
                                            <div class="flex flex-col space-y-2.5">
                                                <div>
                                                    <label class="block text-[10px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Fecha de Caducidad</label>
                                                    <input type="date" wire:model="edit_fecha_caducidad_dni" class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1" />
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Archivo actual: <span class="font-normal text-gray-500">{{ basename($doc->file_path) }}</span></label>
                                                    <label class="inline-flex items-center justify-center px-3 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-lg text-[10px] font-semibold cursor-pointer hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all border border-gray-300 dark:border-white/10">
                                                        Cambiar archivo (opcional)
                                                        <input type="file" wire:model="edit_file" class="hidden" />
                                                    </label>
                                                    @if ($edit_file)
                                                        <span class="text-[10px] text-green-600 dark:text-green-400 block mt-1">✓ Nuevo archivo seleccionado</span>
                                                    @endif
                                                    @error('edit_file') <span class="text-[10px] text-red-500 block mt-1">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        @else
                                            {{ $this->empleado->fecha_caducidad_dni ? $this->empleado->fecha_caducidad_dni->format('d/m/Y') : 'No especificada' }}
                                        @endif
                                    @elseif ($family === 'formacion')
                                        @if ($editingDocumentId === $doc->id)
                                            <div class="flex flex-col space-y-2">
                                                <div>
                                                    <label class="block text-[10px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Fecha Realización</label>
                                                    <input type="date" wire:model="edit_fecha_realizacion" class="rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-xs focus:border-amber-500 focus:ring-amber-500 shadow-sm py-1" />
                                                    @error('edit_fecha_realizacion') <span class="text-[10px] text-red-500 block mt-1">{{ $message }}</span> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Archivo actual: <span class="font-normal text-gray-500">{{ basename($doc->file_path) }}</span></label>
                                                    <label class="inline-flex items-center justify-center px-3 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-lg text-[10px] font-semibold cursor-pointer hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all border border-gray-300 dark:border-white/10">
                                                        Cambiar archivo (opcional)
                                                        <input type="file" wire:model="edit_file" class="hidden" />
                                                    </label>
                                                    @if ($edit_file)
                                                        <span class="text-[10px] text-green-600 dark:text-green-400 block mt-1">✓ Nuevo archivo seleccionado</span>
                                                    @endif
                                                    @error('edit_file') <span class="text-[10px] text-red-500 block mt-1">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        @else
                                            {{ $doc->fecha_realizacion ? $doc->fecha_realizacion->format('d/m/Y') : 'No especificada' }}
                                        @endif
                                    @else
                                        {{ $doc->created_at->timezone('Europe/Madrid')->format('d/m/Y H:i') }}
                                    @endif
                                </td>
                            @endif
                            <td class="whitespace-nowrap px-2.5 py-2.5 text-right space-x-1">
                                @if ($editingDocumentId === $doc->id)
                                    {{-- Guardar --}}
                                    <button type="button" wire:click="saveDocumentEdit" class="inline-flex items-center justify-center p-1.5 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-950/20 rounded-lg transition-all" title="Guardar">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    {{-- Cancelar --}}
                                    <button type="button" wire:click="cancelEdit" class="inline-flex items-center justify-center p-1.5 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-950/20 rounded-lg transition-all" title="Cancelar">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @else
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

                                    {{-- Editar --}}
                                    @if (auth()->user()->can('editar_documentacion_empleados') && ($family === 'contratos' || $family === 'dni'))
                                        <button type="button" wire:click="editDocument({{ $doc->id }})" class="inline-flex items-center justify-center p-1.5 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/20 rounded-lg transition-all" title="Editar">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Eliminar --}}
                                    @if (auth()->user()->can('editar_documentacion_empleados'))
                                        <button type="button" wire:click="deleteDocument({{ $doc->id }})" wire:confirm="¿Estás seguro de que deseas eliminar este documento?" class="inline-flex items-center justify-center p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-lg transition-all" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $family === 'dni' ? 3 : 4 }}" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
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
    @endif
</div>
</div>
