@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Laporan Arsip</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Rekapitulasi dan ekspor data arsip pajak daerah.</p>
        </div>
        <a href="{{ route('laporan.export', request()->query()) }}"
           class="flex items-center gap-2 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary-container/90 transition-colors shadow-sm">
            <span class="material-symbols-outlined" style="font-size: 18px;">download</span>
            Export Excel
        </a>
    </div>

    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md mb-stack-lg shadow-sm">
        <form method="GET" action="{{ route('laporan') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-stack-md">
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
            <div class="flex items-end gap-2">
                <a href="{{ route('laporan') }}" class="px-4 py-2 rounded border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container">Reset</a>
                <button type="submit" class="px-4 py-2 rounded bg-primary-container text-on-primary text-label-md hover:bg-primary-container/90">Terapkan</button>
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-stack-lg mb-stack-lg">
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
            <div class="px-stack-md py-stack-md border-b border-outline-variant">
                <h3 class="text-headline-sm font-bold text-on-surface">Per Unit/UPT (Top 15)</h3>
            </div>
            <div class="overflow-x-auto max-h-80">
                <table class="w-full text-left">
                    <thead class="bg-surface border-b border-outline-variant sticky top-0">
                        <tr>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant">Unit</th>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant text-right">Row</th>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant text-right">Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse ($rekapUnit as $row)
                            <tr>
                                <td class="py-2 px-4 text-body-md text-on-surface">{{ $row->nama_unit }}</td>
                                <td class="py-2 px-4 text-body-md text-on-surface font-medium text-right">{{ number_format($row->total) }}</td>
                                <td class="py-2 px-4 text-body-md text-on-surface font-medium text-right">{{ number_format($row->total_berkas) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 px-4 text-center text-on-surface-variant">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
            <div class="px-stack-md py-stack-md border-b border-outline-variant">
                <h3 class="text-headline-sm font-bold text-on-surface">Per Kurun Waktu</h3>
            </div>
            <div class="overflow-x-auto max-h-80">
                <table class="w-full text-left">
                    <thead class="bg-surface border-b border-outline-variant sticky top-0">
                        <tr>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant">Tahun</th>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant text-right">Row</th>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant text-right">Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse ($rekapTahun as $row)
                            <tr>
                                <td class="py-2 px-4 text-body-md text-on-surface">{{ $row->kurun_waktu }}</td>
                                <td class="py-2 px-4 text-body-md text-on-surface font-medium text-right">{{ number_format($row->total) }}</td>
                                <td class="py-2 px-4 text-body-md text-on-surface font-medium text-right">{{ number_format($row->total_berkas) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 px-4 text-center text-on-surface-variant">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
            <div class="px-stack-md py-stack-md border-b border-outline-variant">
                <h3 class="text-headline-sm font-bold text-on-surface">Per Tipe Arsip</h3>
            </div>
            <div class="overflow-x-auto max-h-80">
                <table class="w-full text-left">
                    <thead class="bg-surface border-b border-outline-variant sticky top-0">
                        <tr>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant">Tipe</th>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant text-right">Row</th>
                            <th class="py-2 px-4 font-table-header text-table-header text-on-surface-variant text-right">Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse ($rekapTipe as $row)
                            <tr>
                                <td class="py-2 px-4 text-body-md text-on-surface capitalize">{{ $row->tipe_arsip }}</td>
                                <td class="py-2 px-4 text-body-md text-on-surface font-medium text-right">{{ number_format($row->total) }}</td>
                                <td class="py-2 px-4 text-body-md text-on-surface font-medium text-right">{{ number_format($row->total_berkas) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 px-4 text-center text-on-surface-variant">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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
@endsection