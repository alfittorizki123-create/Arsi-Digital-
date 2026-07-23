@extends('layouts.app')

@section('title', 'Import Excel')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Import Excel</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Unggah file Excel arsip (format Bapenda), lalu pratinjau sebelum disimpan.</p>
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
                <div class="mb-stack-md">
                    <label for="unit_id" class="block text-label-md font-label-md text-on-surface-variant mb-1">Unit/UPT/UP <span class="text-error">*</span></label>
                    <p class="text-label-md text-on-surface-variant mb-2">Pilih unit pemilik arsip. Semua baris Excel akan dikaitkan ke unit ini. (Kosongkan jika beda-beda per baris.)</p>
                    <select name="unit_id" id="unit_id"
                            class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('unit_id') border-error @else border-outline-variant @enderror">
                        <option value="">— Pilih Unit —</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->nama_unit }} ({{ $unit->kode_unit }})</option>
                        @endforeach
                    </select>
                    @error('unit_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>

                <label for="file" class="block text-label-md font-label-md text-on-surface-variant mb-1">
                    File Excel <span class="text-error">*</span>
                </label>
                <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv"
                       class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary-fixed file:text-on-primary-fixed file:text-label-md file:font-semibold @error('file') border-error @else border-outline-variant @enderror"
                       required>
                <p class="mt-1 text-label-md text-on-surface-variant">Format: .xlsx, .xls, .csv · Maksimal 10 MB · Kolom: NO, KODE KLASIFIKASI, NO ARSIP/BERKAS, URAIAN INFORMASI ARSIP, KURUN WAKTU, JUMLAH, satuan, TINGKAT PERKEMBANGAN, NO. BOKS, KONDISI ARSIP, KLASIFIKASI KEAMANAN</p>
                @error('file') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror

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
            <h3 class="text-headline-sm font-bold text-on-surface mb-stack-md">Format Kolom Excel</h3>
            <ol class="space-y-1 text-body-md text-on-surface list-decimal pl-4">
                <li><span class="font-medium">NO</span> — nomor urut (diabaikan)</li>
                <li><span class="font-medium">KODE KLASIFIKASI</span> — contoh: 900.1.13.1</li>
                <li><span class="font-medium">NO ARSIP/BERKAS</span> — opsional</li>
                <li><span class="font-medium">URAIAN INFORMASI ARSIP</span> — deskripsi</li>
                <li><span class="font-medium">KURUN WAKTU</span> — contoh: 2023</li>
                <li><span class="font-medium">JUMLAH</span> — integer</li>
                <li>(Satuan) — default "Berkas"</li>
                <li><span class="font-medium">TINGKAT PERKEMBANGAN</span> — Asli/Copy/Asli-Copy</li>
                <li><span class="font-medium">NO. BOKS</span> — nomor boks</li>
                <li><span class="font-medium">KONDISI ARSIP</span> — Baik/Rusak</li>
                <li><span class="font-medium">KLASIFIKASI KEAMANAN</span> — Terbuka/Terbatas/Rahasia</li>
            </ol>
            <p class="text-label-md text-on-surface-variant mt-4">Tipe arsip (rekap/detail) akan dideteksi otomatis: jumlah > 1 + uraian mengandung "Boks" = Rekap.</p>
        </div>
    </div>
@endsection