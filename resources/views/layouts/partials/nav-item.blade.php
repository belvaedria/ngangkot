@props(['route', 'icon', 'label', 'requireAuth' => false])

@php
    $active = request()->routeIs($route . '*');
    // If the named route exists use it, otherwise construct a reasonable path so link is clickable (placeholder page)
    $href = Route::has($route) ? route($route) : url('/' . str_replace('.', '/', $route));
    $needsAuth = $requireAuth && !auth()->check();
@endphp

<a href="{{ $needsAuth ? 'javascript:void(0);' : $href }}" 
   @if($needsAuth) data-intended="{{ $href }}" @endif
   class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all group relative {{ $active ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }} {{ $needsAuth ? 'requires-auth' : '' }}">
    
    <div class="{{ $active ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">
        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
    </div>
    
    <span class="font-bold text-sm whitespace-nowrap transition-all duration-300 origin-left"
          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
        {{ $label }}
    </span>

    <!-- Tooltip saat collapsed (Opsional, biar UX bagus) -->
    <div x-show="sidebarCollapsed" 
         class="absolute left-14 bg-slate-900 text-white text-xs font-bold px-3 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50 pointer-events-none shadow-xl">
        {{ $label }}
    </div>
</a>