@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <div x-data="{ exportModalOpen: false }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-md">
            <div>
                <h2 class="font-display-md text-display-md text-on-surface">Laporan Arsip</h2>
                <p class="text-body-md text-on-surface-variant mt-1">Rekapitulasi data arsip pajak daerah.</p>
            </div>
        </div>



    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md mb-stack-lg shadow-sm">
        <form id="filter-form" method="GET" action="{{ route('laporan') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-stack-md">
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Jenis Pajak</label>
                <select name="jenis_pajak_id" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    @foreach ($jenisPajaks as $jp)
                        <option value="{{ $jp->id }}" @selected(($filters['jenis_pajak_id'] ?? '') == $jp->id)>{{ $jp->nama_jenis_pajak }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Unit/UPT/UP</label>
                <select name="unit_id" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" @selected(($filters['unit_id'] ?? '') == $unit->id)>{{ $unit->nama_unit }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Tipe</label>
                <select name="tipe_arsip" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    <option value="rekap" @selected(($filters['tipe_arsip'] ?? '') === 'rekap')>Rekap</option>
                    <option value="detail" @selected(($filters['tipe_arsip'] ?? '') === 'detail')>Detail</option>
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    <option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif</option>
                    <option value="inaktif" @selected(($filters['status'] ?? '') === 'inaktif')>Inaktif</option>
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Kurun Waktu</label>
                <select name="kurun_waktu" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    @foreach ($tahuns as $tahun)
                        <option value="{{ $tahun }}" @selected(($filters['kurun_waktu'] ?? '') == $tahun)>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Bulan</label>
                <select name="bulan" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    @foreach(['1'=>'Januari', '2'=>'Februari', '3'=>'Maret', '4'=>'April', '5'=>'Mei', '6'=>'Juni', '7'=>'Juli', '8'=>'Agustus', '9'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'] as $num => $name)
                        <option value="{{ $num }}" @selected(($filters['bulan'] ?? '') == $num)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Kondisi</label>
                <select name="kondisi" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    <option value="Baik" @selected(($filters['kondisi'] ?? '') === 'Baik')>Baik</option>
                    <option value="Rusak" @selected(($filters['kondisi'] ?? '') === 'Rusak')>Rusak</option>
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Klasifikasi Keamanan</label>
                <select name="klasifikasi_keamanan" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua</option>
                    <option value="Terbuka" @selected(($filters['klasifikasi_keamanan'] ?? '') === 'Terbuka')>Terbuka</option>
                    <option value="Terbatas" @selected(($filters['klasifikasi_keamanan'] ?? '') === 'Terbatas')>Terbatas</option>
                    <option value="Rahasia" @selected(($filters['klasifikasi_keamanan'] ?? '') === 'Rahasia')>Rahasia</option>
                </select>
            </div>
            <div class="flex items-end gap-2 md:col-span-2">
                <a href="{{ route('laporan') }}" class="px-4 py-2 rounded border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container">Reset</a>
                <button type="submit" class="px-4 py-2 rounded bg-primary text-on-primary font-bold text-label-md hover:bg-primary/90">Terapkan Filter</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-stack-md mb-stack-lg">
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Arsip Aktif (filter)</p>
            <p class="text-display-md text-primary mt-1">{{ number_format($rekapStatus['aktif']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Arsip Inaktif (filter)</p>
            <p class="text-display-md text-on-surface mt-1">{{ number_format($rekapStatus['inaktif']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-stack-lg mb-stack-lg">
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
            <div class="px-stack-md py-stack-md border-b border-outline-variant">
                <h3 class="text-headline-sm font-bold text-on-surface">Per UP/UPT (Paling Banyak Berkas)</h3>
            </div>
            <div class="overflow-x-auto max-h-80">
                <table class="w-full text-left">
                    <thead class="bg-surface border-b border-outline-variant sticky top-0">
                        <tr>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant">Unit / UPT</th>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant text-right">Jumlah Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse ($rekapUnit as $row)
                            <tr class="hover:bg-surface-container/30 transition-colors">
                                <td class="py-2 px-4 text-body-md text-on-surface font-medium">{{ $row->nama_unit }}</td>
                                <td class="py-2 px-4 text-body-md text-primary font-bold text-right">{{ number_format($row->total_berkas) }} berkas</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-6 px-4 text-center text-on-surface-variant">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
            <div class="px-stack-md py-stack-md border-b border-outline-variant">
                <h3 class="text-headline-sm font-bold text-on-surface">Per Kurun Waktu (Paling Banyak Berkas)</h3>
            </div>
            <div class="overflow-x-auto max-h-80">
                <table class="w-full text-left">
                    <thead class="bg-surface border-b border-outline-variant sticky top-0">
                        <tr>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant">Tahun / Kurun Waktu</th>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant text-right">Jumlah Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse ($rekapTahun as $row)
                            <tr class="hover:bg-surface-container/30 transition-colors">
                                <td class="py-2 px-4 text-body-md text-on-surface font-medium">{{ $row->kurun_waktu ?? '-' }}</td>
                                <td class="py-2 px-4 text-body-md text-primary font-bold text-right">{{ number_format($row->total_berkas) }} berkas</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-6 px-4 text-center text-on-surface-variant">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TABEL REKAP ARSIP PER UNIT (FORMAT EXCEL BAPENDA) --}}
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm mb-stack-lg">
        <div class="px-stack-md py-4 bg-surface-container/40 border-b border-outline-variant flex flex-col md:flex-row md:items-center justify-between gap-3">
            <h3 class="text-headline-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">table_chart</span>
                Rekapitulasi Pemindahan Arsip Per Unit (Format Rekap Excel Bapenda)
            </h3>
            <div class="flex items-center gap-3">
                <span class="text-xs text-on-surface-variant font-medium">Total: {{ $rekapArsipUnits->total() }} Unit Terdaftar</span>
                <button type="button" @click="exportModalOpen = true"
                        style="background-color: #059669; color: #ffffff;"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-bold text-xs hover:opacity-90 transition-all shadow-md">
                    <span class="material-symbols-outlined text-sm">download</span>
                    <span>Unduh Excel Rekap Ini</span>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-center w-12">No</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Nama Unit & Rincian Boks</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-center">Jumlah Berkas</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-center">Kurun Waktu</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-center">No. Boks</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-center">Lokasi Rak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant text-xs">
                    @forelse ($rekapArsipUnits as $index => $r)
                        <tr class="hover:bg-surface-container/50 transition-colors">
                            <td class="py-3 px-4 text-center font-bold text-on-surface-variant">{{ ($rekapArsipUnits->currentPage() - 1) * $rekapArsipUnits->perPage() + $index + 1 }}</td>
                            <td class="py-3 px-4 font-semibold text-on-surface">
                                <a href="{{ route('laporan', array_merge(request()->query(), ['unit_id' => $r->unit->id])) }}" class="text-primary hover:underline font-bold" title="Klik untuk memfilter laporan khusus unit ini">
                                    {{ $r->unit->nama_unit }}
                                </a>
                                @if($r->rincian_boks)
                                    <span class="font-normal text-on-surface-variant text-[11px] block mt-0.5">( {{ $r->rincian_boks }} )</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-primary">{{ number_format($r->total_berkas) }} Berkas</td>
                            <td class="py-3 px-4 text-center text-on-surface-variant">{{ $r->kurun_waktu ?: '-' }}</td>
                            <td class="py-3 px-4 text-center text-on-surface-variant font-medium">{{ $r->nomor_boks }}</td>
                            <td class="py-3 px-4 text-center text-on-surface-variant">{{ $r->lokasi_rak }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-on-surface-variant">Belum ada data rekap arsip.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rekapArsipUnits->hasPages())
            <div class="bg-surface px-4 py-3 border-t border-outline-variant">
                {{ $rekapArsipUnits->links() }}
            </div>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
        <div class="px-stack-md py-stack-md border-b border-outline-variant">
            <h3 class="text-headline-sm font-bold text-on-surface">Detail Data Arsip</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Kode</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Uraian</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Kurun</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Jml</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Boks</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Unit</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Tipe</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($arsips as $arsip)
                        <tr class="hover:bg-surface-container/50">
                            <td class="py-3 px-4">
                                <a href="{{ route('arsips.show', $arsip) }}" class="text-primary font-medium hover:underline">{{ $arsip->kode_klasifikasi ?? '-' }}</a>
                            </td>
                            <td class="py-3 px-4 text-body-md text-on-surface max-w-xs truncate">{{ $arsip->uraian_informasi_arsip ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $arsip->kurun_waktu ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $arsip->jumlah ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $arsip->nomor_boks ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $arsip->unit->nama_unit ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if ($arsip->tipe_arsip === 'rekap')
                                    <span class="inline-flex px-2 py-1 rounded text-[11px] font-bold uppercase bg-tertiary-fixed text-on-tertiary-fixed">Rekap</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-[11px] font-bold uppercase bg-secondary-fixed text-on-secondary-fixed">Detail</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if ($arsip->status === 'aktif')
                                    <span class="inline-flex px-2 py-1 rounded text-[11px] font-bold uppercase bg-primary-fixed text-on-primary-fixed">Aktif</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-[11px] font-bold uppercase bg-surface-container-highest text-on-surface-variant">Inaktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 px-4 text-center text-on-surface-variant">Tidak ada data sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($arsips->hasPages())
            <div class="bg-surface px-4 py-3 border-t border-outline-variant">
                {{ $arsips->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL OPSI EXPORT EXCEL --}}
    <div x-show="exportModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="exportModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/50" aria-hidden="true" @click="exportModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="exportModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-surface rounded-2xl shadow-2xl border border-outline-variant">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-outline-variant">
                    <h3 class="text-headline-sm font-bold text-on-surface flex items-center gap-2" id="modal-title">
                        <span class="material-symbols-outlined text-primary">download</span>
                        Pilih Opsi Export Excel
                    </h3>
                    <button type="button" @click="exportModalOpen = false" class="text-on-surface-variant hover:text-on-surface p-1 rounded-lg hover:bg-surface-container">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form method="GET" action="{{ route('laporan.export') }}" @submit="exportModalOpen = false">
                    @if(!empty($filters['jenis_pajak_id'])) <input type="hidden" name="jenis_pajak_id" value="{{ $filters['jenis_pajak_id'] }}"> @endif
                    @if(!empty($filters['unit_id'])) <input type="hidden" name="unit_id" value="{{ $filters['unit_id'] }}"> @endif
                    @if(!empty($filters['kurun_waktu'])) <input type="hidden" name="kurun_waktu" value="{{ $filters['kurun_waktu'] }}"> @endif
                    @if(!empty($filters['bulan'])) <input type="hidden" name="bulan" value="{{ $filters['bulan'] }}"> @endif
                    @if(!empty($filters['tipe_arsip'])) <input type="hidden" name="tipe_arsip" value="{{ $filters['tipe_arsip'] }}"> @endif
                    @if(!empty($filters['kondisi'])) <input type="hidden" name="kondisi" value="{{ $filters['kondisi'] }}"> @endif
                    @if(!empty($filters['klasifikasi_keamanan'])) <input type="hidden" name="klasifikasi_keamanan" value="{{ $filters['klasifikasi_keamanan'] }}"> @endif

                    <div class="space-y-4">
                        <div>
                            <label class="block text-label-md font-bold text-on-surface mb-1.5">Pilih Status Arsip <span class="text-error">*</span></label>
                            <select name="status" class="w-full px-3 py-2.5 border border-outline-variant rounded-lg bg-surface focus:outline-none focus:border-primary text-body-md font-semibold text-on-surface shadow-xs">
                                <option value="inaktif" @selected(($filters['status'] ?? '') === 'inaktif' || empty($filters['status']))>Arsip Inaktif</option>
                                <option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Arsip Aktif</option>
                                <option value="" @selected(($filters['status'] ?? '') === '')>Semua Status</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 pt-3 border-t border-outline-variant flex justify-end gap-2">
                        <button type="button" @click="exportModalOpen = false" class="px-4 py-2 border border-outline-variant rounded-lg text-on-surface-variant text-xs font-bold hover:bg-surface-container transition-colors">
                            Batal
                        </button>
                        <button type="submit" style="background-color: #059669; color: #ffffff;" class="px-4 py-2 rounded-lg font-bold text-xs hover:opacity-90 transition-all shadow-md flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">download</span>
                            <span>Download File Excel</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection