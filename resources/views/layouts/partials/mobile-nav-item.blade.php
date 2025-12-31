@props(['route', 'icon', 'label'])

@php
    $active = request()->routeIs($route . '*');
@endphp

<button onclick="window.location='{{ route($route) }}'"
        class="flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-xl transition-all {{ $active ? 'text-blue-600 bg-blue-50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100' }}">
    <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
    <span class="text-[10px] font-bold">{{ $label }}</span>
</button>
