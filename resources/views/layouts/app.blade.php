<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bapenda Riau') - Arsip Digital</title>
    @if (file_exists(public_path('build/manifest.json')))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        @endphp
        @if (isset($manifest['resources/css/app.css']['file']))
            <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">
        @endif
        @if (isset($manifest['resources/js/app.js']['file']))
            <script type="module" src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>
        @endif
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: window.innerWidth >= 768 }" 
      @resize.window="if (window.innerWidth < 768) sidebarOpen = false" 
      @if(session('success')) data-flash-success="{{ session('success') }}" @endif
      @if(session('error')) data-flash-error="{{ session('error') }}" @endif
      @if($errors->any()) data-flash-error="{{ implode(', ', $errors->all()) }}" @endif
      class="bg-background text-on-surface font-body-md text-body-md antialiased overflow-hidden flex h-dvh">

    {{-- Mobile Backdrop Overlay --}}
    <div x-show="sidebarOpen && window.innerWidth < 768" 
         x-transition.opacity
         @@click="sidebarOpen = false" 
         class="fixed inset-0 bg-black/50 z-30 md:hidden"
         style="display: none;"></div>

    {{-- SideNavBar --}}
    <nav
        :class="{
            'translate-x-0 w-sidebar-width': sidebarOpen,
            '-translate-x-full md:translate-x-0 md:w-16': !sidebarOpen
        }"
        class="fixed left-0 top-0 h-dvh bg-primary dark:bg-primary-container flex flex-col pt-3 pb-stack-lg z-40 transition-all duration-200 overflow-y-auto md:overflow-hidden shadow-xl md:shadow-none">
        {{-- Brand / Logo --}}
        <div class="px-2 mb-0 flex items-center justify-between" x-show="sidebarOpen">
            <img src="{{ asset('images/logo-bapenda.png') }}" alt="Logo" class="w-full max-w-[120px] h-auto object-contain mx-auto">
            <button @@click="sidebarOpen = false" class="md:hidden text-on-primary/70 hover:text-on-primary p-1 mr-1">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="px-1 mb-0 flex justify-center" x-show="!sidebarOpen" @@click="sidebarOpen = true">
            <img src="{{ asset('images/logo-bapenda.png') }}" alt="Logo" class="w-12 h-12 object-contain cursor-pointer">
        </div>

        {{-- Divider --}}
        <div class="mx-2 border-t border-on-primary/20 mb-0"></div>

        {{-- Navigation Links --}}
        <div class="flex-1 flex flex-col mt-2">
            @php
                $navItems = [
                    ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Beranda', 'match' => ['dashboard']],
                    ['route' => 'arsips.pilih_unit', 'icon' => 'inventory_2', 'label' => 'Daftar Arsip', 'match' => ['arsips.pilih_unit', 'arsips.index', 'arsips.create', 'arsips.edit', 'arsips.show']],
                    ['route' => 'peminjaman.index', 'icon' => 'book', 'label' => 'Peminjaman', 'match' => ['peminjaman*']],
                    ['route' => 'raks.index', 'icon' => 'shelves', 'label' => 'Kelola Rak', 'match' => ['raks*']],
                    ['route' => 'laporan', 'icon' => 'analytics', 'label' => 'Laporan', 'match' => ['laporan*']],
                    ['route' => 'pengaturan', 'icon' => 'settings', 'label' => 'Pengaturan', 'match' => ['pengaturan*']],
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
        :class="{
            'md:ml-sidebar-width ml-0': sidebarOpen,
            'md:ml-16 ml-0': !sidebarOpen
        }"
        class="flex-1 flex flex-col min-w-0 h-dvh bg-background transition-all duration-200">

        {{-- TopNavBar --}}
        <header class="top-0 h-header-height bg-surface dark:bg-inverse-surface border-b border-outline-variant dark:border-outline flex justify-between items-center px-3 sm:px-4 md:px-6 w-full z-10 shrink-0">
            <div class="flex items-center gap-2 sm:gap-stack-md">
                <button @@click="sidebarOpen = !sidebarOpen"
                        class="p-1.5 sm:p-2 rounded-full hover:bg-surface-container transition-colors text-on-surface-variant hover:text-primary"
                        title="Toggle sidebar">
                    <span class="material-symbols-outlined" x-text="sidebarOpen ? 'menu_open' : 'menu'"></span>
                </button>
                <span class="text-sm sm:text-base md:text-headline-sm font-bold text-primary dark:text-inverse-primary truncate max-w-[140px] xs:max-w-[180px] sm:max-w-none">Sistem Informasi Arsip Pajak Digital</span>
            </div>
            <div class="flex items-center gap-stack-lg shrink-0">

                <div class="flex items-center gap-stack-sm text-on-surface-variant dark:text-surface-variant">
                    <div class="h-8 w-8 ml-stack-sm rounded-full bg-primary-container border border-outline-variant flex items-center justify-center text-on-primary font-bold text-label-md" title="{{ Auth::user()->name }}">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6 w-full max-w-full">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
