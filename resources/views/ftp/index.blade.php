<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg md:col-span-1">
                    <section>
                        <header>
                            <h3 class="text-lg font-medium text-gray-900">Crear Acceso SFTP</h3>
                        </header>

                        <form method="post" action="{{ route('ftp.store') }}" class="mt-6 space-y-6">
                            @csrf
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nombre de Usuario</label>
                                <input name="user" type="text" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" required />
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Contraseña</label>
                                <input name="password" type="password" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" required />
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Rol de Acceso</label>
                                <select name="role" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full">
                                    <option value="editor">Editor (Lectura/Escritura)</option>
                                    <option value="viewer">Viewer (Solo Lectura)</option>
                                </select>
                            </div>

                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md">Dar de Alta</button>
                        </form>
                    </section>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg md:col-span-2">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @forelse($ftpUsers as $ftpUser)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium">{{ $ftpUser->user }}</td>
                                <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $ftpUser->role === 'editor' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($ftpUser->role ?? 'viewer') }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('ftp.show', $ftpUser->user) }}" class="text-indigo-600">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center p-4">No hay usuarios.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
