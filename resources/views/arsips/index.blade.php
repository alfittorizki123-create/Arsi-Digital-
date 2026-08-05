@extends('layouts.app')

@section('title', isset($currentUnit) ? 'Arsip - ' . $currentUnit->nama_unit : 'Daftar Arsip Pajak')

@section('content')
@php
    $exportYears = [];
    if (isset($groupedArsips)) {
        foreach ($groupedArsips as $gKey => $gItems) {
            $yearVal = $gItems->first()->kurun_waktu ?? $gKey;
            if (!isset($exportYears[$yearVal])) {
                $exportYears[$yearVal] = [
                    'tahun' => $yearVal,
                    'items' => []
                ];
            }
            foreach ($gItems as $item) {
                $exportYears[$yearVal]['items'][] = [
                    'id' => $item->id,
                    'uraian' => $item->uraian_informasi_arsip ?? ('Laporan ' . ($item->bulan ? 'Bulan ' . $item->bulan : '')),
                    'bulan' => $item->bulan,
                    'boks' => $item->nomor_boks
                ];
            }
        }
    }
    $exportYearsJson = json_encode(array_values($exportYears));
@endphp

<div x-data="{ 
    editUnitModalOpen: false, 
    importModalOpen: false,
    exportModalOpen: false,
    exportYearsData: {{ $exportYearsJson }},
    selectedReportIds: [],
    expandedYear: null,
    
    init() {
        this.selectedReportIds = [];
    },

    selectAllReports() {
        let ids = [];
        this.exportYearsData.forEach(yr => {
            yr.items.forEach(item => ids.push(item.id));
        });
        this.selectedReportIds = ids;
    },

    deselectAllReports() {
        this.selectedReportIds = [];
    },

    isYearChecked(yrObj) {
        if (!yrObj.items || yrObj.items.length === 0) return false;
        return yrObj.items.every(item => this.selectedReportIds.includes(item.id));
    },

    toggleYear(yrObj, checked) {
        let yrIds = yrObj.items.map(i => i.id);
        if (checked) {
            this.selectedReportIds = Array.from(new Set([...this.selectedReportIds, ...yrIds]));
        } else {
            this.selectedReportIds = this.selectedReportIds.filter(id => !yrIds.includes(id));
        }
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-md">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">
                {{ isset($currentUnit) ? 'Arsip Kantor: ' . $currentUnit->nama_unit : 'Daftar Arsip Pajak' }}
            </h2>
            <p class="text-body-md text-on-surface-variant mt-1">Langkah 2: gunakan tombol dan filter di halaman ini untuk mencari, menambah, membuka file, atau export arsip.</p>
        </div>
        <div class="flex flex-wrap items-center gap-1.5 shrink-0">
            <a href="{{ route('arsips.create') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-primary text-on-primary font-semibold text-xs hover:bg-primary/90 transition-all shadow-xs">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>Tambah Arsip Baru</span>
            </a>
            @if(isset($currentUnit))
                <button type="button" @@click="selectedReportIds = []; exportModalOpen = true" 
                        style="background-color: #059669; color: #ffffff;"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md font-semibold text-xs hover:opacity-90 transition-all shadow-xs">
                    <span class="material-symbols-outlined text-sm">download</span>
                    <span>Download Excel</span>
                </button>
                <button type="button" @@click="editUnitModalOpen = true" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-outline-variant text-on-surface-variant font-semibold text-xs hover:bg-surface-container transition-all shadow-xs" title="Edit Info UP/UPT {{ $currentUnit->nama_unit }}">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    <span>Edit Data Kantor</span>
                </button>
            @endif
            <a href="{{ route('arsips.pilih_unit') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-outline-variant text-on-surface-variant font-semibold text-xs hover:bg-surface-container transition-all shadow-xs" title="Ganti UP/UPT">
                <span class="material-symbols-outlined text-sm">swap_horiz</span>
                    <span>Pilih UP/UPT Lain</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-stack-md">
        <div class="rounded-xl border border-primary/30 bg-primary-fixed/20 p-4">
            <p class="font-bold text-on-surface text-sm flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">search</span>Cari Arsip</p>
            <p class="text-xs text-on-surface-variant mt-1">Ketik kata kunci lalu klik “Terapkan Filter”.</p>
        </div>
        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4">
            <p class="font-bold text-on-surface text-sm flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">attach_file</span>Upload File</p>
            <p class="text-xs text-on-surface-variant mt-1">Klik tombol “Upload File” pada baris arsip.</p>
        </div>
        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4">
            <p class="font-bold text-on-surface text-sm flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">visibility</span>Lihat Detail</p>
            <p class="text-xs text-on-surface-variant mt-1">Klik “Lihat” untuk membaca data lengkap.</p>
        </div>
        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4">
            <p class="font-bold text-on-surface text-sm flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">download</span>Download Laporan</p>
            <p class="text-xs text-on-surface-variant mt-1">Klik “Download Excel” untuk mencetak rekap.</p>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-3 sm:p-4 mb-stack-lg shadow-sm">
        <form id="filter-form" method="GET" action="{{ route('arsips.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-stack-md">
            <div class="lg:col-span-1">
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Pencarian</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline" style="font-size: 18px;">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full pl-9 pr-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md"
                           placeholder="Contoh: nama berkas, kode, nomor arsip">
                </div>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Jenis Pajak</label>
                <select name="jenis_pajak_id" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md text-on-surface">
                    <option value="">Semua</option>
                    @foreach ($jenisPajaks as $jp)
                        <option value="{{ $jp->id }}" @selected(request('jenis_pajak_id') == $jp->id)>{{ $jp->nama_jenis_pajak }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">UP/UPT</label>
                <select disabled class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container text-on-surface-variant cursor-not-allowed opacity-80 text-body-md font-medium">
                    @if(isset($currentUnit))
                        <option value="{{ $currentUnit->id }}" selected>{{ $currentUnit->nama_unit }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Kurun Waktu</label>
                <select name="kurun_waktu" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    @foreach ($tahuns as $tahun)
                        <option value="{{ $tahun }}" @selected(request('kurun_waktu') == $tahun)>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Bulan</label>
                <select name="bulan" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    @foreach(['1'=>'Januari', '2'=>'Februari', '3'=>'Maret', '4'=>'April', '5'=>'Mei', '6'=>'Juni', '7'=>'Juli', '8'=>'Agustus', '9'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'] as $num => $name)
                        <option value="{{ $num }}" @selected(request('bulan') == $num)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="inaktif" @selected(request('status') === 'inaktif')>Inaktif</option>
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Kondisi</label>
                <select name="kondisi" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    <option value="Baik" @selected(request('kondisi') === 'Baik')>Baik</option>
                    <option value="Rusak" @selected(request('kondisi') === 'Rusak')>Rusak</option>
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Klasifikasi Keamanan</label>
                <select name="klasifikasi_keamanan" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    <option value="Terbuka" @selected(request('klasifikasi_keamanan') === 'Terbuka')>Terbuka</option>
                    <option value="Terbatas" @selected(request('klasifikasi_keamanan') === 'Terbatas')>Terbatas</option>
                    <option value="Rahasia" @selected(request('klasifikasi_keamanan') === 'Rahasia')>Rahasia</option>
                </select>
            </div>
            <div class="sm:col-span-2 md:col-span-3 lg:col-span-5 flex flex-wrap items-center justify-start gap-3 pt-3 border-t border-outline-variant/60 mt-1">
                <button type="submit" class="flex items-center gap-1.5 px-5 py-2 rounded-lg bg-primary text-on-primary font-bold text-label-md hover:bg-primary/90 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-sm">filter_alt</span> Cari / Terapkan Filter
                </button>
                <a href="{{ route('arsips.index') }}" class="flex items-center gap-1 px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant text-label-md font-bold hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-sm">restart_alt</span> Reset
                </a>
            </div>
        </form>
    </div>

    @if ($groupedArsips->isEmpty())
         <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-6 sm:p-12 text-center shadow-sm">
            <span class="material-symbols-outlined text-4xl mb-2 text-outline">folder_off</span>
            <p class="text-body-md text-on-surface-variant">Belum ada data arsip untuk kantor ini.</p>
            <a href="{{ route('arsips.create') }}" class="text-primary font-bold hover:underline">Klik di sini untuk tambah arsip pertama</a>
        </div>
    @else
        <div class="space-y-stack-md" x-data="{ 
            openGroup: null,
            toggleGroup(key) {
                if (this.openGroup === key) {
                    this.openGroup = null;
                } else {
                    this.openGroup = key;
                }
            }
        }">
            @foreach ($groupedArsips as $groupKey => $items)
                @php
                    $firstItem = $items->first();
                    $unitName = $currentUnit ? $currentUnit->nama_unit : ($firstItem->unit?->nama_unit ?? 'Semua UP/UPT');
                    $tahunVal = $currentUnit ? $groupKey : ($firstItem->kurun_waktu ?? '2023');
                    
                    // Attach 1-based table row index to each item in $items
                    $itemsWithRowNo = $items->values()->map(function($item, $idx) {
                        $item->table_row_no = $idx + 1;
                        return $item;
                    });

                    // Group items by boks_id / nomor_boks to find all unique Boks & their item row number range
                    $boksGroups = $itemsWithRowNo->groupBy(fn($i) => $i->boks_id ?: ($i->nomor_boks ?: 'tanpa_boks'));
                    $boksInfoParts = [];

                    foreach ($boksGroups as $bId => $bItems) {
                        $bObj = $bItems->first()->boks;
                        $bNum = $bObj ? $bObj->nomor_boks : ($bItems->first()->nomor_boks ?? null);
                        if (!$bNum) continue;

                        // Selalu gunakan urutan nomor baris tabel (table_row_no) agar sinkron dengan kolom NO di tabel
                        $rowNos = $bItems->pluck('table_row_no')->sort()->values();
                        $minNo = $rowNos->first();
                        $maxNo = $rowNos->last();

                        $rangeStr = ($minNo === $maxNo) ? " (No. {$minNo})" : " (No. {$minNo}-{$maxNo})";
                        $rakInfo = ($bObj && $bObj->rak) ? " 📍 Rak {$bObj->rak->nomor_rak}" : "";
                        $boksInfoParts[] = "Boks {$bNum}{$rangeStr}{$rakInfo}";
                    }

                    $boksSummaryText = !empty($boksInfoParts) ? implode(', ', $boksInfoParts) : null;
                @endphp
                <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
                    {{-- Header Accordion per UP/UPT / Tahun --}}
                    <button @@click="toggleGroup('{{ $groupKey }}')"
                            class="w-full px-stack-md py-4 bg-surface hover:bg-surface-container/60 transition-colors flex items-center justify-between text-left">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-2xl transition-transform duration-200" :class="openGroup === '{{ $groupKey }}' ? 'rotate-90' : ''">chevron_right</span>
                            <div>
                                <h3 class="font-display-md text-title-md text-on-surface font-bold">
                                    📦 Arsip {{ $unitName }} — Tahun {{ $tahunVal }}
                                </h3>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                                    <span class="text-primary font-medium flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">touch_app</span>
                                        <span x-text="openGroup === '{{ $groupKey }}' ? 'Klik untuk menutup rincian' : 'Klik untuk membuka rincian (Januari - Desember)'"></span>
                                    </span>
                                    @if (!empty($boksInfoParts))
                                        <span class="text-outline hidden sm:inline">|</span>
                                        @foreach ($boksInfoParts as $bPart)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-surface-container text-on-surface font-semibold border border-outline-variant text-[11px] shadow-xs">
                                                <span>📦 {{ $bPart }}</span>
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-3 py-1.5 rounded-full bg-primary-fixed text-on-primary-fixed text-xs font-bold shadow-sm flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">unfold_more</span>
                                {{ $items->count() }} Berkas Bulanan
                            </span>
                        </div>
                    </button>

                    {{-- Tabel Isi Rincian Laporan Bulanan (Dropdown) --}}
                    <div x-show="openGroup === '{{ $groupKey }}'" x-collapse class="border-t border-outline-variant">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-surface-container/40 border-b border-outline-variant">
                                    <tr>
                                        <th class="py-2.5 px-3 font-table-header text-xs text-on-surface-variant text-center w-10">NO</th>
                                        <th class="py-2.5 px-3 font-table-header text-xs text-on-surface-variant whitespace-nowrap">KODE KLASIFIKASI</th>
                                        <th class="py-2.5 px-3 font-table-header text-xs text-on-surface-variant whitespace-nowrap">NO ARSIP/BERKAS</th>
                                        <th class="py-2.5 px-3 font-table-header text-xs text-on-surface-variant min-w-[300px]">URAIAN INFORMASI ARSIP</th>
                                        <th class="py-2.5 px-3 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">KURUN WAKTU</th>
                                        <th class="py-2.5 px-3 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">JUMLAH</th>
                                        <th class="py-2.5 px-3 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">TINGKAT PERKEMBANGAN</th>
                                        <th class="py-2.5 px-3 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">KONDISI ARSIP</th>
                                        <th class="py-2.5 px-3 font-table-header text-xs text-on-surface-variant text-center whitespace-nowrap">KLASIFIKASI KEAMANAN</th>
                                        <th class="py-2.5 px-3 font-table-header text-xs text-on-surface-variant text-right whitespace-nowrap">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/60">
                                    @foreach ($items as $idx => $arsip)
                                        <tr class="hover:bg-surface-container/30 transition-colors">
                                            <td class="py-3 px-3 text-body-sm text-on-surface-variant text-center font-medium">{{ $idx + 1 }}</td>
                                            <td class="py-3 px-3 text-body-sm text-on-surface whitespace-nowrap font-mono">{{ $arsip->kode_klasifikasi ?? '-' }}</td>
                                            <td class="py-3 px-3 text-body-sm text-on-surface-variant text-center whitespace-nowrap">{{ $arsip->nomor_arsip_berkas ?? '-' }}</td>
                                            <td class="py-2.5 px-3 text-body-sm text-on-surface leading-snug max-w-md">
                                                <p class="line-clamp-2" title="{{ $arsip->uraian_informasi_arsip }}">
                                                    {{ $arsip->uraian_informasi_arsip ?? '-' }}
                                                </p>
                                                @if ($arsip->jenisPajaks->isNotEmpty())
                                                    <div class="flex items-center gap-1 flex-wrap mt-1">
                                                        @foreach ($arsip->jenisPajaks as $jpItem)
                                                            <span class="px-2 py-0.5 rounded bg-primary-fixed/40 text-on-primary-fixed text-[10px] font-bold" title="{{ $jpItem->nama_jenis_pajak }}">
                                                                {{ $jpItem->kode }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3 text-body-sm text-on-surface-variant text-center font-semibold whitespace-nowrap">{{ $arsip->kurun_waktu ?? '-' }}</td>
                                            <td class="py-3 px-3 text-body-sm text-on-surface-variant text-center whitespace-nowrap">{{ $arsip->jumlah ?? '-' }} {{ $arsip->satuan ?? 'Berkas' }}</td>
                                            <td class="py-3 px-3 text-body-sm text-on-surface-variant text-center whitespace-nowrap">{{ $arsip->tingkat_perkembangan ?? 'Asli' }}</td>
                                            <td class="py-3 px-3 text-body-sm text-on-surface-variant text-center whitespace-nowrap">{{ $arsip->kondisi ?? 'Baik' }}</td>
                                            <td class="py-3 px-3 text-body-sm text-on-surface-variant text-center whitespace-nowrap">{{ $arsip->klasifikasi_keamanan ?? 'Terbuka' }}</td>
                                            <td class="py-3 px-3 text-right space-x-1 whitespace-nowrap">
                                                @php $fileCount = $arsip->files->count(); @endphp
                                                @if ($fileCount > 0 || $arsip->path_file)
                                                    <button type="button" 
                                                            onclick="openFilesModal({{ $arsip->id }}, {{ json_encode($arsip->files) }}, '{{ addslashes($arsip->uraian_informasi_arsip) }}', '{{ $arsip->file_url }}')"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-primary-fixed text-on-primary-fixed hover:bg-primary-fixed-dim transition-colors shadow-sm"
                                                            title="Lihat / Kelola {{ $fileCount ?: 1 }} Lampiran File">
                                                        <span class="material-symbols-outlined text-xs">attach_file</span>
                                                        <span>{{ $fileCount ?: 1 }} File</span>
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            onclick="openFilesModal({{ $arsip->id }}, [], '{{ addslashes($arsip->uraian_informasi_arsip) }}', null)"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-surface-container border border-primary/30 text-primary hover:bg-primary-fixed/30 transition-colors shadow-sm"
                                                            title="Upload File Lampiran ke Laporan Ini">
                                                        <span class="material-symbols-outlined text-xs">cloud_upload</span>
                                                        <span>+ Upload File</span>
                                                    </button>
                                                @endif
                                                <a href="{{ route('arsips.show', $arsip) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-secondary-fixed text-on-secondary-fixed hover:bg-secondary-fixed-dim transition-colors shadow-sm" title="Lihat Detail">
                                                    <span class="material-symbols-outlined text-xs">visibility</span>
                                                    <span>Lihat</span>
                                                </a>
                                                <a href="{{ route('arsips.edit', $arsip) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-primary-container text-on-primary hover:bg-primary-container/90 transition-colors shadow-sm" title="Edit Berkas">
                                                    <span class="material-symbols-outlined text-xs">edit</span>
                                                    <span>Edit</span>
                                                </a>
                                                <form action="{{ route('arsips.destroy', $arsip) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin memindahkan data arsip ini ke menu Arsip Terhapus?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-error-container text-on-error-container hover:bg-error-container/80 transition-colors shadow-sm" title="Pindahkan ke Arsip Terhapus">
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
                </div>
            @endforeach
        </div>
    @endif


    @if(isset($currentUnit))
    <div x-show="editUnitModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editUnitModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/50" aria-hidden="true" @@click="editUnitModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="editUnitModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-full px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-surface rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-headline-sm font-bold text-on-surface" id="modal-title">Edit UP/UPT</h3>
                    <button type="button" @@click="editUnitModalOpen = false" class="text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form action="{{ route('unit.update', $currentUnit) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="kode_unit" class="block text-label-md font-label-md text-on-surface-variant mb-1">Kode UP/UPT <span class="text-error">*</span></label>
                            <input type="text" name="kode_unit" id="kode_unit" value="{{ old('kode_unit', $currentUnit->kode_unit) }}" required class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('kode_unit') border-error @else border-outline-variant @enderror" placeholder="Contoh: UPT-001">
                            @error('kode_unit') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="nama_unit" class="block text-label-md font-label-md text-on-surface-variant mb-1">Nama UP/UPT <span class="text-error">*</span></label>
                            <input type="text" name="nama_unit" id="nama_unit" value="{{ old('nama_unit', $currentUnit->nama_unit) }}" required class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('nama_unit') border-error @else border-outline-variant @enderror" placeholder="Contoh: UPT Pekanbaru Kota">
                            @error('nama_unit') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="nomor_rak" class="block text-label-md font-label-md text-on-surface-variant mb-1">Nomor Rak <span class="text-on-surface-variant/60 font-normal">(opsional)</span></label>
                            <input type="text" name="nomor_rak" id="nomor_rak" value="{{ old('nomor_rak', $currentUnit->nomor_rak) }}" class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('nomor_rak') border-error @else border-outline-variant @enderror" placeholder="Contoh: Rak A1">
                            @error('nomor_rak') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @@click="editUnitModalOpen = false" class="px-4 py-2 text-label-md font-label-md text-on-surface hover:bg-surface-container rounded transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 text-label-md font-label-md bg-primary text-on-primary hover:bg-primary/90 rounded transition-colors">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Import Excel Modal --}}
    @if(isset($currentUnit))
    <div x-show="importModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="importModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/50" aria-hidden="true" @@click="importModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="importModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-full px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-surface rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-headline-sm font-bold text-on-surface" id="modal-title">Import Excel ke {{ $currentUnit->nama_unit }}</h3>
                    <button type="button" @@click="importModalOpen = false" class="text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form action="{{ route('arsips.import.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="unit_id" value="{{ $currentUnit->id }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-label-md font-label-md text-on-surface-variant mb-1">
                                File Excel <span class="text-error">*</span>
                            </label>
                            <input type="file" name="file" id="files_modal" required
                                   class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary-fixed file:text-on-primary-fixed file:text-label-md file:font-semibold cursor-pointer"
                                   onchange="uploadExcelModal(this)">
                            <p class="mt-2 text-label-md text-on-surface-variant">Format: .xlsx, .xls, .csv · Maksimal 20 MB</p>
                            <div id="import_modal_loading" class="mt-3 hidden">
                                <div class="flex items-center gap-2 p-3 rounded-lg bg-primary-fixed/20 border border-primary/30">
                                    <span class="material-symbols-outlined text-primary animate-spin text-sm">progress_activity</span>
                                    <span class="text-xs font-bold text-primary">Memproses file...</span>
                                </div>
                            </div>
                            <div id="import_modal_error" class="mt-3 hidden">
                                <p id="import_modal_error_text" class="text-sm text-error font-bold"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @@click="importModalOpen = false" class="px-4 py-2 text-label-md font-label-md text-on-surface hover:bg-surface-container rounded transition-colors">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script>
    async function uploadExcelModal(input) {
        const file = input.files?.[0];
        if (!file) return;

        document.getElementById('import_modal_loading').classList.remove('hidden');
        document.getElementById('import_modal_error').classList.add('hidden');

        // Jeda kecil untuk Android Google Drive
        await new Promise(r => setTimeout(r, 300));

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', csrfToken);

        try {
            const res = await fetch('{{ route('arsips.import.preview_ajax') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!res.ok) {
                let msg = 'Gagal memproses file (HTTP ' + res.status + ').';
                try { const errData = await res.json(); if (errData.error) msg = errData.error; } catch (e) {}
                throw new Error(msg);
            }

            const data = await res.json();

            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            } else {
                throw new Error(data.error || 'Format tidak dikenali.');
            }
        } catch (err) {
            document.getElementById('import_modal_loading').classList.add('hidden');
            document.getElementById('import_modal_error_text').innerText = err.message || 'Gagal upload.';
            document.getElementById('import_modal_error').classList.remove('hidden');
            showToast('error', err.message || 'Gagal upload.');
        }
    }
    </script>

    {{-- Modal Preview & Mass Upload Lampiran Files --}}
    <div id="filesModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="files-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/60" onclick="closeFilesModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block w-full max-w-full px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-surface rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:p-6">
                <div class="flex items-center justify-between pb-3 border-b border-outline-variant mb-4">
                    <div>
                        <h3 class="text-title-lg font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">folder_open</span>
                            Lampiran File Berkas Arsip
                        </h3>
                        <p id="filesModalTitle" class="text-xs text-on-surface-variant line-clamp-1 mt-0.5"></p>
                    </div>
                    <button type="button" onclick="closeFilesModal()" class="text-on-surface-variant hover:text-on-surface p-1 rounded-full hover:bg-surface-container">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Area Batch Mass Upload 100+ Files --}}
                <div class="mb-4 p-4 rounded-xl border-2 border-dashed border-primary/40 bg-surface-container-lowest hover:bg-primary-fixed-dim/10 transition-colors text-center relative">
                    <input type="file" id="modalBatchFileInput" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.heic,.heif,.doc,.docx,.xls,.xlsx"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                           onchange="prepareBatchUpload(this)">
                    <div class="flex flex-col items-center justify-center gap-1.5 pointer-events-none">
                        <span class="material-symbols-outlined text-3xl text-primary">cloud_upload</span>
                        <p class="text-sm font-bold text-on-surface">➕ Pilih Banyak File / Gambar Sekaligus</p>
                        <p class="text-xs text-on-surface-variant">Di HP: tahan/centang beberapa foto/file di galeri/file manager, lalu klik Pilih. Bisa pilih ulang untuk menambah daftar sebelum Upload Semua.</p>
                    </div>
                </div>

                {{-- Verifikasi file sebelum upload --}}
                <div id="batchVerifyContainer" class="mb-4 hidden p-3 rounded-xl border border-primary/30 bg-primary-fixed/10">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                        <div>
                            <p id="batchVerifyTitle" class="text-sm font-bold text-on-surface">Verifikasi File Upload</p>
                            <p class="text-xs text-on-surface-variant">Pastikan file yang dipilih sudah benar sebelum diupload.</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick="cancelBatchSelection()" class="px-3 py-1.5 rounded-lg border border-outline-variant text-xs font-bold text-on-surface-variant hover:bg-surface-container">
                                Batal
                            </button>
                            <button type="button" onclick="startBatchUpload()" class="px-3 py-1.5 rounded-lg bg-primary text-on-primary text-xs font-bold hover:bg-primary/90 shadow-sm">
                                Upload Semua
                            </button>
                        </div>
                    </div>
                    <div id="batchVerifyList" class="max-h-40 overflow-y-auto space-y-2"></div>
                </div>

                {{-- Progress Bar Baris Batch Upload --}}
                <div id="batchProgressContainer" class="mb-4 hidden p-3 rounded-lg bg-primary-fixed-dim/20 border border-primary/30">
                    <div class="flex items-center justify-between text-xs font-bold text-primary mb-1">
                        <span id="batchProgressStatus">Mengunggah file...</span>
                        <span id="batchProgressPercent">0%</span>
                    </div>
                    <div class="w-full bg-surface-container rounded-full h-2.5 overflow-hidden">
                        <div id="batchProgressBar" class="bg-primary h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <div id="filesModalContainer" class="max-h-[50vh] overflow-y-auto space-y-2.5 pr-1">
                    {{-- Dynamically populated via JS --}}
                </div>

                <div class="mt-6 flex justify-between items-center pt-3 border-t border-outline-variant">
                    <span id="filesCountFooter" class="text-xs text-on-surface-variant font-medium"></span>
                    <button type="button" onclick="closeFilesModal()" class="px-5 py-2 text-label-md font-bold bg-primary text-on-primary hover:bg-primary/90 rounded-lg transition-colors shadow-sm">
                        Selesai / Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentArsipId = null;
    let pendingBatchFiles = [];
    const arsipFilePreviewUrlTemplate = @json(route('arsip-files.preview', ['arsipFile' => '__ARSIP_FILE_ID__']));

    function openFilesModal(arsipId, files, uraian, legacyUrl) {
        currentArsipId = arsipId;
        pendingBatchFiles = [];
        document.getElementById('filesModalTitle').innerText = uraian || 'Detail Berkas Arsip';
        document.getElementById('batchProgressContainer').classList.add('hidden');
        document.getElementById('batchVerifyContainer').classList.add('hidden');
        document.getElementById('modalBatchFileInput').value = '';
        renderFileList(files, legacyUrl);
        document.getElementById('filesModal').classList.remove('hidden');
    }

    function renderFileList(files, legacyUrl) {
        const container = document.getElementById('filesModalContainer');
        container.innerHTML = '';
        const countFooter = document.getElementById('filesCountFooter');

        if (files && files.length > 0) {
            countFooter.innerText = `Total ${files.length} Lampiran Tersimpan`;
            files.forEach((f) => {
                const isImage = (f.tipe_file && f.tipe_file.includes('image')) || /\.(jpe?g|png|webp)$/i.test(f.nama_file);
                const fileUrl = f.id
                    ? arsipFilePreviewUrlTemplate.replace('__ARSIP_FILE_ID__', f.id)
                    : (f.url || ('/storage/' + f.path_file));

                const item = document.createElement('div');
                item.className = 'p-3 rounded-xl border border-outline-variant bg-surface-container-lowest hover:border-primary/50 transition-all flex items-center justify-between gap-3 shadow-sm';
                item.innerHTML = `
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-lg bg-primary-fixed/30 text-primary flex items-center justify-center shrink-0 font-bold">
                            <span class="material-symbols-outlined text-lg">${isImage ? 'image' : 'picture_as_pdf'}</span>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold text-on-surface truncate" title="${f.nama_file}">${f.nama_file}</p>
                            <p class="text-[11px] text-on-surface-variant">${(f.ukuran ? f.ukuran : (f.ukuran_file ? (f.ukuran_file/1024).toFixed(1) + ' KB' : 'Dokumen'))}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="${fileUrl}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-primary text-on-primary hover:bg-primary/90 transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-xs">visibility</span> Buka
                        </a>
                        <button type="button" onclick="deleteSingleFileModal(${f.id}, this)" class="inline-flex p-1 text-error hover:bg-error-container/40 rounded" title="Hapus File Ini">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </div>
                `;
                container.appendChild(item);
            });
        } else if (legacyUrl && legacyUrl !== 'null') {
            countFooter.innerText = '1 File Utama';
            const item = document.createElement('div');
            item.className = 'p-3 rounded-xl border border-outline-variant bg-surface-container-lowest flex items-center justify-between gap-3';
            item.innerHTML = `
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-xl">description</span>
                    <p class="text-xs font-bold text-on-surface">File Utama Arsip</p>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <a href="${legacyUrl}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-primary text-on-primary hover:bg-primary/90 transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-xs">visibility</span> Buka Dokumen
                    </a>
                    <button type="button" onclick="deleteLegacyFileModal(${currentArsipId}, this)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-error-container text-on-error-container hover:bg-error-container/80 transition-colors shadow-sm" title="Hapus File Utama Ini">
                        <span class="material-symbols-outlined text-xs">delete</span> Hapus
                    </button>
                </div>
            `;
            container.appendChild(item);
        } else {
            countFooter.innerText = 'Belum ada lampiran';
            container.innerHTML = '<p class="text-center py-6 text-xs text-on-surface-variant">Belum ada file lampiran. Gunakan tombol upload di atas untuk menambahkan 100+ PDF/Foto sekaligus.</p>';
        }
    }

    function prepareBatchUpload(input) {
        if (!input.files || input.files.length === 0 || !currentArsipId) return;

        pendingBatchFiles = pendingBatchFiles.concat(Array.from(input.files));

        const verifyContainer = document.getElementById('batchVerifyContainer');
        const verifyTitle = document.getElementById('batchVerifyTitle');
        const verifyList = document.getElementById('batchVerifyList');

        verifyTitle.innerText = `Verifikasi ${pendingBatchFiles.length} file sebelum upload`;
        verifyList.innerHTML = '';

        pendingBatchFiles.forEach((file, index) => {
            const isImage = /\.(jpe?g|png|webp)$/i.test(file.name);
            const sizeKb = (file.size / 1024).toFixed(1);

            const item = document.createElement('div');
            item.className = 'flex items-center justify-between gap-3 p-2 rounded-lg bg-surface-container-lowest border border-outline-variant';
            item.innerHTML = `
                <div class="flex items-center gap-2 min-w-0">
                    <span class="material-symbols-outlined text-primary text-lg">${isImage ? 'image' : 'description'}</span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-on-surface truncate" title="${file.name}">${index + 1}. ${file.name}</p>
                        <p class="text-[11px] text-on-surface-variant">${sizeKb} KB</p>
                    </div>
                </div>
                <button type="button" onclick="removePendingBatchFile(${index})" class="text-[11px] font-bold text-error shrink-0 hover:underline">Hapus</button>
            `;
            verifyList.appendChild(item);
        });

        document.getElementById('batchProgressContainer').classList.add('hidden');
        verifyContainer.classList.remove('hidden');
        input.value = '';
    }

    function removePendingBatchFile(index) {
        pendingBatchFiles.splice(index, 1);
        renderPendingBatchFiles();
    }

    function renderPendingBatchFiles() {
        const verifyContainer = document.getElementById('batchVerifyContainer');
        const verifyTitle = document.getElementById('batchVerifyTitle');
        const verifyList = document.getElementById('batchVerifyList');

        if (!pendingBatchFiles.length) {
            cancelBatchSelection();
            return;
        }

        verifyTitle.innerText = `Verifikasi ${pendingBatchFiles.length} file sebelum upload`;
        verifyList.innerHTML = '';

        pendingBatchFiles.forEach((file, index) => {
            const isImage = /\.(jpe?g|png|webp)$/i.test(file.name);
            const sizeKb = (file.size / 1024).toFixed(1);

            const item = document.createElement('div');
            item.className = 'flex items-center justify-between gap-3 p-2 rounded-lg bg-surface-container-lowest border border-outline-variant';
            item.innerHTML = `
                <div class="flex items-center gap-2 min-w-0">
                    <span class="material-symbols-outlined text-primary text-lg">${isImage ? 'image' : 'description'}</span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-on-surface truncate" title="${file.name}">${index + 1}. ${file.name}</p>
                        <p class="text-[11px] text-on-surface-variant">${sizeKb} KB</p>
                    </div>
                </div>
                <button type="button" onclick="removePendingBatchFile(${index})" class="text-[11px] font-bold text-error shrink-0 hover:underline">Hapus</button>
            `;
            verifyList.appendChild(item);
        });

        document.getElementById('batchProgressContainer').classList.add('hidden');
        verifyContainer.classList.remove('hidden');
    }

    function cancelBatchSelection() {
        pendingBatchFiles = [];
        document.getElementById('modalBatchFileInput').value = '';
        document.getElementById('batchVerifyContainer').classList.add('hidden');
    }

    async function startBatchUpload() {
        if (!pendingBatchFiles.length || !currentArsipId) return;

        const filesArr = [...pendingBatchFiles];
        const total = filesArr.length;
        
        const progressContainer = document.getElementById('batchProgressContainer');
        const progressStatus = document.getElementById('batchProgressStatus');
        const progressPercent = document.getElementById('batchProgressPercent');
        const progressBar = document.getElementById('batchProgressBar');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        document.getElementById('batchVerifyContainer').classList.add('hidden');
        progressContainer.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressPercent.innerText = '0%';
        progressStatus.innerText = `Memulai mengunggah ${total} file...`;

        let successCount = 0;
        let failCount = 0;
        let lastError = '';

        for (let i = 0; i < filesArr.length; i++) {
            const file = filesArr[i];
            const formData = new FormData();
            formData.append('file', file);

            try {
                progressStatus.innerText = `Mengunggah ${i + 1}/${total}: ${file.name}`;

                const res = await fetch(`/arsips/${currentArsipId}/upload-file`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok || data.success === false) {
                    failCount++;
                    lastError = data.message || data.error || (data.errors ? Object.values(data.errors).flat().join(' ') : '') || `Gagal upload ${file.name}`;
                } else {
                    successCount++;
                }
            } catch (err) {
                failCount++;
                lastError = err.message || `Gagal upload ${file.name}`;
                console.error('Error upload file:', err);
            }

            const percent = Math.round(((i + 1) / total) * 100);
            progressBar.style.width = `${percent}%`;
            progressPercent.innerText = `${percent}%`;
        }

        pendingBatchFiles = [];
        document.getElementById('modalBatchFileInput').value = '';

        if (successCount > 0) {
            progressStatus.innerText = `✅ Berhasil mengunggah ${successCount} dari ${total} file! Menyegarkan...`;
            if (failCount > 0 && typeof showToast === 'function') {
                showToast('warning', `${failCount} file gagal diunggah. ${lastError || ''}`);
            }
            setTimeout(() => {
                window.location.reload();
            }, 800);
        } else {
            progressStatus.innerText = `❌ Semua file gagal diunggah. ${lastError || ''}`;
            if (typeof showToast === 'function') {
                showToast('error', lastError || 'Upload gagal.');
            }
        }
    }

    async function deleteSingleFileModal(fileId, btnEl) {
        const ok = await window.showConfirm('Apakah Anda yakin ingin menghapus file lampiran ini?', 'Hapus File');
        if (!ok) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const res = await fetch(`/arsip-files/${fileId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (res.ok) {
                const itemEl = btnEl.closest('.p-3') || btnEl.closest('.shadow-sm') || btnEl.parentElement.parentElement;
                if (itemEl) itemEl.remove();
                if (typeof showToast === 'function') showToast('success', 'File lampiran berhasil dihapus.');
            }
        } catch (err) {
            console.error('Error delete file:', err);
        }
    }

    async function deleteLegacyFileModal(arsipId, btnEl) {
        const ok = await window.showConfirm('Apakah Anda yakin ingin menghapus file utama ini?', 'Hapus File Utama');
        if (!ok) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const res = await fetch(`/arsips/${arsipId}/legacy-file`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (res.ok) {
                const itemEl = btnEl.closest('.p-3') || btnEl.parentElement.parentElement;
                if (itemEl) itemEl.remove();
                if (typeof showToast === 'function') showToast('success', 'File utama berhasil dihapus.');
            }
        } catch (err) {
            console.error('Error delete legacy file:', err);
        }
    }

    function closeFilesModal() {
        pendingBatchFiles = [];
        document.getElementById('modalBatchFileInput').value = '';
        document.getElementById('batchVerifyContainer').classList.add('hidden');
        document.getElementById('filesModal').classList.add('hidden');
    }
    </script>

    {{-- Modal Minimalis Export Excel --}}
    <div x-show="exportModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="exportModalOpen" x-transition.opacity class="fixed inset-0 bg-black/50 transition-opacity" @@click="exportModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="exportModalOpen" x-transition.scale class="inline-block align-bottom bg-surface rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle border border-outline-variant w-full max-w-[620px] mx-auto" style="max-width: 620px;">
                <form method="GET" action="{{ route('laporan.export') }}" @@submit="exportModalOpen = false">
                    @if(isset($currentUnit)) <input type="hidden" name="unit_id" value="{{ $currentUnit->id }}"> @endif
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('kondisi')) <input type="hidden" name="kondisi" value="{{ request('kondisi') }}"> @endif
                    @if(request('klasifikasi_keamanan')) <input type="hidden" name="klasifikasi_keamanan" value="{{ request('klasifikasi_keamanan') }}"> @endif
                    <input type="hidden" name="selected_ids" :value="selectedReportIds.join(',')">
                    
                    <div class="p-5 sm:p-6">
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-outline-variant">
                            <div class="flex items-center gap-2 text-primary font-bold text-lg">
                                <span class="material-symbols-outlined text-2xl">download</span>
                                <h3>Pilih Laporan Export Excel</h3>
                            </div>
                            <button type="button" @@click="exportModalOpen = false" class="text-on-surface-variant/70 hover:text-on-surface p-1 rounded-full hover:bg-surface-container transition-colors">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between mb-4 text-xs">
                            <span class="text-on-surface-variant font-medium">Centang laporan yang ingin dimasukkan ke Excel:</span>
                            <div class="flex items-center gap-2 font-bold">
                                <button type="button" @@click="selectAllReports()" class="text-primary hover:underline">Pilih Semua</button>
                                <span class="text-outline/60">|</span>
                                <button type="button" @@click="deselectAllReports()" class="text-error hover:underline">Kosongkan</button>
                            </div>
                        </div>

                        {{-- List per Tahun --}}
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                            <template x-for="yr in exportYearsData" :key="yr.tahun">
                                <div class="border border-outline-variant/70 rounded-xl bg-surface-container-lowest overflow-hidden shadow-xs">
                                    {{-- Header Tahun --}}
                                    <div class="flex items-center justify-between p-3 bg-surface-container/30 hover:bg-surface-container/60 transition-colors">
                                        <label class="flex items-center gap-3.5 cursor-pointer font-bold text-sm text-on-surface select-none">
                                            <input type="checkbox" 
                                                   :checked="isYearChecked(yr)" 
                                                   @@change="toggleYear(yr, $event.target.checked)"
                                                   class="w-4 h-4 rounded text-primary border-outline-variant focus:ring-primary shrink-0 mr-1">
                                            <span class="flex items-center gap-1.5">
                                                <span>Tahun <span x-text="yr.tahun"></span></span>
                                                <span class="text-xs font-normal text-on-surface-variant/80" x-text="'(' + yr.items.length + ' Laporan)'"></span>
                                            </span>
                                        </label>

                                        <button type="button" 
                                                @@click="expandedYear = (expandedYear === yr.tahun ? null : yr.tahun)"
                                                class="flex items-center gap-1 px-2.5 py-1 rounded text-xs font-bold text-primary hover:bg-primary-fixed/20 transition-colors">
                                            <span x-text="expandedYear === yr.tahun ? 'Tutup Rincian' : 'Rincian Laporan'"></span>
                                            <span class="material-symbols-outlined text-sm" x-text="expandedYear === yr.tahun ? 'expand_less' : 'expand_more'"></span>
                                        </button>
                                    </div>

                                    {{-- Rincian Laporan --}}
                                    <div x-show="expandedYear === yr.tahun" x-collapse class="p-3 border-t border-outline-variant/60 bg-surface/50 space-y-2">
                                        <template x-for="item in yr.items" :key="item.id">
                                            <label class="flex items-start gap-3.5 p-2.5 rounded-lg border border-outline-variant/40 hover:bg-primary-fixed/10 cursor-pointer text-xs text-on-surface transition-colors select-none">
                                                <input type="checkbox" 
                                                       :value="item.id" 
                                                       x-model="selectedReportIds"
                                                       class="w-4 h-4 mt-0.5 rounded text-primary border-outline-variant focus:ring-primary shrink-0 mr-1">
                                                <div class="min-w-0 flex-1 leading-snug">
                                                    <p class="font-bold line-clamp-1" x-text="item.uraian"></p>
                                                    <p class="text-[11px] text-on-surface-variant/80 mt-0.5" x-text="'Boks: ' + (item.boks || '-') + (item.bulan ? ' | Bulan: ' + item.bulan : '')"></p>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest px-6 py-4 border-t border-outline-variant flex items-center justify-between text-xs rounded-b-2xl">
                        <div class="font-bold text-on-surface text-xs">
                            Total: <span class="text-primary font-extrabold text-sm" x-text="selectedReportIds.length"></span> Laporan Terpilih
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @@click="exportModalOpen = false" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant font-bold hover:bg-surface-container transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                    :disabled="selectedReportIds.length === 0"
                                    class="px-4 py-2 rounded-lg bg-primary text-on-primary font-bold hover:bg-primary/90 shadow-sm flex items-center gap-1.5 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="material-symbols-outlined text-base">download</span>
                                <span>Download Excel</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection