@extends('layouts.app')

@section('title', 'Import Excel')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Import Excel</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Unggah file Excel arsip, lalu pratinjau sebelum disimpan.</p>
        </div>
        <a href="{{ route('arsips.index') }}" class="flex items-center gap-2 px-4 py-2 rounded border border-outline-variant text-on-surface-variant font-label-md text-label-md hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
            Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="mb-stack-md px-4 py-3 rounded-lg bg-primary-fixed text-on-primary-fixed border border-primary-fixed-dim">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-stack-md px-4 py-3 rounded-lg bg-error-container text-on-error-container border border-error/30">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-stack-lg">
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm">
            <form action="{{ route('arsips.import.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="file" class="block text-label-md font-label-md text-on-surface-variant mb-1">
                    File Excel <span class="text-error">*</span>
                </label>
                <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv"
                       class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary-fixed file:text-on-primary-fixed file:text-label-md file:font-semibold @error('file') border-error @else border-outline-variant @enderror"
                       required>
                <p class="mt-1 text-label-md text-on-surface-variant">Format: .xlsx, .xls, .csv · Maksimal 10 MB</p>
                @error('file')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-end gap-stack-sm mt-stack-lg pt-stack-md border-t border-outline-variant">
                    <a href="{{ route('arsips.index') }}" class="px-4 py-2 rounded border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container transition-colors">Batal</a>
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary-container/90 transition-colors shadow-sm">
                        <span class="material-symbols-outlined" style="font-size: 18px;">preview</span>
                        Pratinjau Data
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm">
            <h3 class="text-headline-sm font-bold text-on-surface mb-stack-md">Format Kolom</h3>
            <p class="text-body-md text-on-surface-variant mb-3">Baris pertama harus berisi header. Kolom yang dikenali:</p>
            <ul class="space-y-2 text-body-md text-on-surface">
                <li><span class="font-medium">nomor_arsip</span> — wajib, unik</li>
                <li><span class="font-medium">jenis_pajak</span> — kode atau nama (contoh: PKB)</li>
                <li><span class="font-medium">nama_wajib_pajak</span> — wajib</li>
                <li><span class="font-medium">tahun_arsip</span> — contoh: 2024</li>
                <li><span class="font-medium">nomor_rak</span> — opsional</li>
                <li><span class="font-medium">unit</span> — kode atau nama unit (contoh: UPT-006)</li>
                <li><span class="font-medium">status</span> — aktif / inaktif</li>
            </ul>
            <p class="text-label-md text-on-surface-variant mt-4">Jenis pajak & unit harus sudah ada di master data sistem.</p>
        </div>
    </div>
@endsection
