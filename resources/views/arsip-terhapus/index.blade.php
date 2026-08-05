@extends('layouts.app')

@section('title', 'Arsip Terhapus')

@section('content')
<div>
    {{-- Header --}}
    <div class="mb-stack-lg">
        <h2 class="font-display-md text-2xl sm:text-display-md text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-error text-2xl sm:text-3xl">delete_sweep</span>
            <span>Arsip Terhapus</span>
        </h2>
        <p class="text-xs sm:text-body-md text-on-surface-variant mt-1">Daftar arsip terhapus sementara. Anda dapat memulihkan kembali (restore) atau menghapus permanen.</p>
    </div>

    {{-- Info Box --}}
    <div class="bg-primary-fixed/20 border border-primary/30 rounded-xl p-3 sm:p-stack-md mb-stack-md flex items-start gap-3">
        <span class="material-symbols-outlined text-primary text-xl shrink-0 mt-0.5">info</span>
        <div>
            <p class="text-xs font-bold text-on-surface">Informasi Pemulihan Arsip</p>
            <p class="text-[11px] sm:text-xs text-on-surface-variant mt-0.5">Data yang dihapus tersimpan aman di menu Arsip Terhapus ini. Klik <strong>"Pulihkan"</strong> untuk mengembalikan data arsip ke daftar utama.</p>
        </div>
    </div>

    {{-- Minimalist Search & Filter Form --}}
    <div class="mb-stack-md">
        <form method="GET" action="{{ route('arsips.trash') }}" class="flex flex-col sm:flex-row items-center gap-2">
            <div class="relative flex-1 w-full">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline" style="font-size: 18px;">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full pl-9 pr-3.5 py-2 border border-outline-variant rounded-xl bg-surface-container-lowest focus:outline-none focus:border-primary text-xs font-medium shadow-xs"
                       placeholder="Cari Uraian Arsip, Kode, atau Nomor Boks...">
            </div>

            <div class="w-full sm:w-auto shrink-0">
                <select name="unit_id" onchange="this.form.submit()" class="w-full sm:w-56 px-3 py-2 border border-outline-variant rounded-xl bg-surface-container-lowest text-xs focus:outline-none focus:border-primary font-medium shadow-xs">
                    <option value="">Semua Kantor UP/UPT</option>
                    @foreach ($units as $u)
                        <option value="{{ $u->id }}" @selected(request('unit_id') == $u->id)>{{ $u->nama_unit }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-1.5 w-full sm:w-auto shrink-0">
                <button type="submit" class="flex-1 sm:flex-initial px-4 py-2 bg-primary text-on-primary rounded-xl font-bold text-xs hover:bg-primary/90 transition-colors shadow-xs">Cari</button>
                @if(request('search') || request('unit_id'))
                    <a href="{{ route('arsips.trash') }}" class="px-3.5 py-2 border border-outline-variant text-on-surface-variant rounded-xl font-bold text-xs hover:bg-surface-container transition-colors shadow-xs">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABEL ARSIP TERHAPUS (RESPONSIF UNTUK SEMUA UKURAN LAYAR) --}}
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs min-w-[700px]">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-bold text-on-surface-variant">Uraian Informasi Arsip</th>
                        <th class="py-3 px-4 font-bold text-on-surface-variant">Kantor UP/UPT</th>
                        <th class="py-3 px-4 font-bold text-on-surface-variant text-center">Kurun</th>
                        <th class="py-3 px-4 font-bold text-on-surface-variant text-center">Boks</th>
                        <th class="py-3 px-4 font-bold text-on-surface-variant">Dihapus Pada</th>
                        <th class="py-3 px-4 font-bold text-on-surface-variant text-right">Aksi Pemilihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($arsips as $arsip)
                        <tr class="hover:bg-surface-container/40 transition-colors">
                            <td class="py-3 px-4">
                                <p class="font-bold text-on-surface text-sm line-clamp-2">{{ $arsip->uraian_informasi_arsip }}</p>
                                <p class="text-[11px] text-on-surface-variant mt-0.5">Kode: <span class="font-mono font-bold">{{ $arsip->kode_klasifikasi ?? '-' }}</span></p>
                            </td>
                            <td class="py-3 px-4 text-on-surface-variant font-medium whitespace-nowrap">
                                {{ $arsip->unit->nama_unit ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-on-surface whitespace-nowrap">
                                {{ $arsip->kurun_waktu ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-primary-fixed/40 text-primary">
                                    Boks {{ $arsip->nomor_boks ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-on-surface-variant whitespace-nowrap font-mono text-[11px]">
                                {{ $arsip->deleted_at ? $arsip->deleted_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Lihat Detail --}}
                                    <a href="{{ route('arsips.show', ['arsip' => $arsip->id, 'from' => 'trash']) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-primary text-on-primary hover:bg-primary/90 transition-colors shadow-sm"
                                       title="Lihat Detail Lengkap Arsip Ini">
                                        <span class="material-symbols-outlined text-xs">visibility</span>
                                        <span>Lihat</span>
                                    </a>

                                    {{-- Pulihkan (Restore) --}}
                                    <form action="{{ route('arsips.restore', $arsip->id) }}" method="POST" class="inline" data-confirm="Pulihkan data arsip ini kembali ke daftar utama?">
                                        @csrf
                                        <button type="submit" style="background-color: #16a34a !important; color: #ffffff !important;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm hover:opacity-90 transition-colors" title="Pulihkan Arsip">
                                            <span class="material-symbols-outlined text-xs">restore_from_trash</span>
                                            <span>Pulihkan</span>
                                        </button>
                                    </form>

                                    {{-- Hapus Permanen (Khusus Admin) --}}
                                    @if (Auth::user()->isAdmin())
                                        <form action="{{ route('arsips.force_delete', $arsip->id) }}" method="POST" class="inline" data-confirm="PERINGATAN! Hapus permanen data arsip beserta seluruh berkas filenya? Tindakan ini tidak bisa dibatalkan!">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-error-container text-on-error-container hover:bg-error-container/80 transition-colors shadow-sm" title="Hapus Permanen">
                                                <span class="material-symbols-outlined text-xs">delete_forever</span>
                                                <span>Hapus Permanen</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl text-outline mb-1">delete_outline</span>
                                <p class="text-sm font-bold">Tidak ada arsip yang terhapus.</p>
                                <p class="text-xs opacity-75 mt-0.5">Semua data arsip tersimpan aman di daftar utama.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($arsips->hasPages())
            <div class="p-4 border-t border-outline-variant">
                {{ $arsips->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
