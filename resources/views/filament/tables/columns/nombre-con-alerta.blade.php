<div x-data="{}" onclick="event.stopPropagation()" style="position: relative !important; z-index: 20 !important; display: flex !important; align-items: center !important; gap: 8px !important; justify-content: flex-start !important; text-align: left !important; width: 100% !important;">
    @php
        $record = $getRecord();
        $apellidos = mb_strtoupper(trim($record->apellidos ?? ''));
        if (empty($apellidos)) {
            $apellidos = '—';
        }
    @endphp
    
    <!-- El badge de alertas -->
    @include('filament.components.alerts-badge', ['record' => $record])
    
    <!-- Los apellidos del empleado con enlace a la ficha -->
    <a href="{{ \App\Filament\Resources\Empleados\EmpleadoResource::getUrl('view', ['record' => $record]) }}" 
       style="font-weight: bold !important; font-size: 14px !important; color: currentColor !important; text-decoration: none !important; position: relative !important; z-index: 21 !important; text-align: left !important; display: inline-block !important;"
       onmouseover="this.style.textDecoration='underline'"
       onmouseout="this.style.textDecoration='none'">
        {{ $apellidos }}
    </a>
</div>
