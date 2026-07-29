@extends('layouts.app')

@section('title', 'Pratinjau & Pemetaan Import')

@section('content')
<form action="{{ route('arsips.import.confirm') }}" method="POST">
    @csrf
    <input type="hidden" name="import_token" value="{{ $token }}">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Periksa Data Sebelum Disimpan</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Langkah 2: cek tujuan kantor UP/UPT untuk setiap sheet Excel. Data belum tersimpan sebelum tombol konfirmasi ditekan.</p>
        </div>
        <div class="flex items-center gap-stack-sm">
            <a href="{{ route('arsips.import') }}" class="flex items-center gap-2 px-4 py-2 rounded border border-outline-variant text-on-surface-variant font-label-md text-label-md hover:bg-surface-container transition-colors">
                Kembali
            </a>
            @php
                $importableCount = collect($sheetSummary ?? [])->sum('total_rows');
            @endphp
            <button type="submit" @disabled($importableCount === 0)
                    class="flex items-center gap-2 px-4 py-2 rounded bg-primary-container text-on-primary font-label-md text-label-md hover:bg-primary-container/90 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                Simpan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-stack-md mb-stack-lg">
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Total Baris Terdeteksi</p>
            <p class="text-display-md text-on-surface mt-1">{{ count($preview) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Baris Siap Import</p>
            <p class="text-display-md text-primary mt-1">{{ $importableCount }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md shadow-sm">
            <p class="text-label-md text-on-surface-variant">Total Sheet Terdeteksi</p>
            <p class="text-display-md text-secondary mt-1">{{ count($sheetSummary ?? []) }} Sheet</p>
        </div>
    </div>

    <div class="rounded-xl border border-primary/30 bg-primary-fixed/20 p-4 mb-stack-lg">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-primary text-2xl shrink-0">info</span>
            <div>
                <p class="font-bold text-on-surface text-sm">Panduan singkat</p>
                <p class="text-xs text-on-surface-variant mt-1">Centang sheet yang ingin dimasukkan. Pastikan kolom “Target Unit” sudah benar. Jika nama unit belum ada, sistem dapat membuat unit baru otomatis.</p>
            </div>
        </div>
    </div>

    {{-- SECTION 1: PEMETAAN SHEET PER UNIT (INTERACTIVE MAPPING) --}}
    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-stack-md mb-stack-lg shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-title-lg text-title-lg text-on-surface">📍 Cocokkan Sheet Excel dengan Kantor UP/UPT</h3>
                <p class="text-body-sm text-on-surface-variant">Pilih kantor tujuan untuk tiap sheet. Hilangkan centang jika sheet tidak ingin diimport.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-2.5 px-3 text-xs font-bold text-on-surface-variant w-10 text-center">IMPORT?</th>
                        <th class="py-2.5 px-3 text-xs font-bold text-on-surface-variant">NAMA SHEET EXCEL</th>
                        <th class="py-2.5 px-3 text-xs font-bold text-on-surface-variant text-center">JUMLAH BARIS</th>
                        <th class="py-2.5 px-3 text-xs font-bold text-on-surface-variant text-center">STATUS DETEKSI</th>
                        <th class="py-2.5 px-3 text-xs font-bold text-on-surface-variant">TARGET KANTOR UP/UPT (BISA DIGANTI)</th>
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
                                        <span class="material-symbols-outlined text-xs">add_circle</span> Unit Baru
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-secondary-container text-on-secondary-container">
                                        <span class="material-symbols-outlined text-xs">add_circle</span> Unit Baru
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                @php
                                    $newUnitLabel = '+ Buat Unit: ' . ($summary['unit_name'] !== '-' ? str_replace(['UPT Pengelolaan Pendapatan ', 'Up Pengelolaan Pendapatan ', 'UP Pengelolaan Pendapatan '], ['UPT ', 'UP ', 'UP '], $summary['unit_name']) : 'UPT ' . ucwords(strtolower(trim(str_replace(['UPT', 'UP', '[', ']'], '', $sheetName)))));
                                    $selectedUnitLabel = $newUnitLabel;

                                    if (is_numeric($summary['unit_id'])) {
                                        $selectedUnit = $units->firstWhere('id', (int) $summary['unit_id']);
                                        $selectedUnitLabel = $selectedUnit ? $selectedUnit->nama_unit : $newUnitLabel;
                                    }
                                @endphp

                                <div class="relative searchable-unit-select" data-selected-label="{{ $selectedUnitLabel }}">
                                    <input type="hidden"
                                           name="sheet_units[{{ base64_encode($sheetName) }}]"
                                           value="{{ $summary['unit_id'] ?? '+new' }}"
                                           class="unit-value">

                                    <button type="button"
                                            class="unit-select-trigger w-full min-h-[42px] flex items-center justify-between gap-2 pl-3 pr-3 py-2 border border-outline-variant rounded-lg bg-surface focus:outline-none focus:border-primary text-xs font-semibold text-on-surface cursor-pointer">
                                        <span class="unit-selected-label truncate text-left">{{ $selectedUnitLabel }}</span>
                                        <span class="material-symbols-outlined text-base text-on-surface-variant shrink-0">expand_more</span>
                                    </button>

                                    <div class="unit-options hidden absolute z-50 mt-1 w-full rounded-lg border border-outline-variant bg-surface-container-lowest shadow-lg overflow-hidden">
                                        <div class="p-2 border-b border-outline-variant">
                                            <input type="text"
                                                   class="unit-search w-full px-3 py-2 rounded-md border border-outline-variant bg-surface text-xs text-on-surface focus:outline-none focus:border-primary"
                                                   placeholder="Ketik nama kantor UP/UPT...">
                                        </div>

                                        <div class="unit-options-list max-h-48 overflow-y-auto py-1">
                                            <button type="button"
                                                    class="unit-option w-full text-left px-3 py-2 text-xs font-semibold hover:bg-primary-container/20 text-on-surface"
                                                    data-value=""
                                                    data-label="-- Jangan Import Sheet Ini --">
                                                -- Jangan Import Sheet Ini --
                                            </button>

                                            <button type="button"
                                                    class="unit-option w-full text-left px-3 py-2 text-xs font-semibold hover:bg-primary-container/20 text-on-surface"
                                                    data-value="+new"
                                                    data-label="{{ $newUnitLabel }}">
                                                {{ $newUnitLabel }}
                                            </button>

                                            @foreach ($units as $u)
                                                <button type="button"
                                                        class="unit-option w-full text-left px-3 py-2 text-xs font-semibold hover:bg-primary-container/20 text-on-surface"
                                                        data-value="{{ $u->id }}"
                                                        data-label="{{ $u->nama_unit }}">
                                                    {{ $u->nama_unit }}
                                                </button>
                                            @endforeach

                                            <div class="unit-empty hidden px-3 py-2 text-xs text-on-surface-variant">
                                                Unit tidak ditemukan.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const closeAllDropdowns = (except = null) => {
            document.querySelectorAll('.searchable-unit-select').forEach((select) => {
                if (select !== except) {
                    select.querySelector('.unit-options')?.classList.add('hidden');
                    const search = select.querySelector('.unit-search');
                    if (search) {
                        search.value = '';
                    }
                    select.querySelectorAll('.unit-option').forEach((option) => option.classList.remove('hidden'));
                    select.querySelector('.unit-empty')?.classList.add('hidden');
                }
            });
        };

        document.querySelectorAll('.searchable-unit-select').forEach((select) => {
            const trigger = select.querySelector('.unit-select-trigger');
            const dropdown = select.querySelector('.unit-options');
            const search = select.querySelector('.unit-search');
            const hiddenInput = select.querySelector('.unit-value');
            const label = select.querySelector('.unit-selected-label');
            const options = select.querySelectorAll('.unit-option');
            const empty = select.querySelector('.unit-empty');

            trigger.addEventListener('click', (event) => {
                event.stopPropagation();
                const willOpen = dropdown.classList.contains('hidden');

                closeAllDropdowns(select);
                dropdown.classList.toggle('hidden', !willOpen);

                if (willOpen) {
                    setTimeout(() => search.focus(), 50);
                }
            });

            search.addEventListener('input', () => {
                const keyword = search.value.trim().toLowerCase();
                let visibleCount = 0;

                options.forEach((option) => {
                    const text = option.dataset.label.toLowerCase();
                    const isVisible = text.includes(keyword);
                    option.classList.toggle('hidden', !isVisible);

                    if (isVisible) {
                        visibleCount++;
                    }
                });

                empty.classList.toggle('hidden', visibleCount > 0);
            });

            options.forEach((option) => {
                option.addEventListener('click', () => {
                    hiddenInput.value = option.dataset.value;
                    label.textContent = option.dataset.label;
                    dropdown.classList.add('hidden');
                    search.value = '';

                    options.forEach((item) => item.classList.remove('hidden'));
                    empty.classList.add('hidden');
                });
            });
        });

        document.addEventListener('click', () => closeAllDropdowns());
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeAllDropdowns();
            }
        });
    });
</script>
@endsection
