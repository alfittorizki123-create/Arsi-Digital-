@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Pengaturan</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Kelola master data dan informasi sistem.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-stack-md px-4 py-3 rounded-lg bg-primary-fixed text-on-primary-fixed border border-primary-fixed-dim">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-stack-md px-4 py-3 rounded-lg bg-error-container text-on-error-container">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2 mb-stack-lg border-b border-outline-variant pb-0">
        <a href="{{ route('pengaturan', ['tab' => 'jenis-pajak']) }}"
           class="px-4 py-2 text-label-md font-label-md rounded-t border-b-2 transition-colors {{ $tab === 'jenis-pajak' ? 'border-primary text-primary bg-primary-fixed/30' : 'border-transparent text-on-surface-variant hover:text-primary' }}">
            Jenis Pajak
        </a>
        <a href="{{ route('pengaturan', ['tab' => 'unit']) }}"
           class="px-4 py-2 text-label-md font-label-md rounded-t border-b-2 transition-colors {{ $tab === 'unit' ? 'border-primary text-primary bg-primary-fixed/30' : 'border-transparent text-on-surface-variant hover:text-primary' }}">
            Unit / UPT
        </a>
        <a href="{{ route('pengaturan', ['tab' => 'sistem']) }}"
           class="px-4 py-2 text-label-md font-label-md rounded-t border-b-2 transition-colors {{ $tab === 'sistem' ? 'border-primary text-primary bg-primary-fixed/30' : 'border-transparent text-on-surface-variant hover:text-primary' }}">
            Sistem
        </a>
    </div>

    @if ($tab === 'jenis-pajak')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-stack-lg">
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm h-fit">
                <h3 class="text-headline-sm font-bold text-on-surface mb-stack-md">
                    {{ $editJenis ? 'Edit Jenis Pajak' : 'Tambah Jenis Pajak' }}
                </h3>
                <form method="POST" action="{{ $editJenis ? route('jenis-pajak.update', $editJenis) : route('jenis-pajak.store') }}">
                    @csrf
                    @if ($editJenis) @method('PUT') @endif
                    <div class="space-y-3">
                        <div>
                            <label class="block text-label-md text-on-surface-variant mb-1">Nama Jenis Pajak <span class="text-error">*</span></label>
                            <input type="text" name="nama_jenis_pajak" value="{{ old('nama_jenis_pajak', $editJenis->nama_jenis_pajak ?? '') }}"
                                   class="w-full px-3 py-2 border rounded bg-surface focus:border-primary @error('nama_jenis_pajak') border-error @else border-outline-variant @enderror" required>
                            @error('nama_jenis_pajak') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-label-md text-on-surface-variant mb-1">Kode <span class="text-error">*</span></label>
                            <input type="text" name="kode" value="{{ old('kode', $editJenis->kode ?? '') }}"
                                   class="w-full px-3 py-2 border rounded bg-surface focus:border-primary @error('kode') border-error @else border-outline-variant @enderror"
                                   placeholder="Contoh: PKB" required>
                            @error('kode') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex gap-2 pt-2">
                            @if ($editJenis)
                                <a href="{{ route('pengaturan', ['tab' => 'jenis-pajak']) }}" class="px-4 py-2 rounded border border-outline-variant text-on-surface-variant text-label-md">Batal</a>
                            @endif
                            <button type="submit" class="px-4 py-2 rounded bg-primary-container text-on-primary text-label-md hover:bg-primary-container/90">
                                {{ $editJenis ? 'Simpan Perubahan' : 'Tambah' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-surface border-b border-outline-variant">
                            <tr>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Kode</th>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Nama</th>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Dipakai</th>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant">
                            @forelse ($jenisPajaks as $jp)
                                <tr class="hover:bg-surface-container/50">
                                    <td class="py-3 px-4 font-medium text-on-surface">{{ $jp->kode }}</td>
                                    <td class="py-3 px-4 text-on-surface">{{ $jp->nama_jenis_pajak }}</td>
                                    <td class="py-3 px-4 text-on-surface-variant">{{ $jp->arsips_count }} arsip</td>
                                    <td class="py-3 px-4 text-right space-x-1">
                                        <a href="{{ route('pengaturan', ['tab' => 'jenis-pajak', 'edit_jenis' => $jp->id]) }}" class="inline-flex p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-fixed/30 rounded" title="Edit">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                        </a>
                                        <form action="{{ route('jenis-pajak.destroy', $jp) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jenis pajak {{ $jp->kode }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/50 rounded" title="Hapus">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-10 text-center text-on-surface-variant">Belum ada jenis pajak.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif ($tab === 'unit')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-stack-lg">
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm h-fit">
                <h3 class="text-headline-sm font-bold text-on-surface mb-stack-md">
                    {{ $editUnit ? 'Edit Unit/UPT' : 'Tambah Unit/UPT' }}
                </h3>
                <form method="POST" action="{{ $editUnit ? route('unit.update', $editUnit) : route('unit.store') }}">
                    @csrf
                    @if ($editUnit) @method('PUT') @endif
                    <div class="space-y-3">
                        <div>
                            <label class="block text-label-md text-on-surface-variant mb-1">Nama Unit <span class="text-error">*</span></label>
                            <input type="text" name="nama_unit" value="{{ old('nama_unit', $editUnit->nama_unit ?? '') }}"
                                   class="w-full px-3 py-2 border rounded bg-surface focus:border-primary @error('nama_unit') border-error @else border-outline-variant @enderror" required>
                            @error('nama_unit') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-label-md text-on-surface-variant mb-1">Kode Unit <span class="text-error">*</span></label>
                            <input type="text" name="kode_unit" value="{{ old('kode_unit', $editUnit->kode_unit ?? '') }}"
                                   class="w-full px-3 py-2 border rounded bg-surface focus:border-primary @error('kode_unit') border-error @else border-outline-variant @enderror"
                                   placeholder="Contoh: UPT-051" required>
                            @error('kode_unit') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex gap-2 pt-2">
                            @if ($editUnit)
                                <a href="{{ route('pengaturan', ['tab' => 'unit']) }}" class="px-4 py-2 rounded border border-outline-variant text-on-surface-variant text-label-md">Batal</a>
                            @endif
                            <button type="submit" class="px-4 py-2 rounded bg-primary-container text-on-primary text-label-md hover:bg-primary-container/90">
                                {{ $editUnit ? 'Simpan Perubahan' : 'Tambah' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
                <div class="overflow-x-auto max-h-[70vh]">
                    <table class="w-full text-left">
                        <thead class="bg-surface border-b border-outline-variant sticky top-0">
                            <tr>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Kode</th>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Nama Unit</th>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Dipakai</th>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant">
                            @forelse ($units as $unit)
                                <tr class="hover:bg-surface-container/50">
                                    <td class="py-3 px-4 font-medium text-on-surface">{{ $unit->kode_unit }}</td>
                                    <td class="py-3 px-4 text-on-surface">{{ $unit->nama_unit }}</td>
                                    <td class="py-3 px-4 text-on-surface-variant">{{ $unit->arsips_count }} arsip</td>
                                    <td class="py-3 px-4 text-right space-x-1">
                                        <a href="{{ route('pengaturan', ['tab' => 'unit', 'edit_unit' => $unit->id]) }}" class="inline-flex p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-fixed/30 rounded" title="Edit">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                        </a>
                                        <form action="{{ route('unit.destroy', $unit) }}" method="POST" class="inline" onsubmit="return confirm('Hapus unit {{ $unit->kode_unit }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/50 rounded" title="Hapus">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-10 text-center text-on-surface-variant">Belum ada unit.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-lg">
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm">
                <h3 class="text-headline-sm font-bold text-on-surface mb-stack-md">Informasi Aplikasi</h3>
                <dl class="space-y-3 text-body-md">
                    <div class="flex justify-between border-b border-outline-variant pb-2">
                        <dt class="text-on-surface-variant">Nama</dt>
                        <dd class="font-medium text-on-surface">Arsip Digital Bapenda Riau</dd>
                    </div>
                    <div class="flex justify-between border-b border-outline-variant pb-2">
                        <dt class="text-on-surface-variant">Framework</dt>
                        <dd class="font-medium text-on-surface">Laravel {{ app()->version() }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-outline-variant pb-2">
                        <dt class="text-on-surface-variant">Environment</dt>
                        <dd class="font-medium text-on-surface">{{ config('app.env') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-on-surface-variant">Mode</dt>
                        <dd class="font-medium text-on-surface">Localhost (Laragon)</dd>
                    </div>
                </dl>
            </div>
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm">
                <h3 class="text-headline-sm font-bold text-on-surface mb-stack-md">Master Data</h3>
                <dl class="space-y-3 text-body-md">
                    <div class="flex justify-between border-b border-outline-variant pb-2">
                        <dt class="text-on-surface-variant">Total Jenis Pajak</dt>
                        <dd class="font-medium text-on-surface">{{ $jenisPajaks->count() }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-outline-variant pb-2">
                        <dt class="text-on-surface-variant">Total Unit/UPT</dt>
                        <dd class="font-medium text-on-surface">{{ $units->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-on-surface-variant">Penyimpanan file</dt>
                        <dd class="font-medium text-on-surface">storage/app/public/arsip</dd>
                    </div>
                </dl>
                <p class="mt-stack-md text-label-md text-on-surface-variant">
                    Login multi-user belum diaktifkan (sesuai scope: 1 admin internal). Fitur auth dapat ditambahkan saat revisi berikutnya.
                </p>
            </div>
        </div>
    @endif
@endsection
