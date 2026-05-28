<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Previa: {{ $table }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Check theme preference from parent window or localStorage
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <!-- Simple scrollbar custom styles -->
    <style>
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col font-sans p-6">
    <div class="grow flex flex-col max-w-7xl w-full mx-auto bg-white dark:bg-white/5 rounded-2xl shadow-lg border border-gray-200 dark:border-white/10 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 flex items-center justify-between shrink-0">
            <div class="space-y-1">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 font-mono">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    {{ $table }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Total de registros en tabla: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $totalCount }}</span>
                </p>
            </div>
            <button 
                onclick="window.close()" 
                class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-white/10 transition"
            >
                Cerrar Ventana
            </button>
        </div>

        {{-- Toolbar: Search and Pagination --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-white/10 flex flex-col md:flex-row items-center justify-between gap-4 bg-white dark:bg-gray-900/50 shrink-0">
            {{-- Search Form --}}
            <form method="GET" action="" class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="rowSearch" 
                        value="{{ $rowSearch }}" 
                        placeholder="Buscar en registros..." 
                        class="block w-full pl-9 rounded-lg border-gray-300 dark:border-white/10 dark:bg-white/5 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 text-xs"
                    />
                </div>
                @if($rowSearch !== '')
                    <a href="?page=1" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline">Limpiar</a>
                @endif
                <button 
                    type="submit" 
                    class="px-4 py-2 text-xs font-bold rounded-lg text-white transition-all shadow-sm"
                    style="background-color: #d97706;"
                >
                    Buscar
                </button>
            </form>

            {{-- Pagination controls --}}
            <div class="flex items-center gap-3 shrink-0">
                <a 
                    href="{{ $currentPage > 1 ? '?page=' . ($currentPage - 1) . '&rowSearch=' . urlencode($rowSearch) : '#' }}"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 transition flex items-center gap-1.5 {{ $currentPage <= 1 ? 'opacity-50 pointer-events-none' : 'hover:bg-gray-50 dark:hover:bg-white/10' }}"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Anterior
                </a>

                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 font-mono">
                    Página {{ $currentPage }} de {{ max(1, $totalPages) }}
                </span>

                <a 
                    href="{{ $currentPage < $totalPages ? '?page=' . ($currentPage + 1) . '&rowSearch=' . urlencode($rowSearch) : '#' }}"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 transition flex items-center gap-1.5 {{ $currentPage >= $totalPages ? 'opacity-50 pointer-events-none' : 'hover:bg-gray-50 dark:hover:bg-white/10' }}"
                >
                    Siguiente
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        {{-- Table Body --}}
        <div class="p-6 overflow-auto grow bg-gray-50/50 dark:bg-gray-950/20">
            @if(count($rows) === 0)
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Esta tabla no contiene registros coincidentes.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10 shadow-sm bg-white dark:bg-white/5">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @foreach($columns as $column)
                                    <th class="p-3 font-mono border-r border-gray-200 dark:border-white/5 last:border-r-0">{{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10 font-mono">
                            @foreach($rows as $row)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                    @foreach($columns as $column)
                                        @php
                                            $val = $row[$column] ?? null;
                                            $is_null = is_null($val);
                                            if (!$is_null) {
                                                if (is_array($val) || is_object($val)) {
                                                    $display = json_encode($val);
                                                } else {
                                                    $display = (string)$val;
                                                }
                                                $is_truncated = strlen($display) > 60;
                                                $truncated = $is_truncated ? mb_substr($display, 0, 60) . '...' : $display;
                                            } else {
                                                $display = 'NULL';
                                                $truncated = 'NULL';
                                                $is_truncated = false;
                                            }
                                        @endphp
                                        <td class="p-3 max-w-[250px] truncate border-r border-gray-200 dark:border-white/5 last:border-r-0" title="{{ $is_truncated ? $display : '' }}">
                                            @if($is_null)
                                                <span class="text-gray-400 italic">NULL</span>
                                            @else
                                                {{ $truncated }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 flex items-center justify-between shrink-0">
            <div class="text-xs text-gray-400 dark:text-gray-500">
                @if($totalCount > 0)
                    Mostrando registros del {{ (($currentPage - 1) * 50) + 1 }} al {{ min($totalCount, $currentPage * 50) }} de {{ $totalCount }}
                @else
                    Mostrando 0 registros
                @endif
            </div>
            <button 
                onclick="window.close()" 
                class="px-4 py-2 text-xs font-bold bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 text-gray-600 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-white/10 transition"
            >
                Cerrar
            </button>
        </div>
    </div>
</body>
</html>
