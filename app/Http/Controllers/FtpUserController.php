<?php

namespace App\Http\Controllers;

use App\Models\FtpUser;
use Illuminate\Http\Request;

class FtpUserController extends Controller
{
    public function index()
    {
        $ftpUsers = FtpUser::orderBy('user', 'asc')->get();
        return view('ftp.index', compact('ftpUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user'     => 'required|alpha_dash|unique:ftp_users,user|max:50',
            'password' => 'required|min:6',
        ]);

        // 1. Ciframos la contraseña con el sistema que vuestro FTP lee perfectamente
        $salt = '$6$rounds=5000$' . bin2hex(random_bytes(8)) . '$';
        $passwordCifrada = crypt($request->password, $salt);

        // 2. Insertamos el registro en vuestra base de datos MariaDB
        FtpUser::create([
            'user'     => $request->user,
            'password' => $passwordCifrada,
            'dir'      => '/home/ftpusers/' . $request->user,
            'uid'      => 1000,
            'gid'      => 1000,
        ]);

        // Ya no hacemos mkdir desde Laravel. Al tener CreateHomeDir activo en el FTP
        // y la raíz con permisos 777, el FTP creará la carpeta solo al loguearse.

        return redirect()->back()->with('success', "Empleado '{$request->user}' creado correctamente.");
    }

    public function destroy($id)
    {
        $user = FtpUser::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'Acceso FTP eliminado correctamente.');
    }
}
