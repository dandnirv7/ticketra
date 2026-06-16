@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full px-4 py-3 text-start text-base font-black text-gray-900 bg-neo-yellow border-4 border-black rounded-xl shadow-neo-sm translate-x-[2px] translate-y-[2px] shadow-neo-active focus:outline-none transition-all'
            : 'block w-full px-4 py-3 text-start text-base font-bold text-gray-900 bg-white border-4 border-black rounded-xl shadow-neo-sm hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-neo-hover focus:outline-none transition-all';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
