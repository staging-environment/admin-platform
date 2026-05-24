<?php

namespace App\Http\Controllers;

use App\Models\FtpUser;
use App\Services\FtpPermissionsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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
    public function store(Request $request)
    {
        // 1. Validación estricta
        $request->validate([
            'user'     => 'required|alpha_dash|unique:mariadb_ftp.ftp_users,user|max:50',
            'password' => 'required|min:6',
            'role'     => 'required|in:editor,viewer',
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
