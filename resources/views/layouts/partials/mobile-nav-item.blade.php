@props(['route', 'icon', 'label', 'requireAuth' => false])
@php
    $href = Route::has($route) ? route($route) : '#';
    $needsAuth = $requireAuth && !auth()->check();
@endphp

<a href="{{ $needsAuth ? 'javascript:void(0);' : $href }}" @if($needsAuth) data-intended="{{ $href }}" @endif class="block p-3 rounded-lg text-center {{ $needsAuth ? 'requires-auth' : '' }}">
    <div class="text-slate-600"><i data-lucide="{{ $icon }}" class="w-5 h-5 mx-auto"></i></div>
    <div class="text-xs font-bold text-slate-700 mt-1">{{ $label }}</div>
</a>