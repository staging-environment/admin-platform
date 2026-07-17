<?php
include __DIR__ . '/../vendor/autoload.php';
$app = include __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
print_r(App\Models\Empleado::orderBy('apellidos', 'asc')->pluck('apellidos')->toArray());
