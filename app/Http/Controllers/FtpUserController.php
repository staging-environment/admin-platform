<?php

namespace App\Http\Controllers;

use App\Models\FtpUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FtpUserController extends Controller
{
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
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $path = 'ftp/' . $username;
        $request->file('file')->storeAs($path, $request->file('file')->getClientOriginalName(), 'public');

        return redirect()->back()->with('success', 'Archivo subido correctamente.');
    }

    public function download($username, $filename)
    {
        $path = 'ftp/' . $username . '/' . $filename;
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }
        return Storage::disk('public')->download($path);
    }

    public function deleteFile($username, $filename)
    {
        $path = 'ftp/' . $username . '/' . $filename;
        Storage::disk('public')->delete($path);
        return redirect()->back()->with('success', 'Archivo eliminado.');
    }

    public function store(Request $request)
    {
        // 1. Forzamos la validación del 'unique' apuntando a la conexión y tabla correctas
        $request->validate([
            'user'     => 'required|alpha_dash|unique:mariadb_ftp.ftp_users,user|max:50',
            'password' => 'required|min:6',
        ]);

        $username = $request->user;

        // 2. Ruta física real para Laravel (compatible con SFTP de DDEV)
        // Guardamos en storage/app/public/ftp para que sea accesible y persistente
        $storagePath = 'public/ftp/' . $username;
        $laravelLocalPath = storage_path('app/' . $storagePath);

        // 3. Creamos la carpeta física desde Laravel
        if (!File::exists($laravelLocalPath)) {
            File::makeDirectory($laravelLocalPath, 0755, true, true);
        }

        // 4. Insertamos el registro.
        // Mantenemos el registro por compatibilidad, aunque ahora usamos un usuario maestro SFTP
        FtpUser::create([
            'user'     => $username,
            'password' => $request->password,
            'dir'      => '/home/db/upload/' . $username,
            'uid'      => 1000,
            'gid'      => 1000,
        ]);

        return redirect()->back()->with('success', "Empleado '{$username}' creado correctamente. Ahora puedes subir sus archivos directamente.");
    }

    public function destroy($id)
    {
        // Buscamos al usuario en la base de datos
        $user = FtpUser::findOrFail($id);

        // Borramos su carpeta física
        $storagePath = 'public/ftp/' . $user->user;
        $laravelLocalPath = storage_path('app/' . $storagePath);

        if (File::exists($laravelLocalPath)) {
            File::deleteDirectory($laravelLocalPath);
        }

        $user->delete();
        return redirect()->back()->with('success', 'Acceso y directorio eliminados correctamente.');
    }
}
