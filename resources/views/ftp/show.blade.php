<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Archivos de: ') }} {{ $ftpUser->user }}
            </h2>
            <a href="{{ route('ftp.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Volver al listado</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="font-bold">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg flex justify-between items-center">
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="font-bold">&times;</button>
                </div>
            @endif

            {{-- Sección de Permisos del Usuario FTP --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Permisos de {{ $ftpUser->user }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <strong>Subir Archivos:</strong>
                            @if($ftpUser->can_upload)
                                <span class="text-green-500">✔ Sí</span>
                            @else
                                <span class="text-red-500">✖ No</span>
                            @endif
                        </div>
                        <div>
                            <strong>Descargar Archivos:</strong>
                            @if($ftpUser->can_download)
                                <span class="text-green-500">✔ Sí</span>
                            @else
                                <span class="text-red-500">✖ No</span>
                            @endif
                        </div>
                        <div>
                            <strong>Eliminar Archivos:</strong>
                            @if($ftpUser->can_delete)
                                <span class="text-green-500">✔ Sí</span>
                            @else
                                <span class="text-red-500">✖ No</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulario para subir archivos (condicional) --}}
            @if($ftpUser->can_upload)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Subir nuevo archivo</h3>
                    <form action="{{ route('ftp.upload', $ftpUser->user) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-4">
                        @csrf
                        <input type="file" name="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-xs uppercase tracking-widest hover:bg-gray-700">
                            Subir
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-red-600">
                    <p>Este usuario no tiene permiso para subir archivos.</p>
                </div>
            </div>
            @endif

            {{-- Listado de archivos --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Listado de archivos</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tamaño</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($fileList as $file)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $file['name'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file['size'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            @if($ftpUser->can_download)
                                                <a href="{{ route('ftp.download', [$ftpUser->user, $file['name']]) }}" class="text-indigo-600 hover:text-indigo-900">Descargar</a>
                                            @else
                                                <span class="text-gray-400">Descargar</span>
                                            @endif

                                            @if($ftpUser->can_delete)
                                                <form action="{{ route('ftp.deleteFile', [$ftpUser->user, $file['name']]) }}" method="POST" class="inline" onsubmit="return confirm('¿Borrar archivo?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">Eliminar</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No hay archivos en esta carpeta.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
