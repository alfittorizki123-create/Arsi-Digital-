<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bapenda Riau') - Arsip Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-background text-on-surface font-body-md text-body-md antialiased overflow-hidden flex h-screen">

    {{-- SideNavBar --}}
    <nav class="fixed left-0 top-0 h-screen w-sidebar-width bg-primary dark:bg-primary-container flex flex-col h-full py-stack-lg z-20">
        {{-- Brand Header --}}
        <div class="px-gutter mb-stack-lg flex items-center gap-stack-sm">
            <div class="w-10 h-10 bg-on-primary rounded flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">shield</span>
            </div>
            <div>
                <h1 class="text-headline-sm font-bold text-on-primary dark:text-on-primary-container">Bapenda Riau</h1>
                <p class="text-label-md text-on-primary/80">Arsip Digital Pajak</p>
            </div>
        </div>

        {{-- Navigation Links --}}
        <div class="flex-1 flex flex-col mt-stack-md">
            @php
                $navItems = [
                    ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Beranda', 'match' => 'dashboard'],
                    ['route' => 'arsips.index', 'icon' => 'inventory_2', 'label' => 'Daftar Arsip', 'match' => 'arsips.*'],
                    ['route' => 'laporan', 'icon' => 'analytics', 'label' => 'Laporan', 'match' => 'laporan'],
                    ['route' => 'pengaturan', 'icon' => 'settings', 'label' => 'Pengaturan', 'match' => 'pengaturan'],
                ];
            @endphp
            @foreach ($navItems as $item)
                @php
                    $isActive = request()->routeIs($item['match']);
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-stack-md px-gutter py-stack-md transition-all @if($isActive) bg-primary-container/20 dark:bg-primary/20 border-l-4 border-on-primary text-on-primary font-bold opacity-90 @else text-on-primary/70 dark:text-on-primary-container/70 hover:bg-primary-container/10 dark:hover:bg-primary/10 @endif">
                    <span class="material-symbols-outlined" @if($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Footer Actions --}}
        <div class="mt-auto">
            <a href="#" class="flex items-center gap-stack-md px-gutter py-stack-md text-on-primary/70 dark:text-on-primary-container/70 hover:bg-primary-container/10 dark:hover:bg-primary/10 transition-colors">
                <span class="material-symbols-outlined">logout</span>
                <span>Keluar</span>
            </a>
        </div>
    </nav>

    {{-- Main Content Wrapper --}}
    <div class="flex-1 flex flex-col min-w-0 ml-sidebar-width h-screen bg-background">
        {{-- TopNavBar --}}
        <header class="top-0 h-header-height bg-surface dark:bg-inverse-surface border-b border-outline-variant dark:border-outline flex justify-between items-center px-gutter w-full z-10 shrink-0">
            <div class="flex items-center gap-stack-md">
                <span class="text-headline-sm font-bold text-primary dark:text-inverse-primary">Sistem Arsip Digital Pajak Daerah</span>
            </div>
            <div class="flex items-center gap-stack-lg">
                {{-- Search (global) --}}
                <div class="relative hidden md:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                    <form action="{{ route('arsips.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="pl-10 pr-4 py-2 border border-outline-variant rounded-full bg-surface-container-lowest text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-body-md w-64"
                               placeholder="Cari arsip...">
                    </form>
                </div>
                {{-- Actions --}}
                <div class="flex items-center gap-stack-sm text-on-surface-variant dark:text-surface-variant">
                    <button class="p-2 rounded-full hover:text-primary dark:hover:text-inverse-primary hover:bg-surface-container transition-colors" title="Notifikasi">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <button class="p-2 rounded-full hover:text-primary dark:hover:text-inverse-primary hover:bg-surface-container transition-colors" title="Bantuan">
                        <span class="material-symbols-outlined">help</span>
                    </button>
                    <div class="h-8 w-8 ml-stack-sm rounded-full bg-primary-container border border-outline-variant flex items-center justify-center text-on-primary font-bold">
                        AD
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content Canvas --}}
        <main class="flex-1 overflow-y-auto p-container-padding">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>