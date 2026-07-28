@extends('layouts.app')

@section('title', 'Kelola Rak Arsip')

@section('content')
<div x-data="{ openCreateModal: false, editModal: false, assignModal: false, activeRak: null }">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Kelola Rak Arsip</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Atur penataan rak dan masukkan boks-boks arsip ke lokasi rak fisik.</p>
        </div>
        <div>
            <button @@click="openCreateModal = true"
                    class="flex items-center justify-center gap-2 h-[42px] px-4 rounded-lg bg-primary text-on-primary font-bold text-xs hover:bg-primary/90 transition-all shadow-sm">
                <span class="material-symbols-outlined text-base">add</span>
                <span>Tambah Rak</span>
            </button>
        </div>
    </div>


    {{-- Info Alert jika ada Boks yang Belum Punya Rak --}}
    @if ($unassignedBoks->count() > 0)
        <div class="mb-stack-lg p-stack-md rounded-lg bg-warning-container text-on-warning-container border border-warning/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-warning text-3xl">warning</span>
                <div>
                    <h4 class="font-bold">Ada {{ $unassignedBoks->count() }} Boks Arsip Belum Ditempatkan di Rak</h4>
                    <p class="text-sm opacity-90">Boks-boks ini dibuat otomatis saat import Excel. Pilih rak di bawah lalu klik "Masukkan Boks".</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Search Filter --}}
    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md mb-stack-md shadow-sm">
        <form method="GET" action="{{ route('raks.index') }}" class="flex flex-wrap gap-2">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline" style="font-size: 18px;">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full pl-9 pr-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md"
                       placeholder="Cari Nomor Rak atau Keterangan...">
            </div>
            <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded font-label-md hover:bg-primary/90">Cari</button>
            @if(request('search'))
                <a href="{{ route('raks.index') }}" class="px-4 py-2 border border-outline-variant text-on-surface-variant rounded font-label-md hover:bg-surface-container">Reset</a>
            @endif
        </form>
    </div>

    {{-- Grid Card Rak --}}
    @if ($raks->isEmpty())
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-6 sm:p-12 text-center">
            <span class="material-symbols-outlined text-outline text-6xl mb-2">shelves</span>
            <p class="text-body-lg text-on-surface-variant">Belum ada data rak fisik. Silakan klik <strong>+ Tambah Rak</strong> di atas.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-stack-md mb-stack-lg">
            @foreach ($raks as $rak)
                <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-outline-variant pb-3 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-2xl">shelves</span>
                                <h3 class="font-display-md text-title-lg text-on-surface">Rak {{ $rak->nomor_rak }}</h3>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @@click="editModal = true; activeRak = {{ json_encode($rak) }}"
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-bold bg-primary-container text-on-primary hover:bg-primary-container/90 transition-colors shadow-sm" title="Edit Rak">
                                    <span class="material-symbols-outlined text-xs">edit</span> Edit
                                </button>
                                <form action="{{ route('raks.destroy', $rak->id) }}" method="POST" data-confirm="Hapus Rak {{ $rak->nomor_rak }}? Boks di dalamnya akan dikeluarkan dari rak ini.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-bold bg-error-container text-on-error-container hover:bg-error-container/80 transition-colors shadow-sm" title="Hapus Rak">
                                        <span class="material-symbols-outlined text-xs">delete</span> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if ($rak->keterangan)
                            <p class="text-body-sm text-on-surface-variant mb-3 italic">{{ $rak->keterangan }}</p>
                        @endif

                        {{-- Daftar Boks di Rak Ini --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-label-md font-bold text-on-surface-variant">Daftar Boks ({{ $rak->boks->count() }} Boks):</span>
                                <button @@click="assignModal = true; activeRak = {{ json_encode($rak->load(['boks.unit', 'boks.arsips'])) }}"
                                        class="text-label-sm text-primary font-bold hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">edit_note</span> Kelola Boks
                                </button>
                            </div>

                            @if ($rak->boks->isEmpty())
                                <div class="p-3 rounded-lg bg-surface-container/50 border border-outline-variant/40 text-on-surface-variant text-xs text-center italic">
                                    Rak ini belum diisi boks arsip.
                                </div>
                            @else
                                <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                    @foreach ($rak->boks as $b)
                                        <div class="p-2 rounded-lg border border-outline-variant/60 bg-surface-container-lowest flex items-start justify-between gap-2 text-xs hover:border-primary/50 transition-colors">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 font-bold text-on-surface">
                                                    <span class="material-symbols-outlined text-primary text-sm shrink-0">package_2</span>
                                                    <span class="truncate">{{ $b->unit?->nama_unit ?? 'Unit Umum' }}</span>
                                                </div>
                                                <div class="text-[11px] text-on-surface-variant/80 mt-0.5 flex items-center gap-2">
                                                    <span class="font-semibold text-primary">Boks {{ $b->nomor_boks }}</span>
                                                    @if($b->range_berkas)
                                                        <span class="px-1.5 py-0.2 rounded bg-surface-container text-on-surface font-mono font-bold">{{ $b->range_berkas }}</span>
                                                    @endif
                                                    <span>({{ $b->tahun }})</span>
                                                </div>
                                            </div>
                                            <span class="text-[11px] font-bold text-primary shrink-0 px-2 py-0.5 rounded bg-primary-fixed/20">
                                                {{ $b->arsips->count() }} Berkas
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-outline-variant flex items-center justify-between text-xs text-on-surface-variant">
                        <span>Total Berkas: {{ $rak->boks->sum(fn($b) => $b->arsips->count()) }} berkas</span>
                        <button @@click="assignModal = true; activeRak = {{ json_encode($rak->load(['boks.unit', 'boks.arsips'])) }}"
                                class="px-3 py-1.5 rounded bg-primary/10 text-primary hover:bg-primary/20 font-bold">
                            + Masukkan Boks
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $raks->links() }}
    @endif

    {{-- MODAL TAMBAH RAK --}}
    <div x-show="openCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-scrim/40 p-4" x-cloak>
        <div class="bg-surface rounded-xl border border-outline-variant shadow-lg max-w-md w-full p-stack-lg" @@click.outside="openCreateModal = false">
            <h3 class="font-display-md text-title-md mb-4 text-on-surface">Tambah Rak Fisik Baru</h3>
            <form action="{{ route('raks.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-label-md font-bold mb-1">Nomor / Label Rak <span class="text-error">*</span></label>
                    <input type="text" name="nomor_rak" required placeholder="Contoh: 1, 2, A1, B-02"
                           class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary">
                </div>
                <div class="mb-6">
                    <label class="block text-label-md font-bold mb-1">Keterangan / Lokasi</label>
                    <textarea name="keterangan" rows="2" placeholder="Contoh: Barisan Pojok Kiri Ruang Utama"
                              class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary">{{ old('keterangan') }}</textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @@click="openCreateModal = false" class="px-4 py-2 border rounded text-on-surface-variant">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT RAK --}}
    <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-scrim/40 p-4" x-cloak>
        <div class="bg-surface rounded-xl border border-outline-variant shadow-lg max-w-md w-full p-stack-lg" @@click.outside="editModal = false">
            <h3 class="font-display-md text-title-md mb-4 text-on-surface">Edit Rak</h3>
            <form x-bind:action="'{{ url('raks') }}/' + (activeRak ? activeRak.id : '')" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-label-md font-bold mb-1">Nomor / Label Rak <span class="text-error">*</span></label>
                    <input type="text" name="nomor_rak" x-bind:value="activeRak ? activeRak.nomor_rak : ''" required
                           class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary">
                </div>
                <div class="mb-6">
                    <label class="block text-label-md font-bold mb-1">Keterangan / Lokasi</label>
                    <textarea name="keterangan" rows="2" x-text="activeRak ? activeRak.keterangan : ''"
                              class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @@click="editModal = false" class="px-4 py-2 border rounded text-on-surface-variant">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded font-bold">Update</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL ASSIGN BOKS KE RAK --}}
    <div x-show="assignModal" class="fixed inset-0 z-50 flex items-center justify-center bg-scrim/40 p-4" x-cloak>
        <div class="bg-surface rounded-xl border border-outline-variant shadow-lg max-w-2xl w-full p-stack-lg max-h-[85vh] flex flex-col" @@click.outside="assignModal = false">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="font-display-md text-title-md text-on-surface">
                    Kelola Boks di <span class="text-primary font-bold" x-text="'Rak ' + (activeRak ? activeRak.nomor_rak : '')"></span>
                </h3>
                <button @@click="assignModal = false" class="text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form x-bind:action="'{{ url('raks') }}/' + (activeRak ? activeRak.id : '') + '/assign-boks'" method="POST" class="flex-1 flex flex-col overflow-hidden" x-data="{ searchModalBoks: '' }">
                @csrf
                
                {{-- Search Box di dalam Modal Kelola Boks --}}
                <div class="mb-3 relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70 text-base">search</span>
                    <input type="text" x-model="searchModalBoks" placeholder="Cari unit, nomor boks, atau rentang berkas (misal: Pekanbaru, Boks 1, No. 1-6)..."
                           class="w-full pl-9 pr-8 py-2 border border-outline-variant rounded-lg bg-surface text-xs focus:outline-none focus:border-primary text-on-surface">
                    <button type="button" x-show="searchModalBoks.length > 0" @@click="searchModalBoks = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>

                <p class="text-body-sm text-on-surface-variant mb-2">Centang boks-boks yang ingin ditempatkan di Rak ini:</p>

                @php
                    $allBoks = \App\Models\Boks::with(['unit', 'arsips', 'rak'])->orderBy('tahun', 'desc')->orderBy('nomor_boks', 'asc')->get();
                @endphp

                <div class="flex-1 overflow-y-auto border border-outline-variant/60 rounded-xl p-3 mb-4 space-y-2 bg-surface-container-lowest max-h-[50vh]">
                    @forelse ($allBoks as $b)
                        <label class="flex items-center justify-between p-2.5 rounded-lg hover:bg-surface-container/60 cursor-pointer border border-outline-variant/40 transition-colors"
                               x-show="!searchModalBoks || '{{ strtolower("Boks " . $b->nomor_boks . " " . ($b->unit?->nama_unit ?? "Unit Umum") . " " . $b->range_berkas . " Tahun " . $b->tahun) }}'.includes(searchModalBoks.toLowerCase())">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <input type="checkbox" name="boks_ids[]" value="{{ $b->id }}"
                                       x-bind:checked="activeRak && activeRak.boks && activeRak.boks.some(x => x.id === {{ $b->id }})"
                                       class="w-4 h-4 rounded text-primary focus:ring-primary shrink-0">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-on-surface text-xs">Boks {{ $b->nomor_boks }}</span>
                                        <span class="text-xs px-2 py-0.5 rounded bg-primary-fixed/20 text-primary font-bold">
                                            {{ $b->unit?->nama_unit ?? 'Unit Umum' }}
                                        </span>
                                        @if($b->range_berkas)
                                            <span class="text-[11px] px-1.5 py-0.2 rounded bg-surface-container font-mono font-bold text-on-surface">
                                                {{ $b->range_berkas }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-on-surface-variant/80 mt-0.5">
                                        Tahun {{ $b->tahun }} &bull; Total {{ $b->arsips->count() }} Berkas
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs text-on-surface-variant font-mono shrink-0 ml-2">
                                @if($b->rak_id)
                                    <span x-show="activeRak && {{ $b->rak_id }} === activeRak.id" class="text-primary font-bold px-2 py-0.5 rounded bg-primary/10">[Di Rak Ini]</span>
                                    <span x-show="!activeRak || {{ $b->rak_id }} !== activeRak.id" class="text-warning font-bold px-2 py-0.5 rounded bg-warning-container">[Di Rak {{ $b->rak?->nomor_rak }}]</span>
                                @else
                                    <span class="text-outline italic px-2 py-0.5 rounded bg-surface-container">[Belum Ada Rak]</span>
                                @endif
                            </span>
                        </label>
                    @empty
                        <p class="text-center text-on-surface-variant py-4 text-xs">Belum ada boks terdaftar. Silakan import Excel terlebih dahulu.</p>
                    @endforelse
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" @@click="assignModal = false" class="px-4 py-2 border rounded text-on-surface-variant">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded font-bold">Simpan Penempatan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
