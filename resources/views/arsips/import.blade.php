@extends('layouts.app')

@section('title', 'Import Excel')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-stack-md text-center">
            <h2 class="font-display-md text-display-md text-on-surface">Import File Excel Pemindahan Arsip</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Unggah file Excel (.xlsx / .xls). Sistem akan otomatis membaca Sheet dan mengelompokkannya per Unit/UPT.</p>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-stack-lg shadow-sm">
            <form action="{{ route('arsips.import.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- Pilihan File Excel --}}
                <div class="mb-stack-md">
                    <label for="files" class="block text-label-md font-bold text-on-surface mb-2">
                        Pilih File Excel Pemindahan <span class="text-error">*</span>
                    </label>
                    <div class="p-8 border-2 border-dashed border-emerald-500/40 rounded-2xl bg-surface-container-lowest hover:bg-emerald-500/5 transition-all text-center cursor-pointer relative">
                        <input type="file" name="files[]" id="files" accept=".xlsx,.xls,.csv" multiple required
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               onchange="updateSelectedExcelList(this)">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                                <span class="material-symbols-outlined text-3xl">upload_file</span>
                            </div>
                            <div>
                                <p class="text-body-lg font-bold text-on-surface">Klik atau Drag & Drop File Excel di sini</p>
                                <p class="text-xs text-on-surface-variant mt-1">Bisa memilih 1 file atau banyak file sekaligus (.xlsx, .xls)</p>
                            </div>
                        </div>
                    </div>
                    <div id="selected_excel_preview" class="mt-4 flex flex-wrap gap-2"></div>
                    @error('files') <p class="mt-2 text-xs font-bold text-error">{{ $message }}</p> @enderror
                    @error('files.*') <p class="mt-2 text-xs font-bold text-error">{{ $message }}</p> @enderror
                    @error('file') <p class="mt-2 text-xs font-bold text-error">{{ $message }}</p> @enderror
                </div>

                {{-- Unit Default (Opsional) Accordion --}}
                <div x-data="{ showAdvanced: false }" class="mb-stack-lg border-t border-outline-variant/60 pt-4">
                    <button type="button" @@click="showAdvanced = !showAdvanced" class="flex items-center gap-2 text-xs font-bold text-primary hover:underline">
                        <span class="material-symbols-outlined text-base" x-text="showAdvanced ? 'expand_less' : 'tune'">tune</span>
                        <span>Opsi Lanjutan: Pilih Unit Default (Opsional)</span>
                    </button>
                    <div x-show="showAdvanced" x-cloak class="mt-3 p-3.5 bg-surface-container/30 rounded-lg border border-outline-variant/60">
                        <label for="unit_id" class="block text-xs font-bold text-on-surface-variant mb-1">Unit Tujuan Default</label>
                        <select name="unit_id" id="unit_id"
                                class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-xs focus:outline-none focus:border-primary">
                            <option value="">— Otomatis Deteksi Unit dari Nama Sheet —</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" @selected(old('unit_id', request('unit_id')) == $unit->id)>{{ $unit->nama_unit }} ({{ $unit->kode_unit }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-stack-md border-t border-outline-variant">
                    <a href="{{ route('arsips.pilih_unit') }}" class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-bold text-xs hover:bg-surface-container transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            style="background-color: #059669; color: #ffffff;"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg font-bold text-xs hover:opacity-90 transition-all shadow-md">
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                        <span>Lanjutkan Pratinjau & Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function updateSelectedExcelList(input) {
        const preview = document.getElementById('selected_excel_preview');
        preview.innerHTML = '';
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                const badge = document.createElement('div');
                badge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-700 border border-emerald-500/30 shadow-xs';
                badge.innerHTML = `<span class="material-symbols-outlined text-sm text-emerald-600">table_chart</span> ${file.name} (${(file.size/1024).toFixed(1)} KB)`;
                preview.appendChild(badge);
            });
        }
    }
    </script>
@endsection