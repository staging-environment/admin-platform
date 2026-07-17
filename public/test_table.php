<?php
include __DIR__ . '/../vendor/autoload.php';
$app = include __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$table = new \Filament\Tables\Table(new \App\Filament\Resources\Empleados\Pages\ListEmpleados());
$table = \App\Filament\Resources\Empleados\Tables\EmpleadosTable::configure($table);

echo "Default sort column: " . $table->getDefaultSortColumn() . "\n";
echo "Default sort direction: " . $table->getDefaultSortDirection() . "\n";

$query = \App\Models\Empleado::query();
// In Filament v3, table sorting is applied during execution, let's dump the orders of the query after calling the page's sorting logic if possible, or let's inspect the orders array on the query builder.
// Let's run a select and dump
print_r($query->orderBy($table->getDefaultSortColumn(), $table->getDefaultSortDirection())->toSql());
echo "\n";
