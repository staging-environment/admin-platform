<?php
use App\Models\Empleado;

$empleados = Empleado::whereNotNull('virtus_codigo')->whereNull('apellidos')->get();
$count = 0;
foreach ($empleados as $emp) {
    // If name contains spaces, split it
    if (strpos(trim($emp->nombre), ' ') !== false) {
        $parts = explode(' ', trim($emp->nombre));
        $nombre = array_shift($parts);
        $apellidos = count($parts) > 0 ? implode(' ', $parts) : null;
        
        $emp->nombre = $nombre;
        $emp->apellidos = $apellidos;
    }
    
    // Also generate dummy DNI if missing
    if (empty($emp->dni)) {
        $emp->dni = 'VIRTUS-' . str_pad($emp->virtus_codigo, 4, '0', STR_PAD_LEFT);
    }
    
    $emp->save();
    $count++;
}
echo "Fixed $count employees.\n";
