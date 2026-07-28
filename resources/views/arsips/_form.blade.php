@php
    $isEdit = isset($arsip);
@endphp

<input type="hidden" name="tipe_arsip" value="detail">

<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">

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
        <div class="flex gap-2">
            <input type="number" name="kurun_waktu" id="kurun_waktu"
                   value="{{ old('kurun_waktu', $arsip->kurun_waktu ?? date('Y')) }}"
                   min="1990" max="{{ date('Y') + 1 }}"
                   class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('kurun_waktu') border-error @else border-outline-variant @enderror"
                   required>
            <select name="bulan" id="bulan" class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('bulan') border-error @else border-outline-variant @enderror">
                <option value="">-- Pilih Bulan --</option>
                @foreach(['1'=>'Januari', '2'=>'Februari', '3'=>'Maret', '4'=>'April', '5'=>'Mei', '6'=>'Juni', '7'=>'Juli', '8'=>'Agustus', '9'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'] as $num => $name)
                    <option value="{{ $num }}" @selected(old('bulan', $arsip->bulan ?? '') == $num)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        @error('kurun_waktu') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
        @error('bulan') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
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
               placeholder="Contoh: 1">
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
        @php 
            $presetUnitId = old('unit_id', $arsip->unit_id ?? request('unit_id'));
        @endphp
        @if ($presetUnitId)
            <input type="hidden" name="unit_id" value="{{ $presetUnitId }}">
            <select disabled class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container text-on-surface-variant cursor-not-allowed opacity-80 text-body-md font-medium">
                @foreach ($units as $unit)
                    @if ($presetUnitId == $unit->id)
                        <option value="{{ $unit->id }}" selected>{{ $unit->nama_unit }} ({{ $unit->kode_unit }})</option>
                    @endif
                @endforeach
            </select>
        @else
            <select name="unit_id" id="unit_id"
                    class="w-full px-3 py-2 border rounded bg-surface focus:outline-none focus:border-primary text-body-md @error('unit_id') border-error @else border-outline-variant @enderror">
                <option value="">— Pilih —</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" @selected($presetUnitId == $unit->id)>
                        {{ $unit->nama_unit }} ({{ $unit->kode_unit }})
                    </option>
                @endforeach
            </select>
        @endif
        @error('unit_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-label-md font-label-md text-on-surface-variant mb-2">
            Jenis Pajak <span class="text-on-surface-variant/60 font-normal text-xs">(Opsional - bisa pilih lebih dari 1 jenis pajak sekaligus pada laporan ini)</span>
        </label>
        @php
            $selectedJpIds = old('jenis_pajak_ids', isset($arsip) && $arsip->jenisPajaks->count() > 0 ? $arsip->jenisPajaks->pluck('id')->toArray() : (isset($arsip->jenis_pajak_id) && $arsip->jenis_pajak_id ? [$arsip->jenis_pajak_id] : []));
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2.5 p-3.5 rounded-lg border border-outline-variant bg-surface-container-lowest shadow-xs">
            @foreach ($jenisPajaks as $jp)
                @php $isChecked = in_array($jp->id, $selectedJpIds); @endphp
                <label class="flex items-center gap-2 px-3 py-2 rounded-md border border-outline-variant/60 hover:bg-primary-fixed/20 cursor-pointer transition-colors text-body-sm font-medium text-on-surface select-none">
                    <input type="checkbox" name="jenis_pajak_ids[]" value="{{ $jp->id }}" 
                           @checked($isChecked)
                           class="w-4 h-4 text-primary border-outline-variant rounded focus:ring-primary">
                    <span class="text-xs sm:text-sm font-bold">{{ $jp->kode }}</span>
                    <span class="text-xs text-on-surface-variant truncate" title="{{ $jp->nama_jenis_pajak }}">({{ $jp->nama_jenis_pajak }})</span>
                </label>
            @endforeach
        </div>
        @error('jenis_pajak_ids') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
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
        <label for="form_multi_files" class="block text-label-md font-label-md text-on-surface-variant mb-1">
            Lampiran File / Gambar (Bisa pilih 100+ file sekaligus - Terunggah Langsung Otomatis!)
        </label>
        
        {{-- Area Drag & Drop Realtime Upload --}}
        <div class="p-5 border-2 border-dashed border-primary/40 rounded-xl bg-surface-container-lowest hover:bg-primary-fixed-dim/10 transition-colors text-center cursor-pointer relative">
            <input type="file" id="form_multi_files" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx,.xls,.xlsx"
                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                   onchange="startFormBatchUpload(this)">
            <div class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                <span class="material-symbols-outlined text-4xl text-primary">cloud_upload</span>
                <p class="text-body-md font-bold text-on-surface">Klik atau Drag & Drop puluhan/ratusan PDF & Foto ke sini</p>
                <p class="text-xs text-on-surface-variant">File akan langsung diunggah di latar belakang satu per satu (tanpa batasan ukuran/jumlah). Format: PDF, JPG, PNG, WEBP, DOCX, XLSX.</p>
            </div>
        </div>

        {{-- Container Input Hidden ID File Terunggah --}}
        <div id="hidden_uploaded_file_ids"></div>

        {{-- Progress Bar Realtime Upload --}}
        <div id="formUploadProgress" class="mt-3 hidden p-3 rounded-lg bg-primary-fixed-dim/20 border border-primary/30">
            <div class="flex items-center justify-between text-xs font-bold text-primary mb-1">
                <span id="formUploadStatus">Mengunggah file...</span>
                <span id="formUploadPercent">0%</span>
            </div>
            <div class="w-full bg-surface-container rounded-full h-2.5 overflow-hidden">
                <div id="formUploadBar" class="bg-primary h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
            </div>
        </div>

        {{-- Daftar Preview Lampiran (Tersimpan & Baru Terunggah) --}}
        <div class="mt-4 p-4 rounded-xl bg-surface-container border border-outline-variant">
            <h4 class="text-label-md font-bold text-on-surface mb-3 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-primary">folder_open</span>
                Daftar Lampiran Terunggah (<span id="form_files_count">{{ $isEdit && isset($arsip->files) ? $arsip->files->count() : 0 }}</span> File)
            </h4>

            <div id="form_files_preview" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @if ($isEdit && isset($arsip->files) && $arsip->files->count() > 0)
                    @foreach ($arsip->files as $file)
                        <div id="saved-file-card-{{ $file->id }}" class="flex items-center justify-between p-2.5 rounded-lg bg-surface border border-outline-variant text-xs shadow-sm">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <span class="material-symbols-outlined text-primary shrink-0">
                                    {{ $file->is_pdf ? 'picture_as_pdf' : ($file->is_image ? 'image' : 'description') }}
                                </span>
                                <a href="{{ $file->url }}" target="_blank" class="font-bold text-primary hover:underline truncate" title="{{ $file->nama_file }}">
                                    {{ $file->nama_file }}
                                </a>
                            </div>
                            <button type="button" 
                                    onclick="deleteSavedFile({{ $file->id }})"
                                    class="text-error hover:bg-error-container/40 p-1 rounded shrink-0"
                                    title="Hapus File Ini">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    @endforeach
                @else
                    <p id="empty_files_notice" class="text-xs text-on-surface-variant col-span-3 text-center py-4">Belum ada file terunggah. Silakan pilih atau drag & drop file pada kotak di atas.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
const currentArsipEditId = {{ $isEdit ? $arsip->id : 'null' }};
let uploadedCount = {{ $isEdit && isset($arsip->files) ? $arsip->files->count() : 0 }};

async function startFormBatchUpload(input) {
    if (!input.files || input.files.length === 0) return;

    const filesArr = Array.from(input.files);
    const total = filesArr.length;
    
    const progressContainer = document.getElementById('formUploadProgress');
    const progressStatus = document.getElementById('formUploadStatus');
    const progressPercent = document.getElementById('formUploadPercent');
    const progressBar = document.getElementById('formUploadBar');
    const previewContainer = document.getElementById('form_files_preview');
    const hiddenContainer = document.getElementById('hidden_uploaded_file_ids');
    const emptyNotice = document.getElementById('empty_files_notice');

    if (emptyNotice) emptyNotice.remove();

    progressContainer.classList.remove('hidden');
    progressBar.style.width = '0%';
    progressPercent.innerText = '0%';
    progressStatus.innerText = `Memulai mengunggah ${total} file...`;

    let successCount = 0;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    const targetUrl = currentArsipEditId 
        ? `/arsips/${currentArsipEditId}/upload-file`
        : `/arsips/upload-temp-file`;

    for (let i = 0; i < total; i++) {
        const file = filesArr[i];
        progressStatus.innerText = `Mengunggah (${i + 1}/${total}): ${file.name}...`;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', csrfToken);

        try {
            const res = await fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });

            if (res.ok) {
                const data = await res.json();
                if (data.success && data.file) {
                    successCount++;
                    uploadedCount++;
                    document.getElementById('form_files_count').innerText = uploadedCount;

                    // Tambahkan input hidden jika ini form Create baru
                    if (!currentArsipEditId) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'uploaded_file_ids[]';
                        hiddenInput.value = data.file.id;
                        hiddenContainer.appendChild(hiddenInput);
                    }

                    // Render card preview langsung terlihat di layar
                    const card = document.createElement('div');
                    card.id = `saved-file-card-${data.file.id}`;
                    card.className = 'flex items-center justify-between p-2.5 rounded-lg bg-surface border border-outline-variant text-xs shadow-sm border-l-4 border-l-primary';
                    card.innerHTML = `
                        <div class="flex items-center gap-2 overflow-hidden">
                            <span class="material-symbols-outlined text-primary shrink-0">
                                ${data.file.is_pdf ? 'picture_as_pdf' : (data.file.is_image ? 'image' : 'description')}
                            </span>
                            <div class="overflow-hidden">
                                <a href="${data.file.url}" target="_blank" class="font-bold text-primary hover:underline truncate block" title="${data.file.nama_file}">
                                    ${data.file.nama_file}
                                </a>
                                <span class="text-[10px] text-primary font-semibold">✓ Terunggah (${data.file.ukuran})</span>
                            </div>
                        </div>
                        <button type="button" onclick="deleteSavedFile(${data.file.id})" class="text-error hover:bg-error-container/40 p-1 rounded shrink-0" title="Hapus File">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    `;
                    previewContainer.appendChild(card);
                }
            }
        } catch (err) {
            console.error('Error upload file:', err);
        }

        const pct = Math.round(((i + 1) / total) * 100);
        progressBar.style.width = pct + '%';
        progressPercent.innerText = pct + '%';
    }

    progressStatus.innerText = `✅ Berhasil mengunggah ${successCount} dari ${total} file!`;
    setTimeout(() => {
        progressContainer.classList.add('hidden');
    }, 1500);

    // Reset file input agar bisa upload file lagi jika mau
    input.value = '';
}

async function deleteSavedFile(fileId) {
    if (!await showConfirm('Yakin ingin menghapus file lampiran ini?')) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        const res = await fetch(`/arsip-files/${fileId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        if (res.ok) {
            const cardEl = document.getElementById(`saved-file-card-${fileId}`);
            if (cardEl) cardEl.remove();
            
            if (uploadedCount > 0) uploadedCount--;
            document.getElementById('form_files_count').innerText = uploadedCount;
        }
    } catch (err) {
        console.error('Error delete file:', err);
    }
}
</script>