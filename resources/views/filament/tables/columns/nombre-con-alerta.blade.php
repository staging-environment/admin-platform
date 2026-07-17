<div class="flex items-center gap-3 px-4 py-3" @click.stop>
    @php
        $record = $getRecord();
        $nombre = trim($record->nombre);
        $apellidos = trim($record->apellidos ?? '');

        if (empty($apellidos)) {
            $name = mb_strtoupper($nombre);
        } else {
            $parts = preg_split('/\s+/', $apellidos);
            $primerApellido = array_shift($parts);
            $segundoApellido = count($parts) > 0 ? implode(' ', $parts) : '';

            if ($segundoApellido !== '') {
                $name = mb_strtoupper($primerApellido) . ' ' . mb_strtoupper($segundoApellido) . ', ' . mb_strtoupper($nombre);
            } else {
                $name = mb_strtoupper($primerApellido) . ', ' . mb_strtoupper($nombre);
            }
        }
    @endphp
    
    <!-- El badge de alertas -->
    @include('filament.components.alerts-badge', ['record' => $record])
    
    <!-- El nombre del empleado con enlace a la ficha -->
    <a href="{{ \App\Filament\Resources\Empleados\EmpleadoResource::getUrl('view', ['record' => $record]) }}" 
       class="font-bold text-sm text-gray-900 dark:text-white hover:underline">
        {{ $name }}
    </a>
</div>
