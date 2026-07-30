@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Pengaturan</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Kelola master data dan informasi sistem.</p>
        </div>
    </div>


    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2 mb-stack-lg border-b border-outline-variant pb-0">
        <a href="{{ route('pengaturan', ['tab' => 'jenis-pajak']) }}"
           class="px-4 py-2 text-label-md font-label-md rounded-t border-b-2 transition-colors {{ $tab === 'jenis-pajak' ? 'border-primary text-primary bg-primary-fixed/30' : 'border-transparent text-on-surface-variant hover:text-primary' }}">
            Jenis Pajak
        </a>
        <a href="{{ route('pengaturan', ['tab' => 'unit']) }}"
           class="px-4 py-2 text-label-md font-label-md rounded-t border-b-2 transition-colors {{ $tab === 'unit' ? 'border-primary text-primary bg-primary-fixed/30' : 'border-transparent text-on-surface-variant hover:text-primary' }}">
            UP/UPT
        </a>
        <a href="{{ route('pengaturan', ['tab' => 'sistem']) }}"
           class="px-4 py-2 text-label-md font-label-md rounded-t border-b-2 transition-colors {{ $tab === 'sistem' ? 'border-primary text-primary bg-primary-fixed/30' : 'border-transparent text-on-surface-variant hover:text-primary' }}">
            Sistem
        </a>
    </div>

    @if ($tab === 'jenis-pajak')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-stack-lg" x-data="{ searchJenis: '', editJenisModal: false, activeJenis: null }">
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm h-fit">
                <h3 class="text-headline-sm font-bold text-on-surface mb-stack-md">
                    Tambah Jenis Pajak
                </h3>
                <form method="POST" action="{{ route('jenis-pajak.store') }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-label-md text-on-surface-variant mb-1">Nama Jenis Pajak <span class="text-error">*</span></label>
                            <input type="text" name="nama_jenis_pajak" value="{{ old('nama_jenis_pajak') }}"
                                   class="w-full px-3 py-2 border rounded bg-surface focus:border-primary @error('nama_jenis_pajak') border-error @else border-outline-variant @enderror" required>
                            @error('nama_jenis_pajak') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-label-md text-on-surface-variant mb-1">Kode <span class="text-error">*</span></label>
                            <input type="text" name="kode" value="{{ old('kode') }}"
                                   class="w-full px-3 py-2 border rounded bg-surface focus:border-primary @error('kode') border-error @else border-outline-variant @enderror"
                                   placeholder="Contoh: PKB" required>
                            @error('kode') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="px-4 py-2 rounded bg-primary-container text-on-primary text-label-md hover:bg-primary-container/90">
                                Tambah
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm flex flex-col">
                <div class="p-3 border-b border-outline-variant bg-surface flex items-center gap-2">
                    <div class="relative w-full">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70 text-base">search</span>
                        <input type="text" x-model="searchJenis" placeholder="Cari jenis pajak atau kode (misal: PKB, Air Permukaan)..."
                               class="w-full pl-9 pr-8 py-2 border border-outline-variant rounded-lg bg-surface text-xs focus:outline-none focus:border-primary text-on-surface">
                        <button type="button" x-show="searchJenis.length > 0" @@click="searchJenis = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
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
                                <tr class="hover:bg-surface-container/50 transition-colors"
                                    x-show="!searchJenis || '{{ strtolower($jp->nama_jenis_pajak . ' ' . $jp->kode) }}'.includes(searchJenis.toLowerCase())">
                                    <td class="py-3 px-4 font-medium text-on-surface">{{ $jp->kode }}</td>
                                    <td class="py-3 px-4 text-on-surface">{{ $jp->nama_jenis_pajak }}</td>
                                    <td class="py-3 px-4 text-on-surface-variant">{{ $jp->arsips_count }} arsip</td>
                                    <td class="py-3 px-4 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-1.5">
                                        <button type="button" @@click="activeJenis = { id: {{ $jp->id }}, nama_jenis_pajak: {{ json_encode($jp->nama_jenis_pajak) }}, kode: {{ json_encode($jp->kode) }} }; editJenisModal = true" class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-bold bg-primary-container text-on-primary hover:bg-primary-container/90 transition-colors shadow-sm" title="Edit">
                                            <span class="material-symbols-outlined" style="font-size: 14px;">edit</span> Edit
                                        </button>
                                        <form action="{{ route('jenis-pajak.destroy', $jp) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus jenis pajak {{ $jp->kode }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-bold bg-error-container text-on-error-container hover:bg-error-container/80 transition-colors shadow-sm" title="Hapus">
                                                <span class="material-symbols-outlined" style="font-size: 14px;">delete</span> Hapus
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

            {{-- MODAL EDIT JENIS PAJAK --}}
            <div x-show="editJenisModal" class="fixed inset-0 z-50 flex items-center justify-center bg-scrim/40 p-4" x-cloak>
                <div class="bg-surface rounded-xl border border-outline-variant shadow-lg max-w-md w-full p-stack-lg" @@click.outside="editJenisModal = false">
                    <h3 class="font-display-md text-title-md mb-4 text-on-surface">Edit Jenis Pajak</h3>
                    <form x-bind:action="'{{ url('pengaturan/jenis-pajak') }}/' + (activeJenis ? activeJenis.id : '')" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-label-md font-bold mb-1">Nama Jenis Pajak <span class="text-error">*</span></label>
                            <input type="text" name="nama_jenis_pajak" x-bind:value="activeJenis ? activeJenis.nama_jenis_pajak : ''" required
                                   class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary">
                        </div>
                        <div class="mb-6">
                            <label class="block text-label-md font-bold mb-1">Kode <span class="text-error">*</span></label>
                            <input type="text" name="kode" x-bind:value="activeJenis ? activeJenis.kode : ''" required
                                   class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @@click="editJenisModal = false" class="px-4 py-2 border rounded text-on-surface-variant">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded font-bold">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif ($tab === 'unit')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-stack-lg" x-data="{ searchUnit: '', editUnitModal: false, activeUnit: null }">
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm h-fit">
                <h3 class="text-headline-sm font-bold text-on-surface mb-stack-md">
                    Tambah UP/UPT
                </h3>
                <form method="POST" action="{{ route('unit.store') }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-label-md text-on-surface-variant mb-1">Nama UP/UPT <span class="text-error">*</span></label>
                            <input type="text" name="nama_unit" value="{{ old('nama_unit') }}"
                                   class="w-full px-3 py-2 border rounded bg-surface focus:border-primary @error('nama_unit') border-error @else border-outline-variant @enderror" required>
                            @error('nama_unit') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-label-md text-on-surface-variant mb-1">Kode UP/UPT <span class="text-error">*</span></label>
                            <input type="text" name="kode_unit" value="{{ old('kode_unit') }}"
                                   class="w-full px-3 py-2 border rounded bg-surface focus:border-primary @error('kode_unit') border-error @else border-outline-variant @enderror"
                                   placeholder="Contoh: UPT-051" required>
                            @error('kode_unit') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="px-4 py-2 rounded bg-primary-container text-on-primary text-label-md hover:bg-primary-container/90">
                                Tambah
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm flex flex-col">
                {{-- Search Box di Atas Tabel Nama UP/UPT --}}
                <div class="p-3 border-b border-outline-variant bg-surface flex items-center gap-2">
                    <div class="relative w-full">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70 text-base">search</span>
                        <input type="text" x-model="searchUnit" placeholder="Cari nama UP/UPT atau kode UP/UPT (misal: Pekanbaru, UPT-036)..."
                               class="w-full pl-9 pr-8 py-2 border border-outline-variant rounded-lg bg-surface text-xs focus:outline-none focus:border-primary text-on-surface">
                        <button type="button" x-show="searchUnit.length > 0" @@click="searchUnit = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[70vh]">
                    <table class="w-full text-left">
                        <thead class="bg-surface border-b border-outline-variant sticky top-0">
                            <tr>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Kode</th>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Nama UP/UPT</th>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Dipakai</th>
                                <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant">
                            @forelse ($units as $unit)
                                <tr class="hover:bg-surface-container/50 transition-colors"
                                    x-show="!searchUnit || '{{ strtolower($unit->nama_unit . ' ' . $unit->kode_unit . ' ' . $unit->nomor_rak) }}'.includes(searchUnit.toLowerCase())">
                                    <td class="py-3 px-4 font-medium text-on-surface">{{ $unit->kode_unit }}</td>
                                    <td class="py-3 px-4 text-on-surface font-medium">{{ $unit->nama_unit }}</td>
                                    <td class="py-3 px-4 text-on-surface-variant">{{ $unit->arsips_count }} arsip</td>
                                    <td class="py-3 px-4 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-1.5">
                                        <button type="button" @@click="activeUnit = { id: {{ $unit->id }}, nama_unit: {{ json_encode($unit->nama_unit) }}, kode_unit: {{ json_encode($unit->kode_unit) }} }; editUnitModal = true" class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-bold bg-primary-container text-on-primary hover:bg-primary-container/90 transition-colors shadow-sm" title="Edit">
                                            <span class="material-symbols-outlined" style="font-size: 14px;">edit</span> Edit
                                        </button>
                                        <form action="{{ route('unit.destroy', $unit) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus unit {{ $unit->kode_unit }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-bold bg-error-container text-on-error-container hover:bg-error-container/80 transition-colors shadow-sm" title="Hapus">
                                                <span class="material-symbols-outlined" style="font-size: 14px;">delete</span> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-10 text-center text-on-surface-variant">Belum ada UP/UPT.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MODAL EDIT UP/UPT --}}
            <div x-show="editUnitModal" class="fixed inset-0 z-50 flex items-center justify-center bg-scrim/40 p-4" x-cloak>
                <div class="bg-surface rounded-xl border border-outline-variant shadow-lg max-w-md w-full p-stack-lg" @@click.outside="editUnitModal = false">
                    <h3 class="font-display-md text-title-md mb-4 text-on-surface">Edit UP/UPT</h3>
                    <form x-bind:action="'{{ url('pengaturan/unit') }}/' + (activeUnit ? activeUnit.id : '')" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-label-md font-bold mb-1">Nama UP/UPT <span class="text-error">*</span></label>
                            <input type="text" name="nama_unit" x-bind:value="activeUnit ? activeUnit.nama_unit : ''" required
                                   class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary">
                        </div>
                        <div class="mb-6">
                            <label class="block text-label-md font-bold mb-1">Kode UP/UPT <span class="text-error">*</span></label>
                            <input type="text" name="kode_unit" x-bind:value="activeUnit ? activeUnit.kode_unit : ''" required
                                   class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @@click="editUnitModal = false" class="px-4 py-2 border rounded text-on-surface-variant">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded font-bold">Update</button>
                        </div>
                    </form>
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
                        <dt class="text-on-surface-variant">Total UP/UPT</dt>
                        <dd class="font-medium text-on-surface">{{ $units->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-on-surface-variant">Penyimpanan file</dt>
                        <dd class="font-medium text-on-surface">storage/app/public/arsip</dd>
                    </div>
                </dl>
                <p class="mt-stack-md text-label-md text-on-surface-variant">
                    Sistem login admin internal sudah aktif.
                </p>
            </div>
        </div>
    @endif
@endsection
