<x-filament-widgets::widget>
    @php
        $messages = $this->getUnreadMessages();
    @endphp

    @if($messages->count() > 0)
        <div class="flex flex-col gap-4">
            @foreach($messages as $msg)
                <x-filament::section class="border-orange-500 border-2 bg-orange-50 dark:bg-orange-950/20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <x-filament::icon
                                icon="heroicon-o-exclamation-triangle"
                                class="h-6 w-6 text-orange-500"
                            />
                            <div>
                                <h3 class="text-base font-bold text-orange-700 dark:text-orange-400">
                                    @if($msg->gasolinera)
                                        Nuevo mensaje de contacto para la gasolinera {{ $msg->gasolinera->Nombre }}
                                    @else
                                        Nuevo mensaje de contacto desde la página principal
                                    @endif
                                </h3>
                                <p class="text-sm text-orange-600 dark:text-orange-300">
                                    De: {{ $msg->nombre }} ({{ $msg->email }}) - {{ $msg->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        
                        <x-filament::button
                            tag="a"
                            color="warning"
                            href="{{ url('/admin/contacto-mensajes/' . $msg->id) }}"
                        >
                            Leer Mensaje
                        </x-filament::button>
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    @endif
</x-filament-widgets::widget>
