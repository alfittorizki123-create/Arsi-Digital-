@extends('layouts.app')

@section('title', 'Pratinjau & Pemetaan Import')

@section('content')
<form action="{{ route('arsips.import.confirm') }}" method="POST">
    @csrf

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Pratinjau & Pemetaan Import Excel</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Periksa pemetaan unit tiap sheet dan rincian data sebelum dikonfirmasi ke database.</p>
        </div>
        <div class="flex items-center gap-stack-sm">
            <a href="{{ route('arsips.import') }}" class="flex items-center gap-2 px-4 py-2 rounded border border-outline-variant text-on-surface-variant font-label-md text-label-md hover:bg-surface-container transition-colors">
                Batal
            </a>
            <button type="submit" @disabled($validCount === 0)
                    class="flex items-center gap-2 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary-container/90 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                Konfirmasi Import ({{ $validCount }} Baris)
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-stack-md mb-stack-lg">
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Total Baris Terdeteksi</p>
            <p class="text-display-md text-on-surface mt-1">{{ count($preview) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Baris Valid (Siap Simpan)</p>
            <p class="text-display-md text-primary mt-1">{{ $validCount }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Total Sheet Terdeteksi</p>
            <p class="text-display-md text-secondary mt-1">{{ count($sheetSummary ?? []) }} Sheet</p>
        </div>
    </div>

    {{-- SECTION 1: PEMETAAN SHEET PER UNIT (INTERACTIVE MAPPING) --}}
    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md mb-stack-lg shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-title-lg text-title-lg text-on-surface">📍 Pemetaan Sheet vs Unit UPT</h3>
                <p class="text-body-sm text-on-surface-variant">Pilih unit tujuan untuk tiap sheet. Anda juga bisa meng-uncheck sheet yang tidak ingin diimport atau membuat unit baru.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-2.5 px-3 text-xs font-bold text-on-surface-variant w-10 text-center">PILIH</th>
                        <th class="py-2.5 px-3 text-xs font-bold text-on-surface-variant">NAMA SHEET EXCEL</th>
                        <th class="py-2.5 px-3 text-xs font-bold text-on-surface-variant text-center">JUMLAH BARIS</th>
                        <th class="py-2.5 px-3 text-xs font-bold text-on-surface-variant text-center">STATUS DETEKSI</th>
                        <th class="py-2.5 px-3 text-xs font-bold text-on-surface-variant">TARGET UNIT DI SYSTEM (BISA DIGANTI)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @foreach ($sheetSummary as $sheetName => $summary)
                        <tr class="hover:bg-surface-container/40 transition-colors">
                            <td class="py-2.5 px-3 text-center">
                                <input type="checkbox" name="selected_sheets[]" value="{{ base64_encode($sheetName) }}" checked
                                       class="w-4 h-4 text-primary rounded border-outline-variant focus:ring-primary">
                            </td>
                            <td class="py-2.5 px-3 text-on-surface">
                                <div class="font-bold text-primary flex items-center gap-1.5 text-body-md">
                                    <span class="material-symbols-outlined text-base">domain</span>
                                    <span>{{ $summary['unit_name'] !== '-' ? $summary['unit_name'] : $sheetName }}</span>
                                </div>
                                <div class="text-xs text-on-surface-variant font-medium mt-0.5">
                                    Tab Excel: [{{ $sheetName }}]
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-center text-body-sm font-medium">
                                {{ $summary['total_rows'] }} berkas
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if ($summary['status'] === 'exact')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-primary-fixed text-on-primary-fixed">
                                        <span class="material-symbols-outlined text-xs">check_circle</span> Cocok Presisi
                                    </span>
                                @elseif ($summary['status'] === 'warning')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-secondary-container text-on-secondary-container">
                                        <span class="material-symbols-outlined text-xs">warning</span> Diduga {{ $summary['unit_name'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-error-container text-on-error-container">
                                        <span class="material-symbols-outlined text-xs">help</span> Belum Ada Unit
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                <select name="sheet_units[{{ base64_encode($sheetName) }}]" 
                                        class="w-full pl-3 pr-8 py-2 border border-outline-variant rounded-lg bg-surface focus:outline-none focus:border-primary text-xs font-semibold text-on-surface cursor-pointer">
                                    <option value="+new" @selected($summary['unit_id'] === '+new')>
                                        + Buat Unit: {{ $summary['unit_name'] !== '-' ? str_replace(['UPT Pengelolaan Pendapatan ', 'Up Pengelolaan Pendapatan ', 'UP Pengelolaan Pendapatan '], ['UPT ', 'UP ', 'UP '], $summary['unit_name']) : 'UPT ' . ucwords(strtolower(trim(str_replace(['UPT', 'UP', '[', ']'], '', $sheetName)))) }}
                                    </option>
                                    @foreach ($units as $u)
                                        <option value="{{ $u->id }}" @selected($summary['unit_id'] == $u->id)>
                                            {{ $u->nama_unit }}
                                        </option>
                                    @endforeach
                                    <option value="">-- Jangan Import Sheet Ini --</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>
@endsection