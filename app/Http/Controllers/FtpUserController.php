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
        $ftpUsers = FtpUser::all();
        return view('ftp.index', compact('ftpUsers'));
    }

    /**
     * Crea un nuevo acceso SFTP con permisos granulares.
     */
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
        // 1. Validación estricta
        $request->validate([
            'user'     => 'required|alpha_dash|unique:mariadb_ftp.ftp_users,user|max:50',
            'password' => 'required|min:6',
            'role'     => 'required|in:editor,viewer',
            'user'         => 'required|alpha_dash|unique:mariadb_ftp.ftp_users,user|max:50',
            'password'     => 'required|min:6',
            'can_upload'   => 'boolean',
            'can_download' => 'boolean',
            'can_delete'   => 'boolean',
        ]);

        $username = $request->user;
        $role     = $request->input('role');
        $targetDir = '/home/ftpusers/' . $username;

        // 2. Creación del directorio en el servidor
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        // 3. Registro en Base de Datos (Ahora incluye el rol)
        try {
            $user = FtpUser::create([
                'user'     => $username,
                'password' => $request->password, // Nota: Asegúrate de manejar el hash si tu sistema lo requiere
                'dir'      => $targetDir,
                'role'     => $role,
                'uid'      => 33,
                'gid'      => 33,
            ]);
        // 4. Insertamos el registro.
        // Mantenemos el registro por compatibilidad, aunque ahora usamos un usuario maestro SFTP
        FtpUser::create([
            'user'         => $username,
            'password'     => $request->password,
            'dir'          => '/home/db/upload/' . $username, // Mantener la ruta lógica para el FTP
            'uid'          => 1000,
            'gid'          => 1000,
            'can_upload'   => $request->boolean('can_upload', true), // Default true
            'can_download' => $request->boolean('can_download', true), // Default true
            'can_delete'   => $request->boolean('can_delete', true),   // Default true
        ]);

            // 4. Aplicación de la capa de seguridad (Permisos y Grupos)
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
        $user = FtpUser::findOrFail($id);

        if (File::exists($user->dir)) {
            File::deleteDirectory($user->dir);
        }

        $user->delete();
        return redirect()->back()->with('success', 'Acceso y directorio eliminados correctamente.');
    }
}
