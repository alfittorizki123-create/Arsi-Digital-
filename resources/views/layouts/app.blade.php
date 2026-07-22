<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bapenda Riau') - Arsip Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: true }" class="bg-background text-on-surface font-body-md text-body-md antialiased overflow-hidden flex h-screen">

    {{-- SideNavBar --}}
    <nav
        :class="sidebarOpen ? 'w-sidebar-width' : 'w-16'"
        class="fixed left-0 top-0 h-screen bg-primary dark:bg-primary-container flex flex-col pt-3 pb-stack-lg z-20 transition-all duration-200 overflow-hidden">
        {{-- Brand / Logo --}}
        <div class="px-2 mb-0" x-show="sidebarOpen">
            <img src="{{ asset('images/logo-bapenda.png') }}" alt="Logo" class="w-full max-w-[120px] h-auto object-contain mx-auto">
        </div>
        <div class="px-2 mb-0 flex justify-center" x-show="!sidebarOpen" @@click="sidebarOpen = true">
            <span class="material-symbols-outlined text-on-primary text-2xl cursor-pointer hover:text-on-primary/80">menu</span>
        </div>

        {{-- Divider --}}
        <div class="mx-2 border-t border-on-primary/20 mb-0"></div>

        {{-- Navigation Links --}}
        <div class="flex-1 flex flex-col mt-2">
            @php
                $navItems = [
                    ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Beranda', 'match' => 'dashboard'],
                    ['route' => 'arsips.index', 'icon' => 'inventory_2', 'label' => 'Daftar Arsip', 'match' => 'arsips.*'],
                    ['route' => 'laporan', 'icon' => 'analytics', 'label' => 'Laporan', 'match' => 'laporan*'],
                    ['route' => 'pengaturan', 'icon' => 'settings', 'label' => 'Pengaturan', 'match' => 'pengaturan*'],
                ];
            @endphp
            @foreach ($navItems as $item)
                @php
                    $isActive = request()->routeIs($item['match']);
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-stack-md px-3 py-stack-md transition-all whitespace-nowrap @if($isActive) bg-primary-container/20 dark:bg-primary/20 border-l-4 border-on-primary text-on-primary font-bold opacity-90 @else text-on-primary/70 dark:text-on-primary-container/70 hover:bg-primary-container/10 dark:hover:bg-primary/10 @endif"
                   :title="!sidebarOpen ? '{{ $item['label'] }}' : ''">
                    <span class="material-symbols-outlined shrink-0" @if($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
                    <span x-show="sidebarOpen">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-stack-md px-3 py-stack-md text-on-primary/70 dark:text-on-primary-container/70 hover:bg-primary-container/10 dark:hover:bg-primary/10 transition-colors text-left whitespace-nowrap"
                        :title="!sidebarOpen ? 'Keluar' : ''">
                    <span class="material-symbols-outlined shrink-0">logout</span>
                    <span x-show="sidebarOpen">Keluar</span>
                </button>
            </form>
        </div>
    </nav>

    {{-- Main Content Wrapper --}}
    <div
        :class="sidebarOpen ? 'ml-sidebar-width' : 'ml-16'"
        class="flex-1 flex flex-col min-w-0 h-screen bg-background transition-all duration-200">

        {{-- TopNavBar --}}
        <header class="top-0 h-header-height bg-surface dark:bg-inverse-surface border-b border-outline-variant dark:border-outline flex justify-between items-center px-gutter w-full z-10 shrink-0">
            <div class="flex items-center gap-stack-md">
                <button @@click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-full hover:bg-surface-container transition-colors text-on-surface-variant hover:text-primary"
                        title="Toggle sidebar">
                    <span class="material-symbols-outlined" x-text="sidebarOpen ? 'menu_open' : 'menu'"></span>
                </button>
                <span class="text-headline-sm font-bold text-primary dark:text-inverse-primary hidden sm:inline">Sistem informasi arsip pajak digital</span>
            </div>
            <div class="flex items-center gap-stack-lg">
                <div class="relative hidden md:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                    <form action="{{ route('arsips.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="pl-10 pr-4 py-2 border border-outline-variant rounded-full bg-surface-container-lowest text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-body-md w-64"
                               placeholder="Cari arsip...">
                    </form>
                </div>
                <div class="flex items-center gap-stack-sm text-on-surface-variant dark:text-surface-variant">
                    <div class="h-8 w-8 ml-stack-sm rounded-full bg-primary-container border border-outline-variant flex items-center justify-center text-on-primary font-bold text-label-md" title="{{ Auth::user()->name }}">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-container-padding">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
