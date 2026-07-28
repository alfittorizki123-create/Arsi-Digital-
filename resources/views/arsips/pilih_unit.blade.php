@extends('layouts.app')

@section('title', 'Daftar Arsip per Unit')

@section('content')
@php
    $unitsData = $units->map(fn($u) => [
        'id' => $u->id,
        'searchStr' => mb_strtolower(trim($u->nama_unit . ' ' . ($u->kode_unit ?? '') . ' ' . ($u->nomor_rak ?? '')))
    ])->values()->toJson();
@endphp

<div x-data="{ 
    search: '',
    unitsList: {{ $unitsData }},
    matchUnit(id) {
        if (!this.search || !this.search.trim()) return true;
        const terms = this.search.toLowerCase().trim().split(/\s+/);
        const item = this.unitsList.find(u => u.id === id);
        if (!item) return false;
        return terms.every(t => item.searchStr.includes(t));
    },
    get hasMatches() {
        if (!this.search || !this.search.trim()) return true;
        const terms = this.search.toLowerCase().trim().split(/\s+/);
        return this.unitsList.some(item => terms.every(t => item.searchStr.includes(t)));
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-md">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Daftar Arsip per Unit</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Pilih unit (UPT/UP) untuk melihat daftar arsipnya, mengunggah arsip baru, atau mengimpor dari Excel.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('arsips.import') }}" 
               style="background-color: #059669; color: #ffffff;"
               class="inline-flex items-center justify-center gap-1.5 h-[42px] px-4 rounded-lg font-bold text-xs hover:opacity-90 transition-all shadow-md shrink-0">
                <span class="material-symbols-outlined text-base">upload_file</span>
                <span>Import File Excel</span>
            </a>
            <a href="{{ route('pengaturan', ['tab' => 'unit']) }}" 
               class="inline-flex items-center justify-center gap-1.5 h-[42px] px-4 rounded-lg bg-primary text-on-primary font-bold text-xs hover:bg-primary/90 transition-all shadow-md shrink-0">
                <span class="material-symbols-outlined text-base">add</span>
                <span>Tambah Unit</span>
            </a>
        </div>
    </div>

    {{-- Compact Search Bar --}}
    <div class="bg-surface-container-lowest py-3 px-4 rounded-lg border border-outline-variant mb-stack-md shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-on-surface font-bold text-sm">
            <span class="material-symbols-outlined text-primary text-lg">domain</span>
            <span>Daftar Unit Kerja (UPT / UP)</span>
        </div>
        <div class="relative w-full sm:w-72 shrink-0">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">search</span>
            <input type="text" x-model="search" placeholder="Cari nama unit..." 
                   class="w-full pl-9 pr-3 py-1.5 border border-outline-variant rounded-md bg-surface focus:outline-none focus:border-primary text-xs shadow-xs">
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant w-16">No</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Nama Unit</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Total Arsip</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($units as $unit)
                        <tr class="hover:bg-surface-container/50 transition-colors" 
                            x-show="matchUnit({{ $unit->id }})">
                            <td class="py-3 px-4 font-medium text-on-surface">{{ $loop->iteration }}</td>
                            <td class="py-3 px-4 text-on-surface">
                                <div class="font-semibold text-on-surface">{{ $unit->nama_unit }}</div>
                                @if($unit->boks_display)
                                    <div class="inline-flex items-center gap-1 text-[11px] font-medium text-primary mt-0.5">
                                        <span class="material-symbols-outlined text-xs">inventory_2</span>
                                        <span>{{ $unit->boks_display }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-on-surface-variant">
                                <span class="px-2.5 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-semibold">
                                    {{ $unit->arsips_count }} berkas
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('arsips.index', ['unit_id' => $unit->id]) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary text-on-primary font-bold text-xs hover:bg-primary/90 transition-colors shadow-sm"
                                   title="Buka & Kelola Arsip {{ $unit->nama_unit }}">
                                    <span>Buka Arsip Unit</span>
                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-on-surface-variant bg-surface-container/10">
                                <div class="max-w-md mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-3">
                                        <span class="material-symbols-outlined text-2xl">domain_disabled</span>
                                    </div>
                                    <h4 class="font-bold text-sm text-on-surface mb-1">Belum Ada Data Unit</h4>
                                    <p class="text-xs text-on-surface-variant">Silakan klik tombol <b>Import File Excel</b> atau <b>Tambah Unit</b> di atas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    {{-- Notifikasi Hasil Pencarian Tidak Ditemukan --}}
                    <tr x-show="search.trim() !== '' && !hasMatches" style="display: none;">
                        <td colspan="4" class="py-12 text-center text-on-surface-variant bg-surface-container/20">
                            <span class="material-symbols-outlined text-4xl mb-2 text-outline">search_off</span>
                            <p class="text-body-md font-bold text-on-surface">Unit tidak ditemukan</p>
                            <p class="text-xs text-on-surface-variant mt-1">Tidak ada unit kerja yang cocok dengan kata kunci "<span x-text="search" class="font-bold text-primary"></span>"</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
