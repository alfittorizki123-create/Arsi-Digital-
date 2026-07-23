<?php

namespace App\Http\Controllers;

use App\Imports\ArsipPreviewImport;
use App\Models\Arsip;
use App\Models\JenisPajak;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ArsipImportController extends Controller
{
    public function create()
    {
        $units = Unit::orderBy('nama_unit')->get();

        return view('arsips.import', compact('units'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'unit_id' => ['nullable', 'exists:units,id'],
        ], [], [
            'file' => 'file excel',
            'unit_id' => 'unit/UPT/UP',
        ]);

        $unitId = $request->filled('unit_id') ? (int) $request->unit_id : null;

        $import = new ArsipPreviewImport;
        Excel::import($import, $request->file('file'));

        $rows = $import->rows ?? collect();
        $preview = [];
        $jenisMap = $this->buildJenisMap();
        $unitMap = $this->buildUnitMap();

        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $normalized = $this->normalizeRow($row->toArray());

            // Skip empty rows, total rows, signature rows
            if ($this->isSkipRow($normalized)) {
                continue;
            }

            $errors = [];
            $data = [
                'kode_klasifikasi' => $normalized['kode_klasifikasi'] ?? null,
                'nomor_arsip_berkas' => $normalized['nomor_arsip_berkas'] ?? null,
                'uraian_informasi_arsip' => $normalized['uraian_informasi_arsip'] ?? null,
                'kurun_waktu' => $normalized['kurun_waktu'] ?? null,
                'jumlah' => $normalized['jumlah'] ?? null,
                'satuan' => $normalized['satuan'] ?? 'Berkas',
                'tingkat_perkembangan' => $this->normalizeTingkat($normalized['tingkat_perkembangan'] ?? null),
                'nomor_boks' => $normalized['nomor_boks'] ?? null,
                'kondisi' => $this->normalizeKondisi($normalized['kondisi'] ?? null),
                'klasifikasi_keamanan' => $this->normalizeKeamanan($normalized['klasifikasi_keamanan'] ?? null),
                'tipe_arsip' => $this->detectTipe($normalized),
                'status' => 'inaktif',
                'unit_id' => $unitId,
                'jenis_pajak_id' => null,
            ];

            $validator = Validator::make($data, [
                'kurun_waktu' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
                'jumlah' => ['nullable', 'integer', 'min:0'],
                'tipe_arsip' => ['required', 'in:rekap,detail'],
                'status' => ['required', 'in:aktif,inaktif'],
            ]);

            if ($validator->fails()) {
                $errors = array_merge($errors, $validator->errors()->all());
            }
            if (empty($data['uraian_informasi_arsip']) && empty($data['kode_klasifikasi'])) {
                $errors[] = 'Uraian atau kode klasifikasi wajib diisi.';
            }
            if (! $unitId) {
                $errors[] = 'Unit/UPT belum dipilih (pilih sebelum upload).';
            }

            $preview[] = [
                'line' => $line,
                'valid' => count($errors) === 0,
                'errors' => $errors,
                'data' => $data,
            ];
        }

        if (count($preview) === 0) {
            return redirect()
                ->route('arsips.import')
                ->with('error', 'File Excel kosong atau format kolom tidak dikenali.');
        }

        session(['arsip_import_preview' => $preview]);

        $validCount = collect($preview)->where('valid', true)->count();
        $errorCount = count($preview) - $validCount;

        return view('arsips.import-preview', compact('preview', 'validCount', 'errorCount'));
    }

    public function confirm(Request $request)
    {
        $preview = session('arsip_import_preview');

        if (! is_array($preview) || count($preview) === 0) {
            return redirect()
                ->route('arsips.import')
                ->with('error', 'Tidak ada data preview. Silakan unggah ulang file Excel.');
        }

        $imported = 0;
        $skipped = 0;

        foreach ($preview as $row) {
            if (! $row['valid']) {
                $skipped++;
                continue;
            }

            $data = $row['data'];

            if (empty($data['satuan'])) $data['satuan'] = 'Berkas';
            if (empty($data['kondisi'])) $data['kondisi'] = 'Baik';
            if (empty($data['klasifikasi_keamanan'])) $data['klasifikasi_keamanan'] = 'Terbuka';

            Arsip::create($data);
            $imported++;
        }

        session()->forget('arsip_import_preview');

        return redirect()
            ->route('arsips.index')
            ->with('success', "Import selesai. Berhasil: {$imported}, dilewati: {$skipped}.");
    }

    public function cancel()
    {
        session()->forget('arsip_import_preview');

        return redirect()
            ->route('arsips.import')
            ->with('success', 'Preview import dibatalkan.');
    }

    private function normalizeRow(array $row): array
    {
        $map = [];
        foreach ($row as $key => $value) {
            $k = $this->normalizeHeader((string) $key);
            $map[$k] = is_string($value) ? trim($value) : $value;
        }

        return [
            'kode_klasifikasi' => $this->firstValue($map, ['kode_klasifikasi', 'kode', 'klasifikasi']),
            'nomor_arsip_berkas' => $this->firstValue($map, ['no_arsip_berkas', 'nomor_arsip_berkas', 'no_arsip', 'nomor', 'no']),
            'uraian_informasi_arsip' => $this->firstValue($map, ['uraian_informasi_arsip', 'uraian', 'informasi', 'uraian_informasi', 'deskripsi']),
            'kurun_waktu' => $this->firstValue($map, ['kurun_waktu', 'kurun', 'tahun', 'tahun_arsip']),
            'jumlah' => $this->firstValue($map, ['jumlah', 'jml']),
            'satuan' => $this->firstValue($map, ['satuan']),
            'tingkat_perkembangan' => $this->firstValue($map, ['tingkat_perkembangan', 'tingkat', 'perkembangan']),
            'nomor_boks' => $this->firstValue($map, ['no_boks', 'nomor_boks', 'no_boks_keterangan_no_boks', 'boks', 'no_boks_keterangan']),
            'kondisi' => $this->firstValue($map, ['kondisi_arsip', 'kondisi']),
            'klasifikasi_keamanan' => $this->firstValue($map, ['klasifikasi_keamanan_dan_akses_arsip', 'klasifikasi_keamanan', 'keamanan']),
        ];
    }

    private function normalizeHeader(string $header): string
    {
        $header = Str::lower(trim($header));
        $header = str_replace([' ', '-', '/', '\\', '.'], '_', $header);
        $header = preg_replace('/_+/', '_', $header);

        return trim($header, '_');
    }

    private function firstValue(array $map, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $map) && $map[$key] !== null && $map[$key] !== '') {
                return $map[$key];
            }
        }

        return null;
    }

    private function isSkipRow(array $data): bool
    {
        $uraian = strtolower((string) ($data['uraian_informasi_arsip'] ?? ''));
        if (str_contains($uraian, 'jumlah')) return true;
        if (str_contains($uraian, 'pekanbaru,')) return true;
        if (str_contains($uraian, 'yang memindahkan')) return true;
        if (str_contains($uraian, 'sekretaris')) return true;
        if (str_contains($uraian, 'selaku')) return true;
        if (str_contains($uraian, 'nip.')) return true;
        if (str_contains($uraian, 'kepala')) return true;

        return empty($data['kode_klasifikasi'])
            && empty($data['uraian_informasi_arsip'])
            && empty($data['kurun_waktu']);
    }

    private function normalizeTingkat($val): ?string
    {
        if (empty($val)) return null;
        $val = Str::lower(trim((string) $val));
        if (str_contains($val, 'asli') && str_contains($val, 'copy')) return 'Asli/Copy';
        if (str_contains($val, 'asli')) return 'Asli';
        if (str_contains($val, 'copy')) return 'Copy';
        return null;
    }

    private function normalizeKondisi($val): ?string
    {
        if (empty($val)) return null;
        $val = Str::lower(trim((string) $val));
        if (str_contains($val, 'rusak')) return 'Rusak';
        return 'Baik';
    }

    private function normalizeKeamanan($val): ?string
    {
        if (empty($val)) return null;
        $val = Str::lower(trim((string) $val));
        if (str_contains($val, 'rahasia')) return 'Rahasia';
        if (str_contains($val, 'terbatas')) return 'Terbatas';
        return 'Terbuka';
    }

    private function detectTipe(array $data): string
    {
        $uraian = strtolower((string) ($data['uraian_informasi_arsip'] ?? ''));
        $jumlah = (int) ($data['jumlah'] ?? 0);

        // Rekap: uraian mengandung "Boks" dan jumlah > 1
        if (str_contains($uraian, 'boks') && $jumlah > 1) {
            return 'rekap';
        }

        return 'detail';
    }

    private function buildJenisMap(): array
    {
        $map = [];
        foreach (JenisPajak::all() as $item) {
            $map[Str::lower($item->kode)] = $item->id;
            $map[Str::lower($item->nama_jenis_pajak)] = $item->id;
        }

        return $map;
    }

    private function buildUnitMap(): array
    {
        $map = [];
        foreach (Unit::all() as $item) {
            $map[Str::lower($item->kode_unit)] = $item->id;
            $map[Str::lower($item->nama_unit)] = $item->id;
        }

        return $map;
    }
}