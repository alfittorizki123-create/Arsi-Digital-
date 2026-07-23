@extends('layouts.app')

@section('title', 'Pratinjau Import')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Pratinjau Import</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Periksa data sebelum dikonfirmasi ke database.</p>
        </div>
        <div class="flex items-center gap-stack-sm">
            <form action="{{ route('arsips.import.cancel') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded border border-outline-variant text-on-surface-variant font-label-md text-label-md hover:bg-surface-container transition-colors">
                    Batal
                </button>
            </form>
            <form action="{{ route('arsips.import.confirm') }}" method="POST">
                @csrf
                <button type="submit" @disabled($validCount === 0)
                        class="flex items-center gap-2 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary-container/90 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                    Konfirmasi Import ({{ $validCount }})
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-stack-md mb-stack-lg">
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Total baris</p>
            <p class="text-display-md text-on-surface mt-1">{{ count($preview) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Siap diimport</p>
            <p class="text-display-md text-primary mt-1">{{ $validCount }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Error / dilewati</p>
            <p class="text-display-md text-error mt-1">{{ $errorCount }}</p>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Baris</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Status</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Tipe</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Kode</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Uraian</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Kurun</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Jml</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Boks</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @foreach ($preview as $row)
                        <tr class="{{ $row['valid'] ? 'hover:bg-surface-container/50' : 'bg-error-container/20' }}">
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $row['line'] }}</td>
                            <td class="py-3 px-4">
                                @if ($row['valid'])
                                    <span class="inline-flex px-2 py-1 rounded text-[11px] font-bold uppercase bg-primary-fixed text-on-primary-fixed">OK</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-[11px] font-bold uppercase bg-error-container text-on-error-container">Error</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if ($row['data']['tipe_arsip'] === 'rekap')
                                    <span class="inline-flex px-2 py-1 rounded text-[11px] font-bold uppercase bg-tertiary-fixed text-on-tertiary-fixed">Rekap</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-[11px] font-bold uppercase bg-secondary-fixed text-on-secondary-fixed">Detail</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-body-md text-on-surface font-medium">{{ $row['data']['kode_klasifikasi'] ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface max-w-xs truncate">{{ $row['data']['uraian_informasi_arsip'] ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $row['data']['kurun_waktu'] ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $row['data']['jumlah'] ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $row['data']['nomor_boks'] ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">
                                @if ($row['valid'])
                                    —
                                @else
                                    <ul class="list-disc pl-4 space-y-0.5">
                                        @foreach ($row['errors'] as $err)
                                            <li class="text-error text-sm">{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection