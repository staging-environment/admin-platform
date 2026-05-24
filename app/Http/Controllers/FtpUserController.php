<?php

namespace App\Http\Controllers;

use App\Models\FtpUser;
use App\Services\FtpPermissionsManager; // Reintroducido de producción
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // Reintroducido de producción
use Illuminate\Support\Facades\Storage; // Necesario para gestionar archivos
use Symfony\Component\HttpFoundation\StreamedResponse; // Necesario para descargas

class FtpUserController extends Controller
{
    /**
     * Muestra la lista de usuarios y el formulario de alta.
     */
    public function index()
    {
        // Trae los usuarios ordenados desde la base de datos secundaria 'mariadb_ftp'
        $ftpUsers = FtpUser::orderBy('user', 'asc')->get();

        // Para cada usuario, contamos cuántos archivos tiene
        foreach ($ftpUsers as $user) {
            $path = 'ftp/' . $user->user;
            $user->files_count = count(Storage::disk('public')->files($path));
        }

        return view('ftp.index', compact('ftpUsers'));
    }

    public function show($username)
    {
        $ftpUser = FtpUser::where('user', $username)->firstOrFail();
        $path = 'ftp/' . $username;
        $files = Storage::disk('public')->files($path);

        $fileList = array_map(function($file) use ($username) {
            return [
                'name' => basename($file),
                'path' => $file,
                'size' => round(Storage::disk('public')->size($file) / 1024, 2) . ' KB',
                'url'  => Storage::disk('public')->url($file)
            ];
        }, $files);

        return view('ftp.show', compact('ftpUser', 'fileList'));
    }

    public function upload(Request $request, $username)
    {
        $ftpUser = FtpUser::where('user', $username)->firstOrFail();
        if (!$ftpUser->can_upload) {
            return redirect()->back()->with('error', 'No tienes permiso para subir archivos.');
        }

        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $path = 'ftp/' . $username;
        $request->file('file')->storeAs($path, $request->file('file')->getClientOriginalName(), 'public');

        return redirect()->back()->with('success', 'Archivo subido correctamente.');
    }

    public function download($username, $filename)
    {
        $ftpUser = FtpUser::where('user', $username)->firstOrFail();
        if (!$ftpUser->can_download) {
            return redirect()->back()->with('error', 'No tienes permiso para descargar archivos.');
        }

        $path = 'ftp/' . $username . '/' . $filename;
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }
        return Storage::disk('public')->download($path);
    }

    public function deleteFile($username, $filename)
    {
        $ftpUser = FtpUser::where('user', $username)->firstOrFail();
        if (!$ftpUser->can_delete) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar archivos.');
        }

        $path = 'ftp/' . $username . '/' . $filename;
        Storage::disk('public')->delete($path);
        return redirect()->back()->with('success', 'Archivo eliminado.');
    }

    public function store(Request $request)
    {
        // 1. Validación estricta: Combinamos la validación de roles y permisos granulares
        $request->validate([
            'user'         => 'required|alpha_dash|unique:mariadb_ftp.ftp_users,user|max:50',
            'password'     => 'required|min:6',
            'role'         => 'required|in:editor,viewer', // Validación de rol de producción
            'can_upload'   => 'boolean',
            'can_download' => 'boolean',
            'can_delete'   => 'boolean',
        ]);

        $username = $request->user;
        $role     = $request->input('role'); // Obtenemos el rol de producción
        $targetDir = '/home/ftpusers/' . $username; // Directorio de producción

        // 2. Creación del directorio en el servidor (lógica de producción)
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        // 3. Registro en Base de Datos (Combinamos roles y permisos granulares)
        try {
            $user = FtpUser::create([
                'user'         => $username,
                'password'     => $request->password,
                'dir'          => $targetDir, // Usamos el directorio de producción
                'role'         => $role, // Incluimos el rol de producción
                'uid'          => 33, // UID de producción
                'gid'          => 33, // GID de producción
                'can_upload'   => $request->boolean('can_upload', true),
                'can_download' => $request->boolean('can_download', true),
                'can_delete'   => $request->boolean('can_delete', true),
            ]);

            // 4. Aplicación de la capa de seguridad (Permisos y Grupos) de producción
            if (FtpPermissionsManager::apply($user, $role)) {
                return redirect()->back()->with('success', "Empleado '{$username}' creado como {$role} correctamente.");
            }

            return redirect()->back()->with('error', 'Usuario creado, pero hubo un problema aplicando los permisos.');

        } catch (\Exception $e) {
            Log::error("Error crítico al crear usuario FTP: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error inesperado al procesar la solicitud.');
        }
    }

    /**
     * Revoca acceso y elimina directorios asociados.
     */
    public function destroy($id)
    {
        // Buscamos al usuario en la base de datos
        $user = FtpUser::findOrFail($id);

        // Borramos su carpeta física (lógica local para DDEV)
        $storagePath = 'public/ftp/' . $user->user;
        $laravelLocalPath = storage_path('app/' . $storagePath);

        if (File::exists($laravelLocalPath)) {
            File::deleteDirectory($laravelLocalPath);
        }

        $user->delete();
        return redirect()->back()->with('success', 'Acceso y directorio eliminados correctamente.');
    }
}
