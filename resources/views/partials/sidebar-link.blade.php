@php
    $activeRoutes = (array) $route;
    $isActive = false;
    foreach ($activeRoutes as $r) {
        if (request()->routeIs($r) || request()->routeIs(str_replace('.index', '.*', $r))) {
            $isActive = true; break;
        }
    }
    $primaryRoute = $activeRoutes[0];
@endphp
<a href="{{ route($primaryRoute) }}"
   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
          {{ $isActive
              ? 'bg-white text-ink-900 shadow-soft ring-1 ring-ink-100'
              : 'text-ink-600 hover:bg-white/60 hover:text-ink-900' }}">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
         class="h-5 w-5 {{ $isActive ? 'text-brand-500' : 'text-ink-400 group-hover:text-ink-700' }}">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
    </svg>
    <span>{{ $label }}</span>
    @if($isActive)
        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400"></span>
    @endif
</a>
