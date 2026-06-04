<?php

use Illuminate\Support\Facades\Route;
use App\Models\Gasolinera;
use App\Models\PreciosProducto;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Public\JobOfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Api\DataQueryController;
use App\Http\Controllers\Api\FilterController;
use App\Models\ContactoMensaje;
use Illuminate\Http\Request;

// --- SECCIÓN PÚBLICA ---
Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/seed-perms', function () {
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'gestion_comentarios_empleados']);
    \Spatie\Permission\Models\Role::findByName('Admin')->givePermissionTo('gestion_comentarios_empleados');
    \Spatie\Permission\Models\Role::findByName('Gestor')->givePermissionTo('gestion_comentarios_empleados');
    return 'Permisos actualizados';
});

Route::get('/home', function () {
    $gasolineras = [];
    $homeConfig = null;
    try {
        $homeConfig = \App\Models\HomeConfig::find(1);
        $gasolineras = Gasolinera::with('contenido')->get()->map(function ($estacion) {
            $estacion->diesel = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                ->where('CodigoProducto', '1')->value('PVP');
            $estacion->gasolina95 = PreciosProducto::where('CodigoEstacion', $estacion->Codigo)
                ->where('CodigoProducto', '2')->value('PVP');
            return $estacion;
        });
    } catch (\Exception $e) { report($e); }
    return view('welcome', compact('gasolineras', 'homeConfig'));
});

Route::get('/debug-db', function () {
    $configs = \DB::connection('mariadb')->table('home_configs')->get();
    return response()->json([
        'configs' => $configs
    ]);
});

Route::get('/debug-logs', function () {
    $logPath = storage_path('logs/laravel.log');
    if (!file_exists($logPath)) {
        return response()->json(['message' => 'No log file found']);
    }
    
    $handle = fopen($logPath, "r");
    if (!$handle) {
        return response()->json(['message' => 'Cannot open log file']);
    }
    
    $lines = [];
    fseek($handle, 0, SEEK_END);
    $pos = ftell($handle);
    $lastLine = "";
    
    while ($pos > 0 && count($lines) < 50) {
        fseek($handle, --$pos);
        $char = fgetc($handle);
        if ($char === "\n") {
            if ($lastLine !== "") {
                $lines[] = strrev($lastLine);
            }
            $lastLine = "";
        } else {
            $lastLine .= $char;
        }
    }
    if ($lastLine !== "") {
        $lines[] = strrev($lastLine);
    }
    fclose($handle);
    
    return response()->json([
        'logs' => array_reverse($lines)
    ]);
});

// Job offers routes
Route::get('/ofertas', [JobOfferController::class, 'index'])->name('offers.index');
Route::get('/ofertas/{id}', [JobOfferController::class, 'show'])->name('offers.show');
Route::post('/ofertas/{id}/apply', [JobOfferController::class, 'apply'])->name('offers.apply');

Route::get('/estacion/{slug}', function ($slug) {
    try {
        $estacion = Gasolinera::with('contenido')->get()->first(function ($e) use ($slug) {
            return \Illuminate\Support\Str::slug($e->Nombre) === $slug || $e->Codigo == $slug;
        });
        
        if (!$estacion) {
            return redirect('/');
        }
        
        $codigo = $estacion->Codigo;
        $estacion->diesel = PreciosProducto::where('CodigoEstacion', $codigo)->where('CodigoProducto', '1')->value('PVP');
        $estacion->gasolina95 = PreciosProducto::where('CodigoEstacion', $codigo)->where('CodigoProducto', '2')->value('PVP');
        $homeConfig = \App\Models\HomeConfig::find(1);
        return view('estacion-detalle', compact('estacion', 'homeConfig'));
    } catch (\Exception $e) { return redirect('/'); }
})->name('estacion.show');

Route::post('/estacion/{slug}/contacto', function (Request $request, $slug) {
    $estacion = Gasolinera::get()->first(function ($e) use ($slug) {
        return \Illuminate\Support\Str::slug($e->Nombre) === $slug || $e->Codigo == $slug;
    });
    
    if (!$estacion) abort(404);
    
    // Honeypot check: if filled, silently succeed without saving to DB
    if ($request->filled('website_url_check')) {
        return redirect()->back()->with('success', 'Tu mensaje ha sido enviado correctamente.');
    }
    
    $codigo = $estacion->Codigo;
    $request->validate([
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'mensaje' => 'required|string',
        'captcha_answer' => 'required|numeric',
        'captcha_token' => 'required|string',
    ]);

    try {
        $correctAnswer = decrypt($request->captcha_token);
        if ((int)$request->captcha_answer !== (int)$correctAnswer) {
            return redirect()->back()->withErrors(['captcha_answer' => 'La respuesta a la verificación de seguridad es incorrecta.'])->withInput();
        }
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['captcha_answer' => 'La verificación de seguridad ha fallado. Por favor, intente de nuevo.'])->withInput();
    }

    ContactoMensaje::create([
        'gasolinera_codigo' => $codigo,
        'nombre' => $request->nombre,
        'email' => $request->email,
        'mensaje' => $request->mensaje,
    ]);

    return redirect()->back()->with('success', 'Tu mensaje ha sido enviado correctamente.');
})->name('estacion.contacto');

