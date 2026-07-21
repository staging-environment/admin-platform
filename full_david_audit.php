<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "====================================================\n";
echo "       INFORME DE BÚSQUEDA COMPLETA: 'DAVID'\n";
echo "====================================================\n\n";

echo "1. BUSCANDO EN BASE DE DATOS (Todas las tablas y campos texto):\n";
$tables = DB::select("SHOW TABLES");
$dbMatches = 0;
foreach ($tables as $t) {
    $tableName = array_values((array)$t)[0];
    try {
        $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
        $query = DB::table($tableName);
        $hasStringCols = false;
        foreach ($columns as $col) {
            $type = DB::getSchemaBuilder()->getColumnType($tableName, $col);
            if (in_array($type, ['string', 'text', 'varchar', 'char'])) {
                if (!$hasStringCols) {
                    $query->where($col, 'LIKE', '%david%');
                    $hasStringCols = true;
                } else {
                    $query->orWhere($col, 'LIKE', '%david%');
                }
            }
        }
        if ($hasStringCols) {
            $results = $query->get();
            if ($results->count() > 0) {
                $dbMatches += $results->count();
                echo "  [COINCIDENCIA EN BD] Tabla '$tableName' ({$results->count()} registros):\n";
                foreach ($results as $r) {
                    print_r($r);
                }
            }
        }
    } catch (\Throwable $e) {
        // skip
    }
}
if ($dbMatches === 0) {
    echo "  -> (0 coincidencias encontradas en la Base de Datos)\n";
}

echo "\n2. BUSCANDO EN ARCHIVOS DE LOG DE LARAVEL (storage/logs/*):\n";
$logFiles = glob(storage_path('logs/*.log'));
$laravelLogMatches = 0;
foreach ($logFiles as $lf) {
    $content = file_get_contents($lf);
    preg_match_all('/.*david.*/i', $content, $matches);
    if (!empty($matches[0])) {
        $laravelLogMatches += count($matches[0]);
        echo "  [COINCIDENCIA EN LOG] " . basename($lf) . " (" . count($matches[0]) . " líneas):\n";
        foreach (array_slice($matches[0], 0, 10) as $m) {
            echo "    - " . trim($m) . "\n";
        }
    }
}
if ($laravelLogMatches === 0) {
    echo "  -> (0 coincidencias en storage/logs/*)\n";
}

echo "\n3. BUSCANDO EN HISTORIAL DE COMMITS DE GIT:\n";
$gitCommits = shell_exec("git log --all --grep='david' -i --oneline");
$gitAuthors = shell_exec("git log --all --author='david' -i --oneline");
echo "Commits por mensaje 'david':\n" . ($gitCommits ?: "  -> Ninguno\n");
echo "Commits por autor 'david':\n" . ($gitAuthors ?: "  -> Ninguno\n");

echo "\n4. BUSCANDO EN RECURSOS HUMANOS / DOCUMENTOS ANEXOS:\n";
$docsDavid = DB::table('empleado_documentos')
    ->where('nombre', 'LIKE', '%david%')
    ->orWhere('file_path', 'LIKE', '%david%')
    ->get();
echo "Documentos asociados a David: " . $docsDavid->count() . "\n";
foreach ($docsDavid as $dd) {
    print_r($dd);
}

echo "\n====================================================\n";
echo "       FIN DE AUDITORÍA INTEGRAL DE 'DAVID'\n";
echo "====================================================\n";
