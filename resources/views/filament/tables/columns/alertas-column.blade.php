<div x-data="{}" onclick="event.stopPropagation()" style="position: relative !important; z-index: 20 !important; display: flex !important; align-items: center !important; justify-content: center !important; width: 100 !important;">
    @php
        $record = $getRecord();
    @endphp
    @include('filament.components.alerts-badge', ['record' => $record, 'showStatus' => false])
</div>
