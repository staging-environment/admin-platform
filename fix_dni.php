<?php
use App\Models\Empleado;

$count = Empleado::where('dni', 'LIKE', 'VIRTUS-%')->update(['dni' => null]);
echo "Set $count dummy DNIs to null.\n";
