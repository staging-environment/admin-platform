<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== 1. AUDITING CONTACT MESSAGES (contacto_mensajes) ===\n";
if (Schema::hasTable('contacto_mensajes')) {
    $msgs = DB::table('contacto_mensajes')->orderBy('created_at', 'desc')->get();
    echo "Total Contact Messages: " . $msgs->count() . "\n";
    foreach ($msgs as $m) {
        echo "  - ID: {$m->id} | Name: " . ($m->nombre ?? 'N/A') . " | Email: " . ($m->email ?? 'N/A') . " | Station Code: " . ($m->gasolinera_codigo ?? 'None') . " | Date: " . ($m->created_at ?? 'N/A') . "\n";
        echo "    Message snippet: " . substr($m->mensaje ?? '', 0, 100) . "\n";
    }
}

echo "\n=== 2. AUDITING EMPLOYEE COMMENTS & NOTES (empleado_comentarios) ===\n";
if (Schema::hasTable('empleado_comentarios')) {
    $comments = DB::table('empleado_comentarios')->orderBy('created_at', 'desc')->get();
    echo "Total Employee Comments: " . $comments->count() . "\n";
    foreach ($comments as $c) {
        echo "  - ID: {$c->id} | Employee ID: " . ($c->empleado_id ?? 'N/A') . " | User ID: " . ($c->user_id ?? 'N/A') . " | Text: " . substr($c->comentario ?? '', 0, 100) . "\n";
    }
}

echo "\n=== 3. SEARCHING FOR COMPETITOR BRANDS / EXTERNAL GAS STATIONS IN DB ===\n";
$competitorBrands = ['Repsol', 'Cepsa', 'BP', 'Shell', 'Plenoil', 'Ballenoil', 'Petroprix', 'Galp', 'Disa', 'Eroski', 'Carrefour', 'Alcampo'];
foreach ($competitorBrands as $brand) {
    $foundInMsgs = DB::table('contacto_mensajes')->where('mensaje', 'LIKE', "%$brand%")->count();
    $foundInComments = DB::table('empleado_comentarios')->where('comentario', 'LIKE', "%$brand%")->count();
    if ($foundInMsgs > 0 || $foundInComments > 0) {
        echo "  [FOUND MATCH FOR '$brand'] -> Msgs: $foundInMsgs, Comments: $foundInComments\n";
    }
}
