<?php

namespace App\Http\Controllers;

use App\Models\FtpUser;
use App\Services\FtpPermissionsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            // Comprobamos si el directorio existe antes de intentar contar archivos
            if (Storage::disk('public')->exists($path)) {
                $user->files_count = count(Storage::disk('public')->files($path));
            } else {
                $user->files_count = 0;
            }
        }

        return view('ftp.index', compact('ftpUsers'));
    }

    public function show($username)
    {
        $ftpUser = FtpUser::where('user', $username)->firstOrFail();
        $path = 'ftp/' . $username;

        // Comprobamos si el directorio existe antes de intentar listar archivos
        if (!Storage::disk('public')->exists($path)) {
            return view('ftp.show', compact('ftpUser'))->with('fileList', []);
        }

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
        $role     = $request->input('role');
        $targetDir = '/home/ftpusers/' . $username; // Directorio de producción

        // 2. NO creamos el directorio aquí. Pure-FTPd lo hará en la primera conexión.
        // Eliminado: File::makeDirectory($targetDir, 0755, true, true);

        // 3. Insertamos el registro en Base de Datos
        try {
            $user = FtpUser::create([
                'user'         => $username,
                'password'     => $request->password,
                'dir'          => $targetDir,
                'role'         => $role,
                'uid'          => 1000, // UID de 'developer'
                'gid'          => 33,   // GID de 'www-data'
                'can_upload'   => $request->boolean('can_upload', true),
                'can_download' => $request->boolean('can_download', true),
                'can_delete'   => $request->boolean('can_delete', true),
            ]);

            // 4. Aplicación de la capa de seguridad (Permisos y Grupos) de producción
            // Ahora FtpPermissionsManager::apply() manejará la ausencia del directorio.
            if (FtpPermissionsManager::apply($user)) { // Solo pasamos el objeto $user
                return redirect()->back()->with('success', "Empleado '{$username}' creado como {$role} correctamente. Los permisos de sistema de archivos se aplicarán tras la primera conexión FTP.");
            }

            return redirect()->back()->with('error', 'Usuario creado, pero hubo un problema aplicando los permisos de sistema de archivos.');

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

        // La eliminación del directorio también debería ser manejada por FtpPermissionsManager
        // o un servicio dedicado que use sudo de forma segura.
        // Por ahora, se mantiene la lógica de eliminación recursiva, pero sin sudo.
        if (!empty($laravelLocalPath) && is_dir($laravelLocalPath)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($laravelLocalPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                @$todo($fileinfo->getRealPath()); // @ para suprimir errores si no hay permisos
            }

            @rmdir($laravelLocalPath); // @ para suprimir errores si no hay permisos
        }

        $user->delete();
        return redirect()->back()->with('success', 'Acceso y directorio eliminados correctamente.');
    }
}
