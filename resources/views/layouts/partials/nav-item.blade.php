@props(['route', 'icon', 'label', 'badge' => null])

@php
    $active = request()->routeIs($route . '*');
@endphp

<a href="{{ route($route) }}" 
   class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all group relative {{ $active ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
    
    <div class="{{ $active ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">
        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
    </div>
    
    <span class="font-bold text-sm whitespace-nowrap transition-all duration-300 origin-left"
          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
        {{ $label }}
    </span>

    {{-- Badge Notifikasi --}}
    @if($badge && $badge > 0)
        <span class="ml-auto bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full min-w-[20px] text-center transition-all duration-300"
              :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
            {{ $badge > 99 ? '99+' : $badge }}
        </span>
    @endif

    <!-- Tooltip saat collapsed -->
    <div x-show="sidebarCollapsed" 
         class="absolute left-14 bg-slate-900 text-white text-xs font-bold px-3 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50 pointer-events-none shadow-xl">
        {{ $label }}
        @if($badge && $badge > 0)
            <span class="ml-2 bg-rose-500 px-2 py-0.5 rounded-full">{{ $badge }}</span>
        @endif
    </div>
</a>