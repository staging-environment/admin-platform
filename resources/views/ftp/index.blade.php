<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Usuarios FTP - Repositorio Utrecar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl "mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="font-bold">&times;</button>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg md:col-span-1">
                <section>
                    <header>
                        <h3 class="text-lg font-medium text-gray-900">Crear Acceso FTP</h3>
                        <p class="mt-1 text-sm text-gray-600">Añade un nuevo empleado. Se creará una carpeta aislada automáticamente.</p>
                    </header>

                    <form method="post" action="{{ route('ftp.store') }}" class="mt-6 space-y-6">
                        @csrf

                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="user">Nombre de Usuario (Login)</label>
                            <input id="user" name="user" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" value="{{ old('user') }}" required autofocus autocomplete="off" placeholder="ej: ana_admin" />
                            @error('user')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="password">Contraseña FTP</label>
                            <input id="password" name="password" type="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required autocomplete="new-password" />
                            @error('password')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Dar de Alta') }}
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg md:col-span-2">
                <header class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Empleados con acceso</h3>
                    <p class="mt-1 text-sm text-gray-600">Lista de usuarios activos en el repositorio independiente.</p>
                </header>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario (FTP)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruta Raíz Aislada</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($ftpUsers as $ftpUser)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $ftpUser->user }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                    {{ $ftpUser->dir }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('ftp.destroy', $ftpUser->id) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres quitarle el acceso FTP a este empleado? No se borrarán sus archivos.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                                            Revocar Acceso
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No hay usuarios creados en el FTP todavía.
                                </td>
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
