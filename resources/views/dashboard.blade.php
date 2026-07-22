@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Beranda</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Ringkasan arsip digital pajak daerah Bapenda Provinsi Riau.</p>
        </div>
        <div class="flex items-center gap-stack-sm">
            <a href="{{ route('arsips.import') }}" class="flex items-center gap-2 px-4 py-2 rounded border border-primary-container text-primary-container font-label-md text-label-md hover:bg-primary-container/10 transition-colors bg-surface-container-lowest">
                <span class="material-symbols-outlined" style="font-size: 18px;">upload_file</span>
                Import Excel
            </a>
            <a href="{{ route('arsips.create') }}" class="flex items-center gap-2 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary-container/90 transition-colors shadow-sm">
                <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                Tambah Arsip
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-stack-md mb-stack-lg">
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-label-md text-on-surface-variant">Total Arsip</p>
                <span class="material-symbols-outlined text-primary">inventory_2</span>
            </div>
            <p class="text-display-md text-on-surface mt-2">{{ number_format($stats['total_arsip']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-label-md text-on-surface-variant">Arsip Aktif</p>
                <span class="material-symbols-outlined text-primary">check_circle</span>
            </div>
            <p class="text-display-md text-primary mt-2">{{ number_format($stats['arsip_aktif']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-label-md text-on-surface-variant">Arsip Inaktif</p>
                <span class="material-symbols-outlined text-on-surface-variant">cancel</span>
            </div>
            <p class="text-display-md text-on-surface mt-2">{{ number_format($stats['arsip_inaktif']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-label-md text-on-surface-variant">Total Unit/UPT</p>
                <span class="material-symbols-outlined text-primary">apartment</span>
            </div>
            <p class="text-display-md text-on-surface mt-2">{{ number_format($stats['total_unit']) }}</p>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
        <div class="flex items-center justify-between px-stack-md py-stack-md border-b border-outline-variant">
            <h3 class="text-headline-sm font-bold text-on-surface">Arsip Terbaru</h3>
            <a href="{{ route('arsips.index') }}" class="text-primary text-label-md hover:underline">Lihat semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Nomor Arsip</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Nama WP</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Jenis</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Unit</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($arsipTerbaru as $arsip)
                        <tr class="hover:bg-surface-container/50 transition-colors">
                            <td class="py-3 px-4">
                                <a href="{{ route('arsips.show', $arsip) }}" class="text-body-md font-medium text-primary hover:underline">{{ $arsip->nomor_arsip }}</a>
                            </td>
                            <td class="py-3 px-4 text-body-md text-on-surface">{{ $arsip->nama_wajib_pajak }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $arsip->jenisPajak->kode ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $arsip->unit->nama_unit ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if ($arsip->status === 'aktif')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-bold tracking-wide uppercase bg-primary-fixed text-on-primary-fixed">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-bold tracking-wide uppercase bg-surface-container-highest text-on-surface-variant">Inaktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 px-4 text-center text-on-surface-variant">
                                Belum ada data arsip.
                                <a href="{{ route('arsips.create') }}" class="text-primary hover:underline">Tambah arsip</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
