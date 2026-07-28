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
        <form method="GET" action="{{ route('peminjaman.index') }}" class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline" style="font-size: 18px;">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full pl-9 pr-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md"
                       placeholder="Cari peminjam, instansi, atau arsip...">
            </div>
            <select name="status" class="px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                <option value="">Semua Status</option>
                <option value="dipinjam" @selected(request('status') === 'dipinjam')>Dipinjam</option>
                <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>Dikembalikan</option>
                <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded font-label-md hover:bg-primary/90">Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('peminjaman.index') }}" class="px-4 py-2 border border-outline-variant text-on-surface-variant rounded font-label-md hover:bg-surface-container">Reset</a>
            @endif
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
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant whitespace-nowrap">INSTANSI</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">TGL PINJAM</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">RENCANA KEMBALI</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">DIKEMBALIKAN</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">STATUS</th>
                            <th class="py-3 px-4 font-table-header text-xs text-on-surface-variant text-right whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/60">
                        @foreach ($peminjamen as $idx => $p)
                            <tr class="hover:bg-surface-container/30 transition-colors">
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant text-center font-medium">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 text-body-sm text-on-surface max-w-xs">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-primary text-xs">{{ $p->arsip->kode_klasifikasi ?? '-' }}</span>
                                        <span class="text-xs text-on-surface-variant line-clamp-1" title="{{ $p->arsip->uraian_informasi_arsip }}">
                                            {{ $p->arsip->uraian_informasi_arsip ?? '-' }}
                                        </span>
                                        @if($p->arsip->unit)
                                            <span class="text-[10px] text-primary/70 mt-0.5">{{ $p->arsip->unit->nama_unit }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-body-sm text-on-surface font-medium whitespace-nowrap">{{ $p->nama_peminjam }}</td>
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant whitespace-nowrap">{{ $p->instansi ?? '-' }}</td>
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant text-center whitespace-nowrap font-mono">
                                    {{ $p->tanggal_pinjam->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant text-center whitespace-nowrap font-mono">
                                    {{ $p->tanggal_kembali_rencana ? $p->tanggal_kembali_rencana->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant text-center whitespace-nowrap font-mono">
                                    {{ $p->tanggal_dikembalikan ? $p->tanggal_dikembalikan->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold uppercase {{ $p->status_badge }}">
                                        {{ $p->status_label }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                                    @if ($p->status === 'dipinjam')
                                        <form action="{{ route('peminjaman.kembalikan', $p) }}" method="POST" class="inline" data-confirm="Tandai arsip ini sudah dikembalikan?">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-primary-fixed text-on-primary-fixed hover:bg-primary-fixed-dim transition-colors shadow-sm" title="Kembalikan">
                                                <span class="material-symbols-outlined text-xs">assignment_return</span>
                                                <span>Kembalikan</span>
                                            </button>
                                        </form>
                                    @endif
                                    <button @click="openEditModal({{ $p->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-primary-container text-on-primary hover:bg-primary-container/90 transition-colors shadow-sm" title="Edit">
                                        <span class="material-symbols-outlined text-xs">edit</span>
                                        <span>Edit</span>
                                    </button>
                                    <form action="{{ route('peminjaman.destroy', $p) }}" method="POST" class="inline" data-confirm="Hapus data peminjaman ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-error-container text-on-error-container hover:bg-error-container/80 transition-colors shadow-sm" title="Hapus">
                                            <span class="material-symbols-outlined text-xs">delete</span>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($peminjamen->hasPages())
        <div class="mt-stack-md">{{ $peminjamen->links() }}</div>
    @endif

    {{-- MODAL TAMBAH PINJAMAN --}}
    <div x-show="createModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="createModal" x-transition.opacity class="fixed inset-0 bg-black/50 transition-opacity" @click="createModal = false"></div>
            <div x-show="createModal" x-transition.scale class="relative bg-surface rounded-2xl shadow-xl max-w-xl w-full p-6 border border-outline-variant max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-headline-sm font-bold text-on-surface">Catat Peminjaman Baru</h3>
                    <button @click="createModal = false" class="text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('peminjaman.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div class="relative">
                            <label class="block text-label-md font-bold text-on-surface-variant mb-1">Arsip <span class="text-error">*</span></label>
                            <div class="relative">
                                <input type="text" x-model="arsipSearch" @input="filterArsip()" @focus="arsipDropdown = true" @click.outside="arsipDropdown = false"
                                       placeholder="Cari kode klasifikasi atau uraian arsip..."
                                       class="w-full pl-9 pr-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/70 text-base">search</span>
                            </div>
                            <div x-show="arsipDropdown && filteredArsips.length > 0" class="absolute left-0 right-0 z-10 mt-1 bg-surface border border-outline-variant rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                <template x-for="a in filteredArsips" :key="a.id">
                                    <div @click="selectArsip(a)" class="px-3 py-2 hover:bg-primary-fixed/20 cursor-pointer text-sm border-b border-outline-variant/40 last:border-0">
                                        <div class="font-bold text-primary text-xs" x-text="a.label.split(' - ')[0]"></div>
                                        <div class="text-xs text-on-surface-variant" x-text="a.label.split(' - ').slice(1).join(' - ')"></div>
                                    </div>
                                </template>
                            </div>
                            <p x-show="selectedArsipLabel" class="mt-1.5 text-xs font-bold text-primary flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                <span x-text="selectedArsipLabel"></span>
                            </p>
                            <input type="hidden" name="arsip_id" x-model="selectedArsipId">
                        </div>

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
            <div x-show="editModal" x-transition.scale class="relative bg-surface rounded-2xl shadow-xl max-w-xl w-full p-6 border border-outline-variant max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-headline-sm font-bold text-on-surface">Edit Peminjaman</h3>
                    <button @click="editModal = false" class="text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form method="POST" :action="'/peminjaman/' + editId">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div class="relative">
                            <label class="block text-label-md font-bold text-on-surface-variant mb-1">Arsip <span class="text-error">*</span></label>
                            <div class="relative">
                                <input type="text" x-model="editArsipSearch" @input="filterEditArsip()" @focus="editArsipDropdown = true" @click.outside="editArsipDropdown = false"
                                       placeholder="Cari kode klasifikasi atau uraian arsip..."
                                       class="w-full pl-9 pr-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/70 text-base">search</span>
                            </div>
                            <div x-show="editArsipDropdown && filteredEditArsips.length > 0" class="absolute left-0 right-0 z-10 mt-1 bg-surface border border-outline-variant rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                <template x-for="a in filteredEditArsips" :key="a.id">
                                    <div @click="selectEditArsip(a)" class="px-3 py-2 hover:bg-primary-fixed/20 cursor-pointer text-sm border-b border-outline-variant/40 last:border-0">
                                        <div class="font-bold text-primary text-xs" x-text="a.label.split(' - ')[0]"></div>
                                        <div class="text-xs text-on-surface-variant" x-text="a.label.split(' - ').slice(1).join(' - ')"></div>
                                    </div>
                                </template>
                            </div>
                            <p x-show="selectedEditArsipLabel" class="mt-1.5 text-xs font-bold text-primary flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                <span x-text="selectedEditArsipLabel"></span>
                            </p>
                            <input type="hidden" name="arsip_id" x-model="selectedEditArsipId">
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
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('peminjamanPage', () => ({
        createModal: false,
        editModal: false,
        editId: null,

        // Create form state
        arsipSearch: '',
        arsipDropdown: false,
        filteredArsips: [],
        selectedArsipId: null,
        selectedArsipLabel: '',

        // Edit form state
        editArsipSearch: '',
        editArsipDropdown: false,
        filteredEditArsips: [],
        selectedEditArsipId: null,
        selectedEditArsipLabel: '',
        editNamaPeminjam: '',
        editInstansi: '',
        editTelp: '',
        editKeperluan: '',
        editTglPinjam: '',
        editTglKembaliRencana: '',
        editKeterangan: '',

        openCreateModal() {
            this.createModal = true;
            this.arsipSearch = '';
            this.selectedArsipId = null;
            this.selectedArsipLabel = '';
            this.arsipDropdown = false;
            this.filteredArsips = [];
        },

        async filterArsip() {
            const s = this.arsipSearch.trim();
            if (s.length < 1) {
                this.filteredArsips = [];
                this.arsipDropdown = false;
                return;
            }
            try {
                const res = await fetch(`/peminjaman/arsips/search?q=${encodeURIComponent(s)}`);
                this.filteredArsips = await res.json();
                this.arsipDropdown = this.filteredArsips.length > 0;
            } catch (e) {
                this.filteredArsips = [];
            }
        },

        selectArsip(a) {
            this.selectedArsipId = a.id;
            this.selectedArsipLabel = a.label;
            this.arsipSearch = a.label;
            this.arsipDropdown = false;
        },

        openEditModal(id) {
            this.editId = id;
            this.editModal = true;
            this.editArsipDropdown = false;

            fetch(`/peminjaman/${id}/json`)
                .then(r => r.json())
                .then(data => {
                    this.selectedEditArsipId = data.arsip_id;
                    this.selectedEditArsipLabel = data.arsip_label;
                    this.editArsipSearch = data.arsip_label;
                    this.editNamaPeminjam = data.nama_peminjam;
                    this.editInstansi = data.instansi || '';
                    this.editTelp = data.telp || '';
                    this.editKeperluan = data.keperluan || '';
                    this.editTglPinjam = data.tanggal_pinjam;
                    this.editTglKembaliRencana = data.tanggal_kembali_rencana || '';
                    this.editKeterangan = data.keterangan || '';
                });
        },

        async filterEditArsip() {
            const s = this.editArsipSearch.trim();
            if (s.length < 1) {
                this.filteredEditArsips = [];
                this.editArsipDropdown = false;
                return;
            }
            try {
                const res = await fetch(`/peminjaman/arsips/search?q=${encodeURIComponent(s)}`);
                this.filteredEditArsips = await res.json();
                this.editArsipDropdown = this.filteredEditArsips.length > 0;
            } catch (e) {
                this.filteredEditArsips = [];
            }
        },

        selectEditArsip(a) {
            this.selectedEditArsipId = a.id;
            this.selectedEditArsipLabel = a.label;
            this.editArsipSearch = a.label;
            this.editArsipDropdown = false;
        }
    }));
});
</script>
@endsection
