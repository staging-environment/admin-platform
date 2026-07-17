<div x-data="{}" onclick="event.stopPropagation()" style="position: relative !important; z-index: 20 !important; display: flex !important; align-items: center !important; gap: 12px !important; justify-content: flex-start !important; text-align: left !important; width: 100% !important;">
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
       style="font-weight: bold !important; font-size: 14px !important; color: currentColor !important; text-decoration: none !important; position: relative !important; z-index: 21 !important; text-align: left !important; display: inline-block !important;"
       onmouseover="this.style.textDecoration='underline'"
       onmouseout="this.style.textDecoration='none'">
        {{ $name }}
    </a>
</div>
