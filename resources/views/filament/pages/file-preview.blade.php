<div class="space-y-6">
    {{-- Header / Info Panel --}}
    <div class="flex items-start justify-between gap-4 p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
        <div class="space-y-1">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate max-w-md" title="{{ $name }}">
                {{ $name }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono truncate max-w-md">
                {{ $path }}
            </p>
        </div>
        <div class="text-right shrink-0">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">
                {{ strtoupper($extension ?: 'bin') }}
            </span>
            <div class="text-[11px] text-gray-400 mt-1">
                {{ $size }} | Modificado: {{ $last_modified }}
            </div>
        </div>
    </div>

    {{-- Preview Area --}}
    <div class="preview-container flex items-center justify-center min-h-[250px] max-h-[600px] overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-950/40 border border-gray-200 dark:border-white/10 p-2">
        @if ($isImage && $url)
            <div class="w-full h-full flex items-center justify-center p-2">
                <img src="{{ $url }}" alt="{{ $name }}" class="max-w-full max-h-[500px] object-contain rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
            </div>
        @elseif ($isPdf && $url)
            <div class="w-full h-[500px]">
                <object data="{{ $url }}" type="application/pdf" class="w-full h-full rounded-lg">
                    <iframe src="{{ $url }}" class="w-full h-full rounded-lg" frameborder="0">
                        <p class="text-sm text-gray-500 dark:text-gray-400 p-4 text-center">
                            Este navegador no soporta visualización de PDFs. 
                            <a href="{{ $url }}" download class="text-primary-600 dark:text-primary-400 underline font-semibold">Descargar archivo PDF</a>.
                        </p>
                    </iframe>
                </object>
            </div>
        @elseif ($canReadText && $textContent !== null)
            <div class="w-full max-h-[500px] overflow-auto rounded-lg bg-slate-900 border border-slate-800">
                <pre class="p-4 text-slate-100 text-xs font-mono leading-relaxed select-all"><code>{{ $textContent }}</code></pre>
            </div>
        @else
            <div class="text-center py-12 px-6 flex flex-col items-center justify-center space-y-4">
                <div class="p-4 rounded-full bg-gray-200 dark:bg-white/5 text-gray-400 dark:text-gray-300">
                    <x-heroicon-o-document class="w-16 h-16" />
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Previsualización no disponible
                    </p>
                    <p class="text-xs text-gray-400">
                        Este tipo de archivo no puede visualizarse en el navegador o supera el tamaño límite de 1MB.
                    </p>
                </div>
                <div class="pt-2">
                    <x-filament::button
                        icon="heroicon-m-arrow-down-tray"
                        wire:click="downloadFile('{{ addslashes($path) }}')"
                        size="sm"
                    >
                        Descargar Archivo
                    </x-filament::button>
                </div>
            </div>
        @endif
    </div>
</div>
