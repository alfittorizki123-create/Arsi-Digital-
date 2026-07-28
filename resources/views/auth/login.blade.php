<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</head>
<body class="bg-surface-dim min-h-screen flex items-start justify-center pt-12 antialiased">

    <div class="w-full max-w-md mx-4">
        {{-- Logo --}}
        <div class="text-center mb-stack-lg">
            <div class="w-36 h-36 mx-auto flex items-center justify-center mb-0">
                <img src="{{ asset('images/logo-bapenda.png') }}" alt="Logo Bapenda Riau" class="w-full h-full object-contain">
            </div>
            <p class="text-body-lg font-bold text-on-surface mt-1">SISTEM INFORMASI ARSIP DIGITAL PAJAK</p>
        </div>

        {{-- Card --}}
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-stack-lg shadow-sm">
            <h2 class="text-headline-sm font-bold text-on-surface mb-stack-lg text-center">Login Admin</h2>

            @if ($errors->any())
                <div class="mb-stack-md px-4 py-3 rounded-lg bg-error-container text-on-error-container">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-stack-md">
                @csrf
                <div>
                    <label for="username" class="block text-label-md font-label-md text-on-surface-variant mb-1">Username</label>
                    <input type="text" name="username" id="username"
                           value="{{ old('username') }}"
                           class="w-full px-3 py-2.5 border rounded-lg bg-surface focus:outline-none focus:border-primary text-body-md @error('username') border-error @else border-outline-variant @enderror"
                           placeholder="Masukkan username" required autofocus>
                </div>
                <div x-data="{ show: false }">
                    <label for="password" class="block text-label-md font-label-md text-on-surface-variant mb-1">Kata Sandi</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" id="password"
                               class="w-full px-3 py-2.5 pr-10 border rounded-lg bg-surface focus:outline-none focus:border-primary text-body-md @error('password') border-error @else border-outline-variant @enderror"
                               placeholder="••••••••" required>
                        <button type="button" @@click="show = !show"
                                class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-on-surface-variant hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-lg" x-text="show ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-body-md text-on-surface-variant">
                        <input type="checkbox" name="remember" class="rounded border-outline-variant text-primary focus:ring-primary">
                        Ingat saya
                    </label>
                </div>
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary-container text-on-primary text-label-md font-label-md hover:bg-primary-container/90 transition-colors shadow-sm">
                    <span class="material-symbols-outlined" style="font-size: 18px;">login</span>
                    Masuk
                </button>
            </form>

            <p class="mt-stack-md text-center text-label-md text-on-surface-variant">
                Login khusus admin internal arsip Bapenda
            </p>
        </div>
    </div>
</body>
</html>
