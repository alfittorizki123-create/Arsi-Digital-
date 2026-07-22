@extends('layouts.app')

@section('title', 'Daftar Arsip Pajak')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Daftar Arsip Pajak</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Kelola dan telusuri dokumen arsip perpajakan daerah.</p>
        </div>
        <div class="flex items-center gap-stack-sm">
            <a href="#" class="flex items-center gap-2 px-4 py-2 rounded border border-primary-container text-primary-container font-label-md text-label-md hover:bg-primary-container/10 transition-colors bg-surface-container-lowest">
                <span class="material-symbols-outlined" style="font-size: 18px;">upload_file</span>
                Import Excel
            </a>
            <a href="{{ route('arsips.create') }}" class="flex items-center gap-2 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary-container/90 transition-colors shadow-sm">
                <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                Tambah Arsip
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-stack-md px-4 py-3 rounded-lg bg-primary-fixed text-on-primary-fixed border border-primary-fixed-dim">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md mb-stack-lg shadow-sm">
        <form method="GET" action="{{ route('arsips.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-stack-md">
            <div class="lg:col-span-1">
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Pencarian</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline" style="font-size: 18px;">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full pl-9 pr-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md"
                           placeholder="No. Arsip / Nama WP">
                </div>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Jenis Pajak</label>
                <select name="jenis_pajak_id" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md text-on-surface">
                    <option value="">Semua Jenis Pajak</option>
                    @foreach ($jenisPajaks as $jp)
                        <option value="{{ $jp->id }}" @selected(request('jenis_pajak_id') == $jp->id)>{{ $jp->nama_jenis_pajak }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Unit/UPT</label>
                <select name="unit_id" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md text-on-surface">
                    <option value="">Semua Unit/UPT</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" @selected(request('unit_id') == $unit->id)>{{ $unit->nama_unit }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md text-on-surface">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="inaktif" @selected(request('status') === 'inaktif')>Inaktif</option>
                </select>
            </div>
            <div>
                <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Tahun</label>
                <select name="tahun" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface focus:outline-none focus:border-primary text-body-md text-on-surface">
                    <option value="">Semua Tahun</option>
                    @foreach ($tahuns as $tahun)
                        <option value="{{ $tahun }}" @selected(request('tahun') == $tahun)>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-5 flex gap-2 justify-end">
                <a href="{{ route('arsips.index') }}" class="px-4 py-2 rounded border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container transition-colors">Reset</a>
                <button type="submit" class="px-4 py-2 rounded bg-primary-container text-on-primary text-label-md hover:bg-primary-container/90 transition-colors">Terapkan Filter</button>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant whitespace-nowrap">Nomor Arsip</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant whitespace-nowrap">Jenis Pajak</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant min-w-[200px]">Nama Wajib Pajak</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant whitespace-nowrap">Tahun</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant whitespace-nowrap">Nomor Rak</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant whitespace-nowrap">Unit/UPT</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant bg-surface-container-lowest">
                    @forelse ($arsips as $arsip)
                        <tr class="hover:bg-surface-container/50 transition-colors">
                            <td class="py-4 px-4 font-body-md text-body-md text-on-surface font-medium">{{ $arsip->nomor_arsip }}</td>
                            <td class="py-4 px-4 font-body-md text-body-md text-on-surface-variant">{{ $arsip->jenisPajak->kode ?? '-' }}</td>
                            <td class="py-4 px-4 font-body-md text-body-md text-on-surface">{{ $arsip->nama_wajib_pajak }}</td>
                            <td class="py-4 px-4 font-body-md text-body-md text-on-surface-variant">{{ $arsip->tahun_arsip }}</td>
                            <td class="py-4 px-4 font-body-md text-body-md text-on-surface-variant">{{ $arsip->nomor_rak ?? '-' }}</td>
                            <td class="py-4 px-4 font-body-md text-body-md text-on-surface-variant">{{ $arsip->unit->nama_unit ?? '-' }}</td>
                            <td class="py-4 px-4">
                                @if ($arsip->status === 'aktif')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-bold tracking-wide uppercase bg-primary-fixed text-on-primary-fixed">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-bold tracking-wide uppercase bg-surface-container-highest text-on-surface-variant">Inaktif</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right space-x-1">
                                <a href="{{ route('arsips.show', $arsip) }}" class="inline-flex p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-fixed/30 rounded transition-colors" title="Lihat Detail">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                                </a>
                                <a href="{{ route('arsips.edit', $arsip) }}" class="inline-flex p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-fixed/30 rounded transition-colors" title="Edit">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                </a>
                                <form action="{{ route('arsips.destroy', $arsip) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus arsip {{ $arsip->nomor_arsip }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/50 rounded transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 px-4 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl mb-2 block">folder_off</span>
                                Belum ada data arsip.
                                <a href="{{ route('arsips.create') }}" class="text-primary font-medium hover:underline">Tambah arsip pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($arsips->hasPages())
            <div class="bg-surface px-4 py-3 border-t border-outline-variant">
                {{ $arsips->links() }}
            </div>
        @elseif ($arsips->total() > 0)
            <div class="bg-surface px-4 py-3 border-t border-outline-variant">
                <p class="text-body-md text-on-surface-variant">
                    Menampilkan <span class="font-medium text-on-surface">{{ $arsips->firstItem() }}</span>
                    hingga <span class="font-medium text-on-surface">{{ $arsips->lastItem() }}</span>
                    dari <span class="font-medium text-on-surface">{{ $arsips->total() }}</span> hasil
                </p>
            </div>
        @endif
    </div>
@endsection
