@php
    $isEdit = isset($arsip);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">
    <div>
        <label for="nomor_arsip" class="block text-label-md font-label-md text-on-surface-variant mb-1">Nomor Arsip <span class="text-error">*</span></label>
        <input type="text" name="nomor_arsip" id="nomor_arsip"
               value="{{ old('nomor_arsip', $arsip->nomor_arsip ?? '') }}"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('nomor_arsip') border-error @else border-outline-variant @enderror"
               placeholder="Contoh: ARS-2024-001" required>
        @error('nomor_arsip')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="jenis_pajak_id" class="block text-label-md font-label-md text-on-surface-variant mb-1">Jenis Pajak <span class="text-error">*</span></label>
        <select name="jenis_pajak_id" id="jenis_pajak_id"
                class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('jenis_pajak_id') border-error @else border-outline-variant @enderror"
                required>
            <option value="">Pilih Jenis Pajak</option>
            @foreach ($jenisPajaks as $jp)
                <option value="{{ $jp->id }}" @selected(old('jenis_pajak_id', $arsip->jenis_pajak_id ?? '') == $jp->id)>
                    {{ $jp->nama_jenis_pajak }} ({{ $jp->kode }})
                </option>
            @endforeach
        </select>
        @error('jenis_pajak_id')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="nama_wajib_pajak" class="block text-label-md font-label-md text-on-surface-variant mb-1">Nama Wajib Pajak <span class="text-error">*</span></label>
        <input type="text" name="nama_wajib_pajak" id="nama_wajib_pajak"
               value="{{ old('nama_wajib_pajak', $arsip->nama_wajib_pajak ?? '') }}"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('nama_wajib_pajak') border-error @else border-outline-variant @enderror"
               placeholder="Nama wajib pajak / instansi" required>
        @error('nama_wajib_pajak')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tahun_arsip" class="block text-label-md font-label-md text-on-surface-variant mb-1">Tahun Arsip <span class="text-error">*</span></label>
        <input type="number" name="tahun_arsip" id="tahun_arsip"
               value="{{ old('tahun_arsip', $arsip->tahun_arsip ?? date('Y')) }}"
               min="1990" max="{{ date('Y') + 1 }}"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('tahun_arsip') border-error @else border-outline-variant @enderror"
               required>
        @error('tahun_arsip')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="nomor_rak" class="block text-label-md font-label-md text-on-surface-variant mb-1">Nomor Rak</label>
        <input type="text" name="nomor_rak" id="nomor_rak"
               value="{{ old('nomor_rak', $arsip->nomor_rak ?? '') }}"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('nomor_rak') border-error @else border-outline-variant @enderror"
               placeholder="Contoh: R-A1-05">
        @error('nomor_rak')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="unit_id" class="block text-label-md font-label-md text-on-surface-variant mb-1">Unit/UPT <span class="text-error">*</span></label>
        <select name="unit_id" id="unit_id"
                class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('unit_id') border-error @else border-outline-variant @enderror"
                required>
            <option value="">Pilih Unit/UPT</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected(old('unit_id', $arsip->unit_id ?? '') == $unit->id)>
                    {{ $unit->nama_unit }} ({{ $unit->kode_unit }})
                </option>
            @endforeach
        </select>
        @error('unit_id')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="block text-label-md font-label-md text-on-surface-variant mb-1">Status <span class="text-error">*</span></label>
        <select name="status" id="status"
                class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('status') border-error @else border-outline-variant @enderror"
                required>
            <option value="aktif" @selected(old('status', $arsip->status ?? 'aktif') === 'aktif')>Aktif</option>
            <option value="inaktif" @selected(old('status', $arsip->status ?? '') === 'inaktif')>Inaktif</option>
        </select>
        @error('status')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="file_arsip" class="block text-label-md font-label-md text-on-surface-variant mb-1">
            File Dokumen (PDF / Gambar)
        </label>
        <input type="file" name="file_arsip" id="file_arsip" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary-fixed file:text-on-primary-fixed file:text-label-md file:font-semibold @error('file_arsip') border-error @else border-outline-variant @enderror">
        <p class="mt-1 text-label-md text-on-surface-variant">Format: PDF, JPG, PNG. Maksimal 10 MB. File disimpan di storage, bukan di database.</p>
        @error('file_arsip')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror

        @if ($isEdit && $arsip->path_file)
            <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-3 p-3 rounded-lg bg-surface-container border border-outline-variant">
                <div class="flex items-center gap-2 text-body-md text-on-surface">
                    <span class="material-symbols-outlined text-primary">{{ $arsip->isPdf() ? 'picture_as_pdf' : 'image' }}</span>
                    <span>File saat ini: <a href="{{ $arsip->file_url }}" target="_blank" class="text-primary hover:underline font-medium">{{ $arsip->path_file }}</a></span>
                </div>
                <label class="inline-flex items-center gap-2 text-body-md text-on-surface-variant cursor-pointer">
                    <input type="checkbox" name="hapus_file" value="1" class="rounded border-outline-variant text-primary focus:ring-primary">
                    Hapus file
                </label>
            </div>
        @endif
    </div>
</div>
