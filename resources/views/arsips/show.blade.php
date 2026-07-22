@extends('layouts.app')

@section('title', 'Detail Arsip')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Detail Arsip</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Informasi lengkap arsip {{ $arsip->nomor_arsip }}.</p>
        </div>
        <div class="flex items-center gap-stack-sm">
            <a href="{{ route('arsips.index') }}" class="flex items-center gap-2 px-4 py-2 rounded border border-outline-variant text-on-surface-variant font-label-md text-label-md hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
                Kembali
            </a>
            <a href="{{ route('arsips.edit', $arsip) }}" class="flex items-center gap-2 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary-container/90 transition-colors shadow-sm">
                <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-stack-lg">
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm">
            <h3 class="text-headline-sm font-bold text-on-surface mb-stack-md">Metadata Arsip</h3>
            <dl class="space-y-3">
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-40 shrink-0">Nomor Arsip</dt>
                    <dd class="text-body-md text-on-surface font-medium">{{ $arsip->nomor_arsip }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-40 shrink-0">Jenis Pajak</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->jenisPajak->nama_jenis_pajak ?? '-' }} ({{ $arsip->jenisPajak->kode ?? '-' }})</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-40 shrink-0">Nama Wajib Pajak</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->nama_wajib_pajak }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-40 shrink-0">Tahun Arsip</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->tahun_arsip }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-40 shrink-0">Nomor Rak</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->nomor_rak ?? '-' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-40 shrink-0">Unit/UPT</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->unit->nama_unit ?? '-' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-40 shrink-0">Status</dt>
                    <dd>
                        @if ($arsip->status === 'aktif')
                            <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-bold tracking-wide uppercase bg-primary-fixed text-on-primary-fixed">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-bold tracking-wide uppercase bg-surface-container-highest text-on-surface-variant">Inaktif</span>
                        @endif
                    </dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4">
                    <dt class="text-label-md text-on-surface-variant w-40 shrink-0">Dicatat</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm">
            <div class="flex items-center justify-between mb-stack-md">
                <h3 class="text-headline-sm font-bold text-on-surface">Dokumen Digital</h3>
                @if ($arsip->path_file)
                    <a href="{{ $arsip->file_url }}" target="_blank" class="flex items-center gap-1 text-primary text-label-md hover:underline">
                        <span class="material-symbols-outlined" style="font-size: 18px;">open_in_new</span>
                        Buka file
                    </a>
                @endif
            </div>

            @if ($arsip->path_file)
                <p class="text-label-md text-on-surface-variant mb-3">{{ $arsip->path_file }} · {{ strtoupper($arsip->tipe_file ?? '-') }}</p>

                @if ($arsip->isPdf())
                    <div class="w-full h-[480px] border border-outline-variant rounded-lg overflow-hidden bg-surface">
                        <embed src="{{ $arsip->file_url }}" type="application/pdf" class="w-full h-full">
                    </div>
                @elseif ($arsip->isImage())
                    <div class="w-full border border-outline-variant rounded-lg overflow-hidden bg-surface flex items-center justify-center p-2">
                        <img src="{{ $arsip->file_url }}" alt="Dokumen {{ $arsip->nomor_arsip }}" class="max-w-full max-h-[480px] object-contain rounded">
                    </div>
                @else
                    <p class="text-body-md text-on-surface-variant">Tipe file tidak dapat dipratinjau. Silakan buka file secara langsung.</p>
                @endif
            @else
                <div class="flex flex-col items-center justify-center py-12 text-on-surface-variant border border-dashed border-outline-variant rounded-lg">
                    <span class="material-symbols-outlined text-5xl mb-2">description</span>
                    <p class="text-body-md">Belum ada file terunggah.</p>
                    <a href="{{ route('arsips.edit', $arsip) }}" class="mt-3 text-primary text-label-md hover:underline">Unggah lewat Edit Arsip</a>
                </div>
            @endif
        </div>
    </div>
@endsection
