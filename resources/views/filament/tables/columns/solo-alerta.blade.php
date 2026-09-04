<div x-data="{}" onclick="event.stopPropagation()" style="position: relative !important; z-index: 20 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;">
    @php
        $record = $getRecord();
    @endphp
    @include('filament.components.alerts-badge', ['record' => $record])
</div>
