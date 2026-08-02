@extends('layouts.app')

@section('title', 'Peminjaman Arsip')

@section('content')

<div x-data="peminjamanPage()">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Peminjaman Arsip</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Catat dan pantau peminjaman arsip oleh pihak internal maupun eksternal.</p>
        </div>
        <div>
            <button @click="openCreateModal()"
                    class="flex items-center justify-center gap-2 h-[42px] px-4 rounded-lg bg-primary text-on-primary font-bold text-xs hover:bg-primary/90 transition-all shadow-sm">
                <span class="material-symbols-outlined text-base">add</span>
                <span>Tambah Peminjaman</span>
            </button>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md mb-stack-lg shadow-sm">
        <form method="GET" action="{{ route('peminjaman.index') }}" class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:gap-3">
            <div class="relative flex-1 min-w-0 w-full sm:min-w-[180px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline" style="font-size: 18px;">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full pl-9 pr-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md"
                       placeholder="Cari peminjam, instansi...">
            </div>
            <select name="status"
                    class="w-full sm:w-auto pl-3 pr-10 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md appearance-none" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23131313%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.7rem top 50%; background-size: 0.65rem auto;">
                <option value="">Semua Status</option>
                <option value="dipinjam" @selected(request('status') === 'dipinjam')>Dipinjam</option>
                <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>Dikembalikan</option>
                <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat</option>
            </select>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-initial px-4 py-2 bg-primary text-on-primary rounded font-label-md hover:bg-primary/90">Filter</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('peminjaman.index') }}" class="flex-1 sm:flex-initial px-4 py-2 border border-outline-variant text-on-surface-variant rounded font-label-md hover:bg-surface-container text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    @if ($peminjamen->isEmpty())
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-6 sm:p-12 text-center shadow-sm">
            <span class="material-symbols-outlined text-6xl text-outline mb-3">book</span>
            <p class="text-body-lg text-on-surface-variant">Belum ada data peminjaman arsip.</p>
            <button @click="openCreateModal()" class="mt-3 text-primary font-bold hover:underline text-sm">+ Catat peminjaman pertama</button>
        </div>
    @else
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container/40 border-b border-outline-variant">
                        <tr>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant whitespace-nowrap">NO</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant whitespace-nowrap">ARSIP</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant whitespace-nowrap">PEMINJAM</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant whitespace-nowrap">TGL PINJAM</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">RENCANA KEMBALI</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">DIKEMBALIKAN</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">STATUS</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant text-right whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/60">
                        @foreach ($peminjamen as $idx => $p)
                            @php
                                $firstArsip = $p->arsips->first();
                                $totalArsip = $p->arsips->count();
                            @endphp
                            <tr class="hover:bg-surface-container/30 transition-colors">
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant text-center font-medium">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 text-body-sm text-on-surface max-w-sm">
                                    @if($firstArsip)
                                        <div class="flex items-center gap-2 cursor-pointer group" @click="openDetailModal({{ $p->id }})">
                                            <span class="material-symbols-outlined text-base text-primary shrink-0 group-hover:scale-110 transition-transform">description</span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-primary text-xs">{{ $firstArsip->kode_klasifikasi ?? '-' }}</span>
                                                    @if($firstArsip->unit)
                                                        <span class="text-[10px] px-1.5 py-0.2 rounded bg-surface-container text-on-surface font-semibold">{{ $firstArsip->unit->nama_unit }}</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-on-surface-variant block leading-tight line-clamp-1 group-hover:text-primary transition-colors">
                                                    {{ $firstArsip->uraian_informasi_arsip ?? '-' }}
                                                </span>
                                            </div>
                                            @if($totalArsip > 1)
                                                <span class="px-2 py-0.5 rounded-full bg-primary-fixed/30 text-primary font-bold text-[11px] shrink-0 border border-primary/20 hover:bg-primary-fixed/50 transition-colors"
                                                      title="Klik untuk lihat {{ $totalArsip }} arsip">
                                                    +{{ $totalArsip - 1 }} arsip
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-outline italic">Tidak ada arsip</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-body-sm text-on-surface font-medium whitespace-nowrap">{{ $p->nama_peminjam }}</td>
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant whitespace-nowrap font-mono">{{ $p->tanggal_pinjam->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant text-center whitespace-nowrap font-mono">{{ $p->tanggal_kembali_rencana ? $p->tanggal_kembali_rencana->format('d/m/Y') : '-' }}</td>
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant text-center whitespace-nowrap font-mono">{{ $p->tanggal_dikembalikan ? $p->tanggal_dikembalikan->format('d/m/Y') : '-' }}</td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold uppercase {{ $p->status_badge }}">
                                        {{ $p->status_label }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                        <button @click="openDetailModal({{ $p->id }})"
                                                class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg text-xs font-bold bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors shadow-sm"
                                                title="Lihat Detail Peminjaman">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                            <span>Detail</span>
                                        </button>

                                        @if ($p->status === 'dipinjam')
                                            <form action="{{ route('peminjaman.kembalikan', $p) }}" method="POST" data-confirm="Tandai arsip ini sudah dikembalikan?" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg text-xs font-bold bg-primary-fixed text-on-primary-fixed hover:bg-primary-fixed-dim transition-colors shadow-sm" title="Kembalikan Arsip">
                                                    <span class="material-symbols-outlined text-sm">assignment_return</span>
                                                    <span>Kembalikan</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($peminjamen->hasPages())
            <div class="mt-stack-md">{{ $peminjamen->links() }}</div>
        @endif
    @endif

    {{-- MODAL TAMBAH PINJAMAN --}}
    <div x-show="createModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="createModal" x-transition.opacity class="fixed inset-0 bg-black/50 transition-opacity" @click="createModal = false"></div>
            <div x-show="createModal" x-transition.scale class="relative bg-surface rounded-2xl shadow-xl max-w-2xl w-full p-6 border border-outline-variant max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-headline-sm font-bold text-on-surface">Catat Peminjaman Baru</h3>
                    <button @click="createModal = false" class="text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('peminjaman.store') }}">
                    @csrf
                    <div class="space-y-4">
                        {{-- Pilih Unit --}}
                        <div>
                            <label class="block text-label-md font-bold text-on-surface-variant mb-1">Pilih Unit <span class="text-error">*</span></label>
                            <select x-model="createUnitId" @change="loadArsipsByUnit()"
                                    class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                                <option value="">— Pilih Unit —</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit }} ({{ $unit->kode_unit }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Daftar Arsip per Boks --}}
                        <div x-show="createUnitId" class="border border-outline-variant rounded-xl overflow-hidden">
                            <div class="bg-surface-container/40 px-4 py-2 text-xs font-bold text-on-surface-variant border-b border-outline-variant flex items-center justify-between">
                                <span>Pilih Arsip yang Akan Dipinjam</span>
                                <span x-text="selectedCreateIds.length + ' dipilih'"></span>
                            </div>
                            <div class="max-h-64 overflow-y-auto p-3 space-y-3">
                                <template x-for="group in createArsipGroups" :key="group.group">
                                    <div>
                                        <label class="flex items-center gap-2 px-2 py-1.5 bg-surface-container/60 rounded-lg cursor-pointer font-bold text-xs text-on-surface mb-1">
                                            <input type="checkbox" @change="toggleCreateGroup(group)" :checked="isCreateGroupFullyChecked(group)"
                                                   class="w-4 h-4 rounded text-primary focus:ring-primary">
                                            <span class="material-symbols-outlined text-sm text-primary">package_2</span>
                                            <span x-text="group.group"></span>
                                            <span class="text-on-surface-variant font-normal" x-text="`(${group.items.length})`"></span>
                                        </label>
                                        <div class="ml-2 space-y-1">
                                            <template x-for="item in group.items" :key="item.id">
                                                <label class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-surface-container/60 cursor-pointer transition-colors">
                                                    <input type="checkbox" :value="item.id" x-model="selectedCreateIds"
                                                           class="w-4 h-4 rounded text-primary focus:ring-primary shrink-0">
                                                    <div class="min-w-0">
                                                        <span class="text-xs font-bold text-primary" x-text="item.kode"></span>
                                                        <span class="text-xs text-on-surface-variant block leading-tight line-clamp-1" x-text="item.uraian"></span>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <p x-show="createArsipGroups.length === 0 && createUnitId" class="text-center text-xs text-on-surface-variant italic py-4">
                                    Tidak ada arsip untuk unit ini.
                                </p>
                            </div>
                        </div>

                        @foreach (['arsip_ids_input' => 'selectedCreateIds'] as $inputName => $model)
                            <template x-for="id in {{ $model }}" :key="'create-' + id">
                                <input type="hidden" name="arsip_ids[]" x-bind:value="id">
                            </template>
                        @endforeach

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-label-md font-bold text-on-surface-variant mb-1">Nama Peminjam <span class="text-error">*</span></label>
                                <input type="text" name="nama_peminjam" required placeholder="Nama lengkap"
                                       class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-label-md font-bold text-on-surface-variant mb-1">Instansi / Unit</label>
                                <input type="text" name="instansi" placeholder="Contoh: Bapenda, Kejaksaan"
                                       class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary">
                            </div>
                        </div>

                        <div>
                            <label class="block text-label-md font-bold text-on-surface-variant mb-1">No. Telp / Kontak</label>
                            <input type="text" name="telp" placeholder="Contoh: 0812-xxxx-xxxx"
                                   class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-label-md font-bold text-on-surface-variant mb-1">Keperluan</label>
                            <textarea name="keperluan" rows="2" placeholder="Tujuan peminjaman arsip..."
                                      class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-label-md font-bold text-on-surface-variant mb-1">Tanggal Pinjam <span class="text-error">*</span></label>
                                <input type="date" name="tanggal_pinjam" required value="{{ date('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-label-md font-bold text-on-surface-variant mb-1">Rencana Kembali</label>
                                <input type="date" name="tanggal_kembali_rencana"
                                       class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary">
                            </div>
                        </div>

                        <div>
                            <label class="block text-label-md font-bold text-on-surface-variant mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="1" placeholder="Catatan tambahan..."
                                      class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="createModal = false" class="px-4 py-2 text-label-md font-bold text-on-surface-variant hover:bg-surface-container rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2 text-label-md font-bold bg-primary text-on-primary hover:bg-primary/90 rounded-lg transition-colors shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT PINJAMAN --}}
    <div x-show="editModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="editModal" x-transition.opacity class="fixed inset-0 bg-black/50 transition-opacity" @click="editModal = false"></div>
            <div x-show="editModal" x-transition.scale class="relative bg-surface rounded-2xl shadow-xl max-w-2xl w-full p-6 border border-outline-variant max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-headline-sm font-bold text-on-surface">Edit Peminjaman</h3>
                    <button @click="editModal = false" class="text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form method="POST" :action="'/peminjaman/' + editId">
                    @csrf
                    @method('PUT')

                    @foreach (['arsip_ids_input' => 'selectedEditIds'] as $inputName => $model)
                        <template x-for="id in {{ $model }}" :key="'edit-' + id">
                            <input type="hidden" name="arsip_ids[]" x-bind:value="id">
                        </template>
                    @endforeach

                    <div class="space-y-4">
                        {{-- Pilih Unit --}}
                        <div>
                            <label class="block text-label-md font-bold text-on-surface-variant mb-1">Pilih Unit <span class="text-error">*</span></label>
                            <select x-model="editUnitId" @change="loadEditArsipsByUnit()"
                                    class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                                <option value="">— Pilih Unit —</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit }} ({{ $unit->kode_unit }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Daftar Arsip per Boks --}}
                        <div x-show="editUnitId" class="border border-outline-variant rounded-xl overflow-hidden">
                            <div class="bg-surface-container/40 px-4 py-2 text-xs font-bold text-on-surface-variant border-b border-outline-variant flex items-center justify-between">
                                <span>Pilih Arsip yang Akan Dipinjam</span>
                                <span x-text="selectedEditIds.length + ' dipilih'"></span>
                            </div>
                            <div class="max-h-64 overflow-y-auto p-3 space-y-3">
                                <template x-for="group in editArsipGroups" :key="group.group">
                                    <div>
                                        <label class="flex items-center gap-2 px-2 py-1.5 bg-surface-container/60 rounded-lg cursor-pointer font-bold text-xs text-on-surface mb-1">
                                            <input type="checkbox" @change="toggleEditGroup(group)" :checked="isEditGroupFullyChecked(group)"
                                                   class="w-4 h-4 rounded text-primary focus:ring-primary">
                                            <span class="material-symbols-outlined text-sm text-primary">package_2</span>
                                            <span x-text="group.group"></span>
                                            <span class="text-on-surface-variant font-normal" x-text="`(${group.items.length})`"></span>
                                        </label>
                                        <div class="ml-2 space-y-1">
                                            <template x-for="item in group.items" :key="item.id">
                                                <label class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-surface-container/60 cursor-pointer transition-colors">
                                                    <input type="checkbox" :value="item.id" x-model="selectedEditIds"
                                                           class="w-4 h-4 rounded text-primary focus:ring-primary shrink-0">
                                                    <div class="min-w-0">
                                                        <span class="text-xs font-bold text-primary" x-text="item.kode"></span>
                                                        <span class="text-xs text-on-surface-variant block leading-tight line-clamp-1" x-text="item.uraian"></span>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <p x-show="editArsipGroups.length === 0 && editUnitId" class="text-center text-xs text-on-surface-variant italic py-4">
                                    Tidak ada arsip untuk unit ini.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-label-md font-bold text-on-surface-variant mb-1">Nama Peminjam <span class="text-error">*</span></label>
                                <input type="text" name="nama_peminjam" required x-model="editNamaPeminjam"
                                       class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-label-md font-bold text-on-surface-variant mb-1">Instansi / Unit</label>
                                <input type="text" name="instansi" x-model="editInstansi"
                                       class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary">
                            </div>
                        </div>

                        <div>
                            <label class="block text-label-md font-bold text-on-surface-variant mb-1">No. Telp / Kontak</label>
                            <input type="text" name="telp" x-model="editTelp"
                                   class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-label-md font-bold text-on-surface-variant mb-1">Keperluan</label>
                            <textarea name="keperluan" rows="2" x-model="editKeperluan"
                                      class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-label-md font-bold text-on-surface-variant mb-1">Tanggal Pinjam <span class="text-error">*</span></label>
                                <input type="date" name="tanggal_pinjam" required x-model="editTglPinjam"
                                       class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-label-md font-bold text-on-surface-variant mb-1">Rencana Kembali</label>
                                <input type="date" name="tanggal_kembali_rencana" x-model="editTglKembaliRencana"
                                       class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary">
                            </div>
                        </div>

                        <div>
                            <label class="block text-label-md font-bold text-on-surface-variant mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="1" x-model="editKeterangan"
                                      class="w-full px-3 py-2 border border-outline-variant rounded bg-surface text-sm focus:outline-none focus:border-primary"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="editModal = false" class="px-4 py-2 text-label-md font-bold text-on-surface-variant hover:bg-surface-container rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2 text-label-md font-bold bg-primary text-on-primary hover:bg-primary/90 rounded-lg transition-colors shadow-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL PEMINJAMAN --}}
    <div x-show="detailModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="detailModal" x-transition.opacity class="fixed inset-0 bg-black/50 transition-opacity" @click="detailModal = false"></div>
            <div x-show="detailModal" x-transition.scale class="relative bg-surface rounded-2xl shadow-xl max-w-3xl w-full p-6 border border-outline-variant max-h-[90vh] overflow-y-auto flex flex-col">
                <div class="flex items-center justify-between border-b border-outline-variant pb-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-fixed/30 text-primary flex items-center justify-center shrink-0 border border-primary/20">
                            <span class="material-symbols-outlined text-xl">description</span>
                        </div>
                        <div>
                            <h3 class="text-title-lg font-bold text-on-surface">Detail Peminjaman Arsip</h3>
                            <p class="text-xs text-on-surface-variant">Rincian informasi peminjaman dan daftar berkas yang dipinjam</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <template x-if="detailData">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase"
                                  :class="detailData.status_badge"
                                  x-text="detailData.status_label"></span>
                        </template>
                        <button @click="detailModal = false" class="text-on-surface-variant hover:text-on-surface p-1 rounded-lg">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                </div>

                <template x-if="loadingDetail">
                    <div class="py-12 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl animate-spin text-primary mb-2">progress_activity</span>
                        <p class="text-sm font-medium">Memuat detail peminjaman...</p>
                    </div>
                </template>

                <template x-if="!loadingDetail && detailData">
                    <div class="space-y-5">
                        {{-- Ringkasan Peminjam & Tanggal --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-surface-container/40 border border-outline-variant/60">
                            <div>
                                <h4 class="text-xs font-bold text-primary uppercase tracking-wider mb-2.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-base">person</span>
                                    <span>Identitas Peminjam</span>
                                </h4>
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between items-center">
                                        <span class="text-on-surface-variant">Nama Peminjam:</span>
                                        <span class="font-bold text-on-surface text-sm" x-text="detailData.nama_peminjam"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-on-surface-variant">Instansi / Unit:</span>
                                        <span class="font-medium text-on-surface" x-text="detailData.instansi || '-'"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-on-surface-variant">No. Telp / Kontak:</span>
                                        <span class="font-mono text-on-surface" x-text="detailData.telp || '-'"></span>
                                    </div>
                                    <div class="pt-2 border-t border-outline-variant/30">
                                        <span class="text-on-surface-variant block mb-1 font-semibold">Keperluan:</span>
                                        <p class="text-on-surface bg-surface p-2.5 rounded-lg border border-outline-variant/40 text-xs leading-relaxed" x-text="detailData.keperluan || 'Tidak dicantumkan'"></p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-primary uppercase tracking-wider mb-2.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-base">calendar_clock</span>
                                    <span>Jadwal & Status</span>
                                </h4>
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between items-center">
                                        <span class="text-on-surface-variant">Tanggal Pinjam:</span>
                                        <span class="font-mono font-bold text-on-surface" x-text="detailData.tanggal_pinjam || '-'"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-on-surface-variant">Rencana Kembali:</span>
                                        <span class="font-mono font-bold text-warning" x-text="detailData.tanggal_kembali_rencana || '-'"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-on-surface-variant">Dikembalikan Pada:</span>
                                        <span class="font-mono font-bold text-success" x-text="detailData.tanggal_dikembalikan || '-'"></span>
                                    </div>
                                    <template x-if="detailData.keterangan">
                                        <div class="pt-2 border-t border-outline-variant/30">
                                            <span class="text-on-surface-variant block mb-1 font-semibold">Catatan / Keterangan:</span>
                                            <p class="text-on-surface bg-surface p-2.5 rounded-lg border border-outline-variant/40 text-xs leading-relaxed" x-text="detailData.keterangan"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Daftar Arsip Dipinjam --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base">folder_open</span>
                                    <span>Daftar Berkas Dipinjam (<span x-text="detailData.arsips ? detailData.arsips.length : 0"></span> Berkas)</span>
                                </h4>
                            </div>
                            <div class="border border-outline-variant rounded-xl overflow-hidden max-h-56 overflow-y-auto shadow-sm">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-surface-container/60 border-b border-outline-variant sticky top-0">
                                        <tr>
                                            <th class="py-2.5 px-3 font-bold text-on-surface-variant text-center">NO</th>
                                            <th class="py-2.5 px-3 font-bold text-on-surface-variant">KODE</th>
                                            <th class="py-2.5 px-3 font-bold text-on-surface-variant">URAIAN INFORMASI ARSIP</th>
                                            <th class="py-2.5 px-3 font-bold text-on-surface-variant">KANTOR UNIT/UPT</th>
                                            <th class="py-2.5 px-3 font-bold text-on-surface-variant">BOKS</th>
                                            <th class="py-2.5 px-3 font-bold text-on-surface-variant text-center">KURUN</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-outline-variant/40 bg-surface-container-lowest">
                                        <template x-for="(item, idx) in detailData.arsips" :key="item.id">
                                            <tr class="hover:bg-surface-container/20 transition-colors">
                                                <td class="py-2.5 px-3 text-on-surface-variant text-center font-medium" x-text="idx + 1"></td>
                                                <td class="py-2.5 px-3 font-mono font-bold text-primary whitespace-nowrap" x-text="item.kode"></td>
                                                <td class="py-2.5 px-3 text-on-surface font-medium" x-text="item.uraian"></td>
                                                <td class="py-2.5 px-3 text-on-surface-variant whitespace-nowrap" x-text="item.unit"></td>
                                                <td class="py-2.5 px-3 font-bold text-primary whitespace-nowrap" x-text="item.boks"></td>
                                                <td class="py-2.5 px-3 font-mono text-on-surface-variant text-center whitespace-nowrap" x-text="item.kurun"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="mt-6 pt-3 border-t border-outline-variant flex justify-between items-center">
                    <button type="button" @click="detailModal = false" class="px-4 py-2 text-xs font-bold border border-outline-variant rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                        Tutup
                    </button>
                    <div class="flex items-center gap-2" x-show="detailData">
                        <template x-if="detailData && detailData.status === 'dipinjam'">
                            <form :action="'/peminjaman/' + detailData.id + '/kembalikan'" method="POST" data-confirm="Tandai arsip ini sudah dikembalikan?">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-xs font-bold bg-primary-fixed text-on-primary-fixed hover:bg-primary-fixed-dim rounded-lg shadow-sm flex items-center gap-1 transition-colors">
                                    <span class="material-symbols-outlined text-xs">assignment_return</span>
                                    <span>Tandai Dikembalikan</span>
                                </button>
                            </form>
                        </template>
                        <button type="button" @click="detailModal = false; openEditModal(detailData.id)" class="px-4 py-2 text-xs font-bold bg-primary text-on-primary hover:bg-primary/90 rounded-lg shadow-sm flex items-center gap-1 transition-colors">
                            <span class="material-symbols-outlined text-xs">edit</span>
                            <span>Edit Peminjaman</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('peminjamanPage', () => ({
        createModal: false,
        editModal: false,
        detailModal: false,
        detailData: null,
        loadingDetail: false,
        editId: null,

        // Create form
        createUnitId: '',
        createArsipGroups: [],
        selectedCreateIds: [],

        // Edit form
        editUnitId: '',
        editArsipGroups: [],
        selectedEditIds: [],
        editNamaPeminjam: '',
        editInstansi: '',
        editTelp: '',
        editKeperluan: '',
        editTglPinjam: '',
        editTglKembaliRencana: '',
        editKeterangan: '',

        openDetailModal(id) {
            this.detailModal = true;
            this.loadingDetail = true;
            this.detailData = null;

            fetch(`/peminjaman/${id}/json`)
                .then(r => r.json())
                .then(data => {
                    this.detailData = data;
                    this.loadingDetail = false;
                })
                .catch(() => {
                    this.loadingDetail = false;
                });
        },

        openCreateModal() {
            this.createModal = true;
            this.createUnitId = '';
            this.createArsipGroups = [];
            this.selectedCreateIds = [];
        },

        async loadArsipsByUnit() {
            if (!this.createUnitId) { this.createArsipGroups = []; return; }
            try {
                const res = await fetch(`/peminjaman/arsips-by-unit?unit_id=${this.createUnitId}`);
                this.createArsipGroups = await res.json();
                this.selectedCreateIds = [];
            } catch (e) { this.createArsipGroups = []; }
        },

        isCreateGroupFullyChecked(group) {
            if (!group.items.length) return false;
            return group.items.every(item => this.selectedCreateIds.includes(item.id));
        },

        toggleCreateGroup(group) {
            const allIds = group.items.map(i => i.id);
            const isChecked = this.isCreateGroupFullyChecked(group);
            if (isChecked) {
                this.selectedCreateIds = this.selectedCreateIds.filter(id => !allIds.includes(id));
            } else {
                allIds.forEach(id => { if (!this.selectedCreateIds.includes(id)) this.selectedCreateIds.push(id); });
            }
        },

        loadEditArsipsByUnit() {
            if (!this.editUnitId) { this.editArsipGroups = []; return; }
            fetch(`/peminjaman/arsips-by-unit?unit_id=${this.editUnitId}`)
                .then(r => r.json())
                .then(data => {
                    this.editArsipGroups = data;
                    this.selectedEditIds = [];
                });
        },

        isEditGroupFullyChecked(group) {
            if (!group.items.length) return false;
            return group.items.every(item => this.selectedEditIds.includes(item.id));
        },

        toggleEditGroup(group) {
            const allIds = group.items.map(i => i.id);
            const isChecked = this.isEditGroupFullyChecked(group);
            if (isChecked) {
                this.selectedEditIds = this.selectedEditIds.filter(id => !allIds.includes(id));
            } else {
                allIds.forEach(id => { if (!this.selectedEditIds.includes(id)) this.selectedEditIds.push(id); });
            }
        },

        openEditModal(id) {
            this.editId = id;
            this.editModal = true;
            this.editUnitId = '';
            this.editArsipGroups = [];
            this.selectedEditIds = [];

            fetch(`/peminjaman/${id}/json`)
                .then(r => r.json())
                .then(data => {
                    this.selectedEditIds = data.arsip_ids || [];
                    this.editNamaPeminjam = data.nama_peminjam;
                    this.editInstansi = data.instansi || '';
                    this.editTelp = data.telp || '';
                    this.editKeperluan = data.keperluan || '';
                    this.editTglPinjam = data.tanggal_pinjam;
                    this.editTglKembaliRencana = data.tanggal_kembali_rencana || '';
                    this.editKeterangan = data.keterangan || '';
                });
        },
    }));
});
</script>
@endsection
