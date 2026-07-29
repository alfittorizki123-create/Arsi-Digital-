@extends('layouts.app')

@section('title', 'Detail Arsip')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Detail Arsip</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Informasi lengkap arsip @if($arsip->kode_klasifikasi){{ $arsip->kode_klasifikasi }}@else(No. kode)@endif.</p>
        </div>
        <div class="flex items-center gap-stack-sm">
            <a href="{{ route('arsips.index', $arsip->unit_id ? ['unit_id' => $arsip->unit_id] : []) }}" class="flex items-center gap-2 px-4 py-2 rounded border border-outline-variant text-on-surface-variant font-label-md text-label-md hover:bg-surface-container transition-colors">
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
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Kode Klasifikasi</dt>
                    <dd class="text-body-md text-on-surface font-medium">{{ $arsip->kode_klasifikasi ?? '-' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">No. Arsip/Berkas</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->nomor_arsip_berkas ?? '-' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Uraian Informasi Arsip</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->uraian_informasi_arsip ?? '-' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Kurun Waktu</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->kurun_waktu ?? '-' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Bulan</dt>
                    <dd class="text-body-md text-on-surface font-medium">
                        @php
                            $bulanNames = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        {{ $arsip->bulan ? ($bulanNames[$arsip->bulan] ?? 'Bulan ' . $arsip->bulan) : '-' }}
                    </dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Jumlah</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->jumlah ?? '-' }} {{ $arsip->satuan ?? '' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Tingkat Perkembangan</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->tingkat_perkembangan ?? '-' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">No. Boks</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->nomor_boks ?? '-' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Kondisi</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->kondisi ?? '-' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Klasifikasi Keamanan</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->klasifikasi_keamanan ?? '-' }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Unit/UPT/UP</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->unit->nama_unit ?? '-' }} @if($arsip->unit)({{ $arsip->unit->kode_unit }})@endif</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Jenis Pajak</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->jenisPajaks->first()?->nama_jenis_pajak ?? '-' }} @if($arsip->jenisPajaks->isNotEmpty())({{ $arsip->jenisPajaks->first()->kode }})@endif</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4 border-b border-outline-variant pb-3">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Status</dt>
                    <dd>
                        @if ($arsip->status === 'aktif')
                            <span class="inline-flex px-2 py-1 rounded text-[11px] font-bold uppercase bg-primary-fixed text-on-primary-fixed">Aktif</span>
                        @else
                            <span class="inline-flex px-2 py-1 rounded text-[11px] font-bold uppercase bg-surface-container-highest text-on-surface-variant">Inaktif</span>
                        @endif
                    </dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4">
                    <dt class="text-label-md text-on-surface-variant w-52 shrink-0">Dicatat</dt>
                    <dd class="text-body-md text-on-surface">{{ $arsip->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm">
            <div class="flex items-center justify-between mb-stack-md">
                <h3 class="text-headline-sm font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">folder_open</span>
                    Dokumen Digital ({{ $arsip->files->count() ?: ($arsip->path_file ? 1 : 0) }} Lampiran)
                </h3>
            </div>

            @php
                $pdfFiles = $arsip->files->filter(fn($f) => $f->is_pdf || str_contains($f->tipe_file, 'pdf') || str_contains($f->nama_file, '.pdf'));
                $imageFiles = $arsip->files->filter(fn($f) => $f->is_image || str_contains($f->tipe_file, 'image') || preg_match('/\.(jpe?g|png|webp)$/i', $f->nama_file));
                $otherFiles = $arsip->files->reject(fn($f) => $f->is_pdf || $f->is_image || str_contains($f->tipe_file, 'pdf') || str_contains($f->tipe_file, 'image'));
            @endphp

            @if ($arsip->files->count() > 0)
                <div class="space-y-6">
                    {{-- Section 1: Dokumen PDF / Berkas --}}
                    @if ($pdfFiles->count() > 0 || $otherFiles->count() > 0)
                        <div>
                            <h4 class="text-title-sm font-bold text-on-surface mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">picture_as_pdf</span>
                                Dokumen PDF & Berkas Resmi ({{ $pdfFiles->count() + $otherFiles->count() }} File)
                            </h4>
                            <div class="space-y-3">
                                @foreach ($pdfFiles->concat($otherFiles) as $file)
                                    <div class="p-3 border border-outline-variant rounded-xl bg-surface hover:border-primary/50 transition-all flex items-center justify-between gap-3 shadow-sm">
                                        <div class="flex items-center gap-3 overflow-hidden">
                                            <div class="w-10 h-10 rounded-lg bg-primary-fixed/30 text-primary flex items-center justify-center shrink-0 font-bold">
                                                <span class="material-symbols-outlined text-xl">{{ $file->is_pdf ? 'picture_as_pdf' : 'description' }}</span>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-sm font-bold text-on-surface truncate" title="{{ $file->nama_file }}">{{ $file->nama_file }}</p>
                                                <p class="text-xs text-on-surface-variant">{{ $file->formatted_size }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <a href="{{ $file->url }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-primary text-on-primary hover:bg-primary/90 transition-colors shadow-sm">
                                                <span class="material-symbols-outlined text-xs">open_in_new</span> Buka
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Section 2: Galeri Foto & Gambar --}}
                    @if ($imageFiles->count() > 0)
                        <div>
                            <h4 class="text-title-sm font-bold text-on-surface mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">photo_library</span>
                                Galeri Foto & Gambar Fisik ({{ $imageFiles->count() }} Foto)
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach ($imageFiles as $img)
                                    <div class="group relative rounded-xl border border-outline-variant bg-surface overflow-hidden shadow-sm hover:border-primary transition-all">
                                        <img src="{{ $img->url }}" alt="{{ $img->nama_file }}" class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-2 flex flex-col justify-between">
                                            <div></div>
                                            <div class="flex items-center justify-between text-white">
                                                <span class="text-[10px] truncate max-w-[70%]" title="{{ $img->nama_file }}">{{ $img->nama_file }}</span>
                                                <a href="{{ $img->url }}" target="_blank" class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary text-on-primary hover:bg-primary/90">
                                                    Zoom
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @elseif ($arsip->path_file)
                <div class="p-4 border border-outline-variant rounded-xl bg-surface">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-2xl">description</span>
                            <p class="text-sm font-bold text-on-surface">File Utama: {{ $arsip->path_file }}</p>
                        </div>
                        <a href="{{ $arsip->file_url }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-primary text-on-primary hover:bg-primary/90 transition-colors">
                            <span class="material-symbols-outlined text-xs">open_in_new</span> Buka File
                        </a>
                    </div>
                    @if ($arsip->isPdf())
                        <div class="w-full h-64 border border-outline-variant rounded-lg overflow-hidden">
                            <embed src="{{ $arsip->file_url }}" type="application/pdf" class="w-full h-full">
                        </div>
                    @elseif ($arsip->isImage())
                        <div class="w-full bg-surface-container-lowest rounded-lg p-2 flex justify-center border border-outline-variant">
                            <img src="{{ $arsip->file_url }}" alt="Dokumen" class="max-h-64 object-contain rounded">
                        </div>
                    @endif
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-10 text-on-surface-variant border border-dashed border-outline-variant rounded-xl">
                    <span class="material-symbols-outlined text-5xl mb-2 text-primary">folder_off</span>
                    <p class="text-body-md font-semibold">Belum ada dokumen digital.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
