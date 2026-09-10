@props(['active' => false])

@php
$classes = ($active ?? false)
    ? 'block w-full px-4 py-2 text-start text-sm leading-5 font-bold text-indigo-600 bg-indigo-50/80 hover:bg-indigo-100/80 focus:outline-none transition duration-150 ease-in-out'
    : 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-center justify-between w-full">
        <span>{{ $slot }}</span>
        @if($active ?? false)
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 ms-2"></span>
        @endif
    </div>
</a>
