@extends('layouts.app')

@section('title', 'Tambah Arsip Baru')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Tambah Arsip Baru</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Isi data arsip pajak baru ke dalam sistem.</p>
        </div>
        <a href="{{ route('arsips.index', request('unit_id') ? ['unit_id' => request('unit_id')] : []) }}" class="flex items-center gap-2 px-4 py-2 rounded border border-outline-variant text-on-surface-variant font-label-md text-label-md hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
            Kembali
        </a>
    </div>

    <div class="mb-5 p-4 rounded-xl bg-primary-fixed/20 border border-primary/30 text-on-surface max-w-4xl">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-primary text-2xl shrink-0 mt-0.5">info</span>
            <div>
                <p class="text-sm font-bold text-primary">Membuat Arsip Baru</p>
                <p class="text-xs text-on-surface-variant mt-1">Form ini digunakan untuk mendaftarkan <strong>arsip baru</strong> ke dalam sistem. Jika Anda hanya ingin menambah / mengunggah file lampiran ke arsip yang <strong>sudah ada</strong>, Anda cukup menekan tombol <strong>"+ Upload File"</strong> langsung pada baris tabel di menu <strong>Daftar Arsip</strong>.</p>
            </div>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm max-w-4xl">
        <form action="{{ route('arsips.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('arsips._form')
            <div class="flex items-center justify-end gap-stack-sm mt-stack-lg pt-stack-md border-t border-outline-variant">
                <a href="{{ route('arsips.index') }}" class="px-4 py-2 rounded border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container transition-colors">Batal</a>
                <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary-container/90 transition-colors shadow-sm">
                    <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                    Simpan Laporan
                </button>
            </div>
        </form>
    </div>
@endsection
