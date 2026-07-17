<div style="display: flex; align-items: center; gap: 12px; justify-content: flex-start; text-align: left; width: 100%;">
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
       style="font-weight: bold; font-size: 14px; color: currentColor; text-decoration: none;"
       onmouseover="this.style.textDecoration='underline'"
       onmouseout="this.style.textDecoration='none'">
        {{ $name }}
    </a>
</div>
