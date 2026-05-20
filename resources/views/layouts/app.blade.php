<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">
<div class="min-h-full">
    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-ink-900/40 backdrop-blur-sm lg:hidden" @click="sidebarOpen=false" style="display:none"></div>

    {{-- Sidebar --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-canvas px-4 py-5 transition-transform duration-200 ease-out lg:translate-x-0">
        {{-- Brand --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-2">
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-ink-900 text-brand-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                    <path d="M12 2 4 7v10l8 5 8-5V7l-8-5Zm0 2.3 5.6 3.5L12 11.3 6.4 7.8 12 4.3Zm-6 5.4 5 3.1v6.4l-5-3.1V9.7Zm12 0v6.4l-5 3.1v-6.4l5-3.1Z"/>
                </svg>
            </span>
            <span class="font-display text-lg font-bold tracking-tight text-ink-900">Estatly</span>
        </a>

        {{-- Account card --}}
        <div class="mt-5 flex items-center gap-3 rounded-2xl bg-white p-3 ring-1 ring-ink-100/80 shadow-soft">
            <div class="grid h-10 w-10 place-items-center rounded-full bg-brand-100 font-display text-sm font-bold text-ink-900">AD</div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-ink-900">Admin User</p>
                <p class="truncate text-[11px] text-ink-500">Portfolio Manager</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4 text-ink-400"><path stroke-linecap="round" stroke-linejoin="round" d="m8 9 4-4 4 4M8 15l4 4 4-4"/></svg>
        </div>

        {{-- Navigation --}}
        <nav class="mt-6 flex-1 space-y-1 text-sm">
            <p class="eyebrow px-3 pb-2">Main Menu</p>
            @include('partials.sidebar-link', [
                'route' => 'dashboard',
                'label' => 'Dashboard',
                'icon'  => 'M3 13 12 4l9 9M5 11v9a1 1 0 0 0 1 1h3v-6h6v6h3a1 1 0 0 0 1-1v-9'
            ])
            @include('partials.sidebar-link', [
                'route' => 'properties.index',
                'label' => 'Properties',
                'icon'  => 'M4 21h16M5 21V7l7-4 7 4v14M9 9h6M9 13h6M9 17h6'
            ])

            <p class="eyebrow px-3 pb-2 pt-6">Admin</p>
            @include('partials.sidebar-link', [
                'route' => 'admin.valuations.index',
                'label' => 'Valuations',
                'icon'  => 'M12 7v10M9 10h6M9 14h6M5 4h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z'
            ])
            @include('partials.sidebar-link', [
                'route' => 'admin.rentals.index',
                'label' => 'Rentals',
                'icon'  => 'M3 12 12 3l9 9M5 10v10h14V10M9 21v-6h6v6'
            ])

            <p class="eyebrow px-3 pb-2 pt-6">Preference</p>
            <a href="#" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-ink-600 transition hover:text-ink-900">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-5 w-5 text-ink-400 group-hover:text-ink-700"><path stroke-linecap="round" stroke-linejoin="round" d="M10.3 3.6a1.5 1.5 0 0 1 3.4 0l.2 1a7 7 0 0 1 2 .8l.9-.5a1.5 1.5 0 0 1 2 .6l.6 1a1.5 1.5 0 0 1-.4 2l-.8.6a7 7 0 0 1 0 2.2l.8.6c.7.5.9 1.5.4 2.2l-.6.9a1.5 1.5 0 0 1-2 .6l-.9-.5a7 7 0 0 1-2 .8l-.2 1a1.5 1.5 0 0 1-3.4 0l-.2-1a7 7 0 0 1-2-.8l-.9.5a1.5 1.5 0 0 1-2-.6l-.6-1a1.5 1.5 0 0 1 .4-2l.8-.6a7 7 0 0 1 0-2.2l-.8-.6a1.5 1.5 0 0 1-.4-2l.6-1a1.5 1.5 0 0 1 2-.6l.9.5a7 7 0 0 1 2-.8l.2-1Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                <span>Settings</span>
            </a>
            <a href="#" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-ink-600 transition hover:text-ink-900">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-5 w-5 text-ink-400 group-hover:text-ink-700"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.5v.01M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.7.3-1 1-1 1.7v.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>Help Center</span>
            </a>
        </nav>

        {{-- Bottom promo --}}
        <div class="mt-4 rounded-2xl bg-ink-900 p-4 text-white">
            <div class="flex items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-brand-400/20 text-brand-300">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M11 2 3 14h7l-1 8 8-12h-7l1-8Z"/></svg>
                </span>
                <p class="font-display text-sm font-semibold">POC Build</p>
            </div>
            <p class="mt-2 text-[11px] leading-relaxed text-ink-300">Laravel 12 · PostgreSQL · Leaflet polygons. Spin up Cyberjaya properties in seconds.</p>
            <a href="{{ route('properties.create') }}" class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-full bg-brand-400 px-3 py-1.5 text-[12px] font-semibold text-ink-900 hover:bg-brand-300">+ New Property</a>
        </div>
    </aside>

    {{-- Main column --}}
    <div class="lg:pl-72">
        {{-- Topbar --}}
        <header class="sticky top-0 z-30 flex h-20 items-center gap-3 bg-canvas/85 px-4 backdrop-blur lg:px-8">
            <button type="button" class="grid h-10 w-10 place-items-center rounded-xl bg-white text-ink-500 ring-1 ring-ink-100 hover:text-ink-900 lg:hidden" @click="sidebarOpen=true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
            </button>

            <div class="min-w-0 flex-1">
                <h1 class="font-display text-xl font-bold text-ink-900 sm:text-2xl">@yield('header', View::yieldContent('title'))</h1>
                <p class="truncate text-xs text-ink-500">@yield('subheader', '')</p>
            </div>

            <form method="GET" action="{{ route('properties.index') }}" class="relative hidden md:block">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                <input type="text" name="search" placeholder="Search properties..." class="h-10 w-80 rounded-full border-0 bg-white pl-11 pr-20 text-sm shadow-soft ring-1 ring-ink-100 placeholder:text-ink-400 focus:ring-2 focus:ring-brand-300">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md bg-ink-100 px-1.5 py-0.5 text-[10px] font-medium text-ink-500">⌘ F</span>
            </form>

            <div class="flex items-center gap-2">
                @yield('actions')
                <button class="grid h-10 w-10 place-items-center rounded-full bg-white text-ink-500 ring-1 ring-ink-100 hover:text-ink-900">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                </button>
                <div class="grid h-10 w-10 place-items-center rounded-full bg-brand-400 font-display text-sm font-bold text-ink-900">AD</div>
            </div>
        </header>

        <main class="px-4 pb-10 lg:px-8">
            @if(session('status'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl bg-brand-100/80 px-4 py-3 text-sm text-brand-800 ring-1 ring-brand-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mt-0.5 h-5 w-5"><path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
                    <p class="font-medium">Please review the highlighted fields:</p>
                    <ul class="mt-1 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