Route::post('/contacto', function (Request $request) {
    // Honeypot check: if filled, silently succeed without saving to DB
    if ($request->filled('website_url_check')) {
        return redirect()->back()->with('success', 'Tu mensaje ha sido enviado correctamente.');
    }

    $request->validate([
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'mensaje' => 'required|string',
        'captcha_answer' => 'required|numeric',
        'captcha_token' => 'required|string',
    ]);

    try {
        $correctAnswer = decrypt($request->captcha_token);
        if ((int)$request->captcha_answer !== (int)$correctAnswer) {
            return redirect()->back()->withErrors(['captcha_answer' => 'La respuesta a la verificación de seguridad es incorrecta.'])->withInput();
        }
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['captcha_answer' => 'La verificación de seguridad ha fallado. Por favor, intente de nuevo.'])->withInput();
    }

    ContactoMensaje::create([
        'gasolinera_codigo' => null,
        'nombre' => $request->nombre,
        'email' => $request->email,
        'mensaje' => $request->mensaje,
    ]);

    return redirect()->back()->with('success', 'Tu mensaje ha sido enviado correctamente.');
})->name('home.contacto');

// --- SECCIÓN PRIVADA (BACKEND) ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/dashboard', '/admin/dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/candidaturas/{id}/descargar-cv', function ($id) {
        $application = \App\Models\JobApplication::findOrFail($id);
        $disk = \Illuminate\Support\Facades\Storage::disk('private_cvs');
        if (!$disk->exists($application->cv_path)) {
            abort(404, 'Archivo no encontrado');
        }
        return $disk->download($application->cv_path);
    })->name('admin.cv.download');

    Route::get('/admin/recursos-humanos-adjuntos/descargar-archivo', function (Request $request) {
        $path = $request->query('path');
        if (!$path) {
            abort(400, 'Ruta no especificada');
        }
        
        // Evitar Directory Traversal
        if (str_contains($path, '..')) {
            abort(403, 'Acceso denegado');
        }
        
        $disk = \Illuminate\Support\Facades\Storage::disk('local');
        if (!$disk->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }
        return $disk->download($path);
    })->name('admin.recursos_humanos.descargar_archivo');

    Route::get('/admin/recursos-humanos-adjuntos/ver-archivo', function (Request $request) {
        $path = $request->query('path');
        if (!$path) {
            abort(400, 'Ruta no especificada');
        }
        
        // Evitar Directory Traversal
        if (str_contains($path, '..')) {
            abort(403, 'Acceso denegado');
        }
        
        $disk = \Illuminate\Support\Facades\Storage::disk('local');
        if (!$disk->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }
        
        $mimeType = $disk->mimeType($path);
        
        return response()->file($disk->path($path), [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline'
        ]);
    })->name('admin.recursos_humanos.ver_archivo');



    // Gestión de usuarios de la plataforma y del repositorio FTP
    Route::middleware('permission:manage-users')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);


    });

    // Rutas de la API para el panel
    Route::prefix('api')->group(function () {
        Route::get('/databases/tables', [DataQueryController::class, 'getTables']);
        Route::post('/data/query', [DataQueryController::class, 'query']);
        Route::post('/data/custom-query', [DataQueryController::class, 'customQuery']);
        Route::get('/data/schema', [DataQueryController::class, 'getSchema']);
        Route::apiResource('/filters', FilterController::class);
    });

    // Vista previa de tablas (SII, Virtusgesnet, etc.)
    Route::get('/admin/db-preview/{connection}/{table}', function (Request $request, $connection, $table) {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        // Validar que la conexión esté en nuestra lista de permitidas
        if (!in_array($connection, ['virtusgesnet', 'sii'])) {
            abort(400, 'Conexión no válida');
        }
        
        $rowSearch = $request->input('rowSearch', '');
        $currentPage = (int)$request->input('page', 1);
        if ($currentPage < 1) $currentPage = 1;
        $pageSize = 50;

        try {
            $columnsResult = DB::connection($connection)->select("SHOW COLUMNS FROM `$table` ");
            $columns = [];
            foreach ($columnsResult as $col) {
                $colArray = (array)$col;
                $columns[] = $colArray['Field'] ?? $colArray['field'] ?? null;
            }
            $columns = array_filter($columns);

            $query = DB::connection($connection)->table($table);

            if ($rowSearch !== '') {
                $query->where(function($q) use ($columns, $rowSearch) {
                    foreach ($columns as $index => $column) {
                        if ($index === 0) {
                            $q->where($column, 'like', '%' . $rowSearch . '%');
                        } else {
                            $q->orWhere($column, 'like', '%' . $rowSearch . '%');
                        }
                    }
                });
            }

            $totalCount = $query->count();
            $totalPages = (int)ceil($totalCount / $pageSize);

            $rows = $query->offset(($currentPage - 1) * $pageSize)
                ->limit($pageSize)
                ->get()
                ->map(fn($row) => (array)$row)
                ->toArray();

            return view('admin.db-preview', compact('table', 'columns', 'rows', 'totalCount', 'totalPages', 'currentPage', 'rowSearch'));

        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    })->name('db.preview');
});

require __DIR__.'/auth.php';
