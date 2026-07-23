@php
    $isEdit = isset($arsip);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">
    <div class="md:col-span-2">
        <label class="block text-label-md font-label-md text-on-surface-variant mb-1">Tipe Arsip <span class="text-error">*</span></label>
        <div class="flex gap-4">
            @php $tipe = old('tipe_arsip', $arsip->tipe_arsip ?? 'detail'); @endphp
            <label class="flex items-center gap-2 text-body-md text-on-surface cursor-pointer">
                <input type="radio" name="tipe_arsip" value="detail" @checked($tipe === 'detail') class="border-outline-variant text-primary focus:ring-primary">
                <span>Detail (per berkas)</span>
            </label>
            <label class="flex items-center gap-2 text-body-md text-on-surface cursor-pointer">
                <input type="radio" name="tipe_arsip" value="rekap" @checked($tipe === 'rekap') class="border-outline-variant text-primary focus:ring-primary">
                <span>Rekap (per unit)</span>
            </label>
        </div>
        @error('tipe_arsip') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="kode_klasifikasi" class="block text-label-md font-label-md text-on-surface-variant mb-1">Kode Klasifikasi</label>
        <input type="text" name="kode_klasifikasi" id="kode_klasifikasi"
               value="{{ old('kode_klasifikasi', $arsip->kode_klasifikasi ?? '') }}"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('kode_klasifikasi') border-error @else border-outline-variant @enderror"
               placeholder="Contoh: 900.1.13.1">
        @error('kode_klasifikasi') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="nomor_arsip_berkas" class="block text-label-md font-label-md text-on-surface-variant mb-1">No. Arsip/Berkas</label>
        <input type="text" name="nomor_arsip_berkas" id="nomor_arsip_berkas"
               value="{{ old('nomor_arsip_berkas', $arsip->nomor_arsip_berkas ?? '') }}"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('nomor_arsip_berkas') border-error @else border-outline-variant @enderror"
               placeholder="(Opsional)">
        @error('nomor_arsip_berkas') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="uraian_informasi_arsip" class="block text-label-md font-label-md text-on-surface-variant mb-1">Uraian Informasi Arsip</label>
        <textarea name="uraian_informasi_arsip" id="uraian_informasi_arsip" rows="3"
                  class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('uraian_informasi_arsip') border-error @else border-outline-variant @enderror"
                  placeholder="Contoh: Laporan Pertanggungjawaban Bendahara Penerimaan Pembantu Bulan Januari 2023">{{ old('uraian_informasi_arsip', $arsip->uraian_informasi_arsip ?? '') }}</textarea>
        @error('uraian_informasi_arsip') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="kurun_waktu" class="block text-label-md font-label-md text-on-surface-variant mb-1">Kurun Waktu (Tahun) <span class="text-error">*</span></label>
        <input type="number" name="kurun_waktu" id="kurun_waktu"
               value="{{ old('kurun_waktu', $arsip->kurun_waktu ?? date('Y')) }}"
               min="1990" max="{{ date('Y') + 1 }}"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('kurun_waktu') border-error @else border-outline-variant @enderror"
               required>
        @error('kurun_waktu') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="jumlah" class="block text-label-md font-label-md text-on-surface-variant mb-1">Jumlah</label>
        <input type="number" name="jumlah" id="jumlah"
               value="{{ old('jumlah', $arsip->jumlah ?? 1) }}"
               min="0"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('jumlah') border-error @else border-outline-variant @enderror">
        @error('jumlah') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="satuan" class="block text-label-md font-label-md text-on-surface-variant mb-1">Satuan</label>
        <input type="text" name="satuan" id="satuan"
               value="{{ old('satuan', $arsip->satuan ?? 'Berkas') }}"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('satuan') border-error @else border-outline-variant @enderror">
        @error('satuan') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="tingkat_perkembangan" class="block text-label-md font-label-md text-on-surface-variant mb-1">Tingkat Perkembangan</label>
        <select name="tingkat_perkembangan" id="tingkat_perkembangan"
                class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('tingkat_perkembangan') border-error @else border-outline-variant @enderror">
            <option value="">— Pilih —</option>
            @php $tp = old('tingkat_perkembangan', $arsip->tingkat_perkembangan ?? ''); @endphp
            <option value="Asli" @selected($tp === 'Asli')>Asli</option>
            <option value="Copy" @selected($tp === 'Copy')>Copy</option>
            <option value="Asli/Copy" @selected($tp === 'Asli/Copy')>Asli/Copy</option>
        </select>
        @error('tingkat_perkembangan') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="nomor_boks" class="block text-label-md font-label-md text-on-surface-variant mb-1">No. Boks</label>
        <input type="text" name="nomor_boks" id="nomor_boks"
               value="{{ old('nomor_boks', $arsip->nomor_boks ?? '') }}"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('nomor_boks') border-error @else border-outline-variant @enderror"
               placeholder="Contoh: 54 atau 3,4">
        @error('nomor_boks') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="kondisi" class="block text-label-md font-label-md text-on-surface-variant mb-1">Kondisi Arsip</label>
        <select name="kondisi" id="kondisi"
                class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('kondisi') border-error @else border-outline-variant @enderror">
            @php $kd = old('kondisi', $arsip->kondisi ?? 'Baik'); @endphp
            <option value="Baik" @selected($kd === 'Baik')>Baik</option>
            <option value="Rusak" @selected($kd === 'Rusak')>Rusak</option>
        </select>
        @error('kondisi') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="klasifikasi_keamanan" class="block text-label-md font-label-md text-on-surface-variant mb-1">Klasifikasi Keamanan</label>
        <select name="klasifikasi_keamanan" id="klasifikasi_keamanan"
                class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('klasifikasi_keamanan') border-error @else border-outline-variant @enderror">
            @php $kk = old('klasifikasi_keamanan', $arsip->klasifikasi_keamanan ?? 'Terbuka'); @endphp
            <option value="Terbuka" @selected($kk === 'Terbuka')>Terbuka</option>
            <option value="Terbatas" @selected($kk === 'Terbatas')>Terbatas</option>
            <option value="Rahasia" @selected($kk === 'Rahasia')>Rahasia</option>
        </select>
        @error('klasifikasi_keamanan') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="unit_id" class="block text-label-md font-label-md text-on-surface-variant mb-1">Unit/UPT/UP</label>
        <select name="unit_id" id="unit_id"
                class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('unit_id') border-error @else border-outline-variant @enderror">
            <option value="">— Pilih —</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected(old('unit_id', $arsip->unit_id ?? '') == $unit->id)>
                    {{ $unit->nama_unit }} ({{ $unit->kode_unit }})
                </option>
            @endforeach
        </select>
        @error('unit_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="jenis_pajak_id" class="block text-label-md font-label-md text-on-surface-variant mb-1">Jenis Pajak <span class="text-on-surface-variant/60 normal-case">(opsional)</span></label>
        <select name="jenis_pajak_id" id="jenis_pajak_id"
                class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('jenis_pajak_id') border-error @else border-outline-variant @enderror">
            <option value="">— Pilih —</option>
            @foreach ($jenisPajaks as $jp)
                <option value="{{ $jp->id }}" @selected(old('jenis_pajak_id', $arsip->jenis_pajak_id ?? '') == $jp->id)>
                    {{ $jp->nama_jenis_pajak }} ({{ $jp->kode }})
                </option>
            @endforeach
        </select>
        @error('jenis_pajak_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="block text-label-md font-label-md text-on-surface-variant mb-1">Status <span class="text-error">*</span></label>
        <select name="status" id="status"
                class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('status') border-error @else border-outline-variant @enderror"
                required>
            @php $st = old('status', $arsip->status ?? 'aktif'); @endphp
            <option value="aktif" @selected($st === 'aktif')>Aktif</option>
            <option value="inaktif" @selected($st === 'inaktif')>Inaktif</option>
        </select>
        @error('status') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="file_arsip" class="block text-label-md font-label-md text-on-surface-variant mb-1">
            File Dokumen (PDF / Gambar)
        </label>
        <input type="file" name="file_arsip" id="file_arsip" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
               class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary-fixed file:text-on-primary-fixed file:text-label-md file:font-semibold @error('file_arsip') border-error @else border-outline-variant @enderror">
        <p class="mt-1 text-label-md text-on-surface-variant">Format: PDF, JPG, PNG. Maksimal 10 MB. File disimpan di storage, bukan di database.</p>
        @error('file_arsip') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror

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