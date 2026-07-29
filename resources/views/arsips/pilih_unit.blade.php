@extends('layouts.app')

@section('title', 'Daftar Arsip per UP/UPT')

@section('content')
@php
    $unitsData = $units->map(fn($u) => [
        'id' => $u->id,
        'searchStr' => mb_strtolower(trim($u->nama_unit . ' ' . ($u->kode_unit ?? '') . ' ' . ($u->nomor_rak ?? '')))
    ])->values()->toJson();
@endphp

<div x-data="{ 
    search: '',
    currentPage: 1,
    perPage: 7,
    unitsList: {{ $unitsData }},
    get filteredUnits() {
        if (!this.search || !this.search.trim()) return this.unitsList;
        const terms = this.search.toLowerCase().trim().split(/\s+/);
        return this.unitsList.filter(item => terms.every(t => item.searchStr.includes(t)));
    },
    get totalPages() {
        return Math.max(1, Math.ceil(this.filteredUnits.length / this.perPage));
    },
    get paginatedUnitIds() {
        if (this.currentPage > this.totalPages) this.currentPage = this.totalPages;
        const start = (this.currentPage - 1) * this.perPage;
        return this.filteredUnits.slice(start, start + this.perPage).map(item => item.id);
    },
    matchUnit(id) {
        return this.paginatedUnitIds.includes(id);
    },
    rowNumber(id) {
        return ((this.currentPage - 1) * this.perPage) + this.paginatedUnitIds.indexOf(id) + 1;
    },
    goToPage(page) {
        this.currentPage = Math.min(Math.max(page, 1), this.totalPages);
    },
    get hasMatches() {
        return this.filteredUnits.length > 0;
    }
}" x-effect="search; currentPage = 1">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-md">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Pilih Kantor UP/UPT</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Langkah 1: pilih nama kantor terlebih dahulu. Setelah itu sistem akan membuka daftar arsip kantor tersebut.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('arsips.import') }}" 
               style="background-color: #059669; color: #ffffff;"
               class="inline-flex items-center justify-center gap-1.5 h-[42px] px-4 rounded-lg font-bold text-xs hover:opacity-90 transition-all shadow-md shrink-0">
                <span class="material-symbols-outlined text-base">upload_file</span>
                <span>Import Data dari Excel</span>
            </a>
            <a href="{{ route('pengaturan', ['tab' => 'unit']) }}" 
               class="inline-flex items-center justify-center gap-1.5 h-[42px] px-4 rounded-lg bg-primary text-on-primary font-bold text-xs hover:bg-primary/90 transition-all shadow-md shrink-0">
                <span class="material-symbols-outlined text-base">add</span>
                <span>Tambah Kantor UP/UPT</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-stack-md">
        <div class="rounded-xl border border-primary/30 bg-primary-fixed/20 p-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold shrink-0">1</div>
                <div>
                    <p class="font-bold text-on-surface text-sm">Cari nama UP/UPT</p>
                    <p class="text-xs text-on-surface-variant mt-1">Gunakan kotak pencarian jika daftar UP/UPT banyak.</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-surface-container text-on-surface flex items-center justify-center font-bold shrink-0">2</div>
                <div>
                    <p class="font-bold text-on-surface text-sm">Klik “Buka Arsip”</p>
                    <p class="text-xs text-on-surface-variant mt-1">Tombol berada di sisi kanan setiap nama kantor.</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-surface-container text-on-surface flex items-center justify-center font-bold shrink-0">3</div>
                <div>
                    <p class="font-bold text-on-surface text-sm">Kelola data arsip</p>
                    <p class="text-xs text-on-surface-variant mt-1">Tambah, lihat, upload file, atau export laporan.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Compact Search Bar --}}
    <div class="bg-surface-container-lowest py-3 px-4 rounded-lg border border-outline-variant mb-stack-md shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-on-surface font-bold text-sm">
            <span class="material-symbols-outlined text-primary text-lg">domain</span>
            <span>Daftar Kantor UP/UPT</span>
        </div>
        <div class="relative w-full sm:w-72 shrink-0">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">search</span>
            <input type="text" x-model="search" placeholder="Ketik nama kantor..." 
                   class="w-full pl-9 pr-3 py-1.5 border border-outline-variant rounded-md bg-surface focus:outline-none focus:border-primary text-xs shadow-xs">
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant w-16">No</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Nama Kantor UP/UPT</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Total Arsip</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($units as $unit)
                        <tr class="hover:bg-surface-container/50 transition-colors" 
                            x-show="matchUnit({{ $unit->id }})">
                            <td class="py-3 px-4 font-medium text-on-surface" x-text="rowNumber({{ $unit->id }})"></td>
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
                                <form method="POST" action="{{ route('arsips.pilih_unit.store') }}" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary text-on-primary font-bold text-xs hover:bg-primary/90 transition-colors shadow-sm"
                                            title="Buka & Kelola Arsip {{ $unit->nama_unit }}">
                                        <span>Buka Arsip</span>
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-on-surface-variant bg-surface-container/10">
                                <div class="max-w-md mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-3">
                                        <span class="material-symbols-outlined text-2xl">domain_disabled</span>
                                    </div>
                                    <h4 class="font-bold text-sm text-on-surface mb-1">Belum Ada Data UP/UPT</h4>
                                    <p class="text-xs text-on-surface-variant">Silakan klik tombol <b>Import File Excel</b> atau <b>Tambah UP/UPT</b> di atas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    {{-- Notifikasi Hasil Pencarian Tidak Ditemukan --}}
                    <tr x-show="search.trim() !== '' && !hasMatches" style="display: none;">
                        <td colspan="4" class="py-12 text-center text-on-surface-variant bg-surface-container/20">
                            <span class="material-symbols-outlined text-4xl mb-2 text-outline">search_off</span>
                            <p class="text-body-md font-bold text-on-surface">UP/UPT tidak ditemukan</p>
                            <p class="text-xs text-on-surface-variant mt-1">Tidak ada UP/UPT yang cocok dengan kata kunci "<span x-text="search" class="font-bold text-primary"></span>"</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-3 border-t border-outline-variant bg-surface-container-lowest"
             x-show="hasMatches">
            <div class="text-xs text-on-surface-variant">
                Menampilkan
                <span class="font-bold text-on-surface" x-text="((currentPage - 1) * perPage) + 1"></span>
                -
                <span class="font-bold text-on-surface" x-text="Math.min(currentPage * perPage, filteredUnits.length)"></span>
                dari
                <span class="font-bold text-on-surface" x-text="filteredUnits.length"></span>
                UP/UPT
            </div>

            <div class="flex items-center gap-1">
                <button type="button"
                        class="px-3 py-1.5 rounded-md border border-outline-variant text-xs font-semibold hover:bg-surface-container disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="currentPage === 1"
                        @click="goToPage(currentPage - 1)">
                    Sebelumnya
                </button>

                <template x-for="page in totalPages" :key="page">
                    <button type="button"
                            class="min-w-8 px-3 py-1.5 rounded-md border text-xs font-bold transition-colors"
                            :class="page === currentPage ? 'bg-primary text-on-primary border-primary' : 'border-outline-variant text-on-surface hover:bg-surface-container'"
                            @click="goToPage(page)"
                            x-text="page">
                    </button>
                </template>

                <button type="button"
                        class="px-3 py-1.5 rounded-md border border-outline-variant text-xs font-semibold hover:bg-surface-container disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="currentPage === totalPages"
                        @click="goToPage(currentPage + 1)">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
