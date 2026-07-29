@extends('layouts.app')

@section('title', 'Import Excel')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-stack-md text-center">
            <h2 class="font-display-md text-display-md text-on-surface">Import Data Arsip dari Excel</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Langkah 1: pilih file Excel. Sistem akan membaca isi file terlebih dahulu, belum langsung menyimpan ke database.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-stack-md">
            <div class="rounded-xl border border-primary/30 bg-primary-fixed/20 p-4">
                <div class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold mx-auto mb-2">1</div>
                <p class="text-sm font-bold text-center text-on-surface">Pilih File</p>
                <p class="text-xs text-center text-on-surface-variant mt-1">Klik tombol pilih file Excel.</p>
            </div>
            <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4">
                <div class="w-8 h-8 rounded-full bg-surface-container text-on-surface flex items-center justify-center font-bold mx-auto mb-2">2</div>
                <p class="text-sm font-bold text-center text-on-surface">Periksa Preview</p>
                <p class="text-xs text-center text-on-surface-variant mt-1">Cek nama sheet dan tujuan UP/UPT.</p>
            </div>
            <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4">
                <div class="w-8 h-8 rounded-full bg-surface-container text-on-surface flex items-center justify-center font-bold mx-auto mb-2">3</div>
                <p class="text-sm font-bold text-center text-on-surface">Konfirmasi</p>
                <p class="text-xs text-center text-on-surface-variant mt-1">Data tersimpan setelah klik konfirmasi.</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-stack-lg shadow-sm">
            <div class="mb-stack-md">
                <label class="block text-label-md font-bold text-on-surface mb-2">
                    Pilih File Excel Pemindahan <span class="text-error">*</span>
                </label>
                <div class="p-6 border-2 border-dashed border-emerald-500/40 rounded-2xl bg-surface-container-lowest hover:bg-emerald-500/5 transition-all">
                    <input type="file" name="file" id="files" required
                           class="w-full text-sm file:mr-4 file:py-2.5 file:px-5 file:rounded-lg file:border-0 file:bg-emerald-500 file:text-white file:font-bold file:text-xs file:cursor-pointer cursor-pointer"
                           onchange="uploadExcel(this)">
                    <p class="text-xs text-on-surface-variant mt-3 text-center">Klik tombol di atas untuk pilih file Excel. Setelah dipilih, sistem membuka halaman preview untuk diperiksa dulu.</p>
                </div>
                <div id="selected_excel_preview" class="mt-4 flex flex-wrap gap-2"></div>
                <div id="import_loading" class="mt-4 hidden">
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-primary-fixed/20 border border-primary/30">
                        <span class="material-symbols-outlined text-primary animate-spin">progress_activity</span>
                        <span class="text-sm font-bold text-primary">Membaca file Excel, mohon tunggu sebentar...</span>
                    </div>
                </div>
                <div id="import_error" class="mt-4 hidden">
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-error-container/20 border border-error/30">
                        <span class="material-symbols-outlined text-error">error</span>
                        <span id="import_error_text" class="text-sm font-bold text-error"></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-stack-md border-t border-outline-variant">
                <a href="{{ route('arsips.pilih_unit') }}" class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-bold text-xs hover:bg-surface-container transition-colors">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <script>
    async function uploadExcel(input) {
        const file = input.files?.[0];
        if (!file) return;

        const preview = document.getElementById('selected_excel_preview');
        preview.innerHTML = '';
        const badge = document.createElement('div');
        badge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-700 border border-emerald-500/30 shadow-xs';
        badge.innerHTML = `<span class="material-symbols-outlined text-sm text-emerald-600">table_chart</span> ${file.name} (${(file.size/1024).toFixed(1)} KB)`;
        preview.appendChild(badge);

        const loading = document.getElementById('import_loading');
        const errorEl = document.getElementById('import_error');
        const errorText = document.getElementById('import_error_text');
        loading.classList.remove('hidden');
        errorEl.classList.add('hidden');

        // Jeda kecil untuk Android Google Drive (biar file selesai di-download dulu)
        await new Promise(r => setTimeout(r, 300));

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', csrfToken);

        try {
            const res = await fetch('{{ route('arsips.import.preview_ajax', [], false) }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!res.ok) {
                let msg = 'Gagal memproses file (HTTP ' + res.status + ').';
                try {
                    const errData = await res.json();
                    if (errData.error) msg = errData.error;
                    if (errData.message) msg = errData.message;
                } catch (e) {}
                throw new Error(msg);
            }

            const data = await res.json();

            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            } else {
                throw new Error(data.error || 'Format tidak dikenali.');
            }
        } catch (err) {
            loading.classList.add('hidden');
            errorText.innerText = err.message || 'Gagal upload. Coba lagi.';
            errorEl.classList.remove('hidden');
            showToast('error', err.message || 'Gagal upload.');
        }
    }
    </script>
@endsection
