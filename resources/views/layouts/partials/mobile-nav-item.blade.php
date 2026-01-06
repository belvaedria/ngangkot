@props(['route', 'icon', 'label', 'badge' => null])

@php
    $active = request()->routeIs($route . '*');
@endphp

<a href="{{ route($route) }}"
   class="relative flex flex-col items-center justify-center py-2 {{ $active ? 'text-blue-600' : 'text-slate-400' }} transition-colors">
    
    {{-- Badge notif mobile --}}
    @if($badge && $badge > 0)
        <span class="absolute top-0 right-1/4 bg-rose-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full min-w-[16px] text-center">
            {{ $badge > 9 ? '9+' : $badge }}
        </span>
    @endif
    
    <i data-lucide="{{ $icon }}" class="w-5 h-5 mb-1"></i>
    <span class="text-[10px] font-bold">{{ $label }}</span>
</a>
