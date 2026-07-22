@extends('layouts.app')

@section('title', 'Tambah Arsip')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Tambah Arsip</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Isi data arsip pajak baru ke dalam sistem.</p>
        </div>
        <a href="{{ route('arsips.index') }}" class="flex items-center gap-2 px-4 py-2 rounded border border-outline-variant text-on-surface-variant font-label-md text-label-md hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
            Kembali
        </a>
    </div>

    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-lg shadow-sm max-w-4xl">
        <form action="{{ route('arsips.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('arsips._form')
            <div class="flex items-center justify-end gap-stack-sm mt-stack-lg pt-stack-md border-t border-outline-variant">
                <a href="{{ route('arsips.index') }}" class="px-4 py-2 rounded border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container transition-colors">Batal</a>
                <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary-container/90 transition-colors shadow-sm">
                    <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                    Simpan Arsip
                </button>
            </div>
        </form>
    </div>
@endsection
