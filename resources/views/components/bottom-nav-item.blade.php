@props(['route', 'icon', 'label'])

@php
    $isActive = request()->routeIs($route) || request()->routeIs(explode('.', $route)[0] . '.*');
@endphp

<a href="{{ route($route) }}"
   class="bottom-nav-item flex flex-col items-center gap-1 py-1 px-3 transition-colors duration-150 {{ $isActive ? 'text-member-blue font-semibold' : 'text-member-slate hover:text-member-ink font-medium' }}">
    <i data-lucide="{{ $icon }}" class="w-[22px] h-[22px] transition-transform duration-150 {{ $isActive ? 'scale-105' : '' }}"></i>
    <span class="text-[11px] leading-none">{{ $label }}</span>
</a>
