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
        return view('arsips.import');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [], ['file' => 'file excel']);

        $import = new ArsipPreviewImport;
        Excel::import($import, $request->file('file'));

        $rows = $import->rows ?? collect();
        $preview = [];
        $jenisMap = $this->buildJenisMap();
        $unitMap = $this->buildUnitMap();
        $existingNomor = Arsip::pluck('nomor_arsip')->map(fn ($n) => strtolower($n))->all();

        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $normalized = $this->normalizeRow($row->toArray());

            if ($this->isEmptyRow($normalized)) {
                continue;
            }

            $errors = [];
            $data = [
                'nomor_arsip' => $normalized['nomor_arsip'] ?? null,
                'nama_wajib_pajak' => $normalized['nama_wajib_pajak'] ?? null,
                'tahun_arsip' => $normalized['tahun_arsip'] ?? null,
                'nomor_rak' => $normalized['nomor_rak'] ?? null,
                'status' => $this->normalizeStatus($normalized['status'] ?? 'aktif'),
                'jenis_pajak_id' => null,
                'jenis_pajak_label' => $normalized['jenis_pajak'] ?? null,
                'unit_id' => null,
                'unit_label' => $normalized['unit'] ?? null,
            ];

            $validator = Validator::make($data, [
                'nomor_arsip' => ['required', 'string', 'max:100'],
                'nama_wajib_pajak' => ['required', 'string', 'max:255'],
                'tahun_arsip' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
                'nomor_rak' => ['nullable', 'string', 'max:50'],
                'status' => ['required', 'in:aktif,inaktif'],
            ]);

            if ($validator->fails()) {
                $errors = array_merge($errors, $validator->errors()->all());
            }

            if (! empty($data['nomor_arsip']) && in_array(strtolower($data['nomor_arsip']), $existingNomor, true)) {
                $errors[] = 'Nomor arsip sudah ada di database.';
            }

            $jenisKey = $this->lookupKey($normalized['jenis_pajak'] ?? '');
            if ($jenisKey === '') {
                $errors[] = 'Jenis pajak wajib diisi.';
            } elseif (! isset($jenisMap[$jenisKey])) {
                $errors[] = 'Jenis pajak tidak ditemukan: ' . ($normalized['jenis_pajak'] ?? '-');
            } else {
                $data['jenis_pajak_id'] = $jenisMap[$jenisKey]['id'];
                $data['jenis_pajak_label'] = $jenisMap[$jenisKey]['label'];
            }

            $unitKey = $this->lookupKey($normalized['unit'] ?? '');
            if ($unitKey === '') {
                $errors[] = 'Unit/UPT wajib diisi.';
            } elseif (! isset($unitMap[$unitKey])) {
                $errors[] = 'Unit/UPT tidak ditemukan: ' . ($normalized['unit'] ?? '-');
            } else {
                $data['unit_id'] = $unitMap[$unitKey]['id'];
                $data['unit_label'] = $unitMap[$unitKey]['label'];
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

            if (Arsip::where('nomor_arsip', $data['nomor_arsip'])->exists()) {
                $skipped++;
                continue;
            }

            Arsip::create([
                'nomor_arsip' => $data['nomor_arsip'],
                'jenis_pajak_id' => $data['jenis_pajak_id'],
                'nama_wajib_pajak' => $data['nama_wajib_pajak'],
                'tahun_arsip' => (int) $data['tahun_arsip'],
                'nomor_rak' => $data['nomor_rak'] ?: null,
                'status' => $data['status'],
                'unit_id' => $data['unit_id'],
            ]);

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
            'nomor_arsip' => $this->firstValue($map, [
                'nomor_arsip', 'no_arsip', 'nomor', 'no', 'nomorarsip',
            ]),
            'jenis_pajak' => $this->firstValue($map, [
                'jenis_pajak', 'jenis', 'kode_jenis_pajak', 'kode_pajak', 'jenispajak',
            ]),
            'nama_wajib_pajak' => $this->firstValue($map, [
                'nama_wajib_pajak', 'nama_wp', 'wajib_pajak', 'nama', 'namawajibpajak',
            ]),
            'tahun_arsip' => $this->firstValue($map, [
                'tahun_arsip', 'tahun', 'tahunarsip',
            ]),
            'nomor_rak' => $this->firstValue($map, [
                'nomor_rak', 'no_rak', 'rak', 'nomorrak',
            ]),
            'unit' => $this->firstValue($map, [
                'unit', 'unit_upt', 'kode_unit', 'nama_unit', 'upt', 'kodeunit',
            ]),
            'status' => $this->firstValue($map, [
                'status',
            ]),
        ];
    }

    private function normalizeHeader(string $header): string
    {
        $header = Str::lower(trim($header));
        $header = str_replace([' ', '-', '/', '\\'], '_', $header);
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

    private function isEmptyRow(array $data): bool
    {
        return empty($data['nomor_arsip'])
            && empty($data['nama_wajib_pajak'])
            && empty($data['jenis_pajak'])
            && empty($data['unit']);
    }

    private function normalizeStatus($status): string
    {
        $status = Str::lower(trim((string) $status));

        if (in_array($status, ['inaktif', 'inactive', 'nonaktif', '0'], true)) {
            return 'inaktif';
        }

        return 'aktif';
    }

    private function lookupKey(string $value): string
    {
        return Str::lower(trim($value));
    }

    private function buildJenisMap(): array
    {
        $map = [];
        foreach (JenisPajak::all() as $item) {
            $map[Str::lower($item->kode)] = [
                'id' => $item->id,
                'label' => $item->nama_jenis_pajak . ' (' . $item->kode . ')',
            ];
            $map[Str::lower($item->nama_jenis_pajak)] = [
                'id' => $item->id,
                'label' => $item->nama_jenis_pajak . ' (' . $item->kode . ')',
            ];
        }

        return $map;
    }

    private function buildUnitMap(): array
    {
        $map = [];
        foreach (Unit::all() as $item) {
            $map[Str::lower($item->kode_unit)] = [
                'id' => $item->id,
                'label' => $item->nama_unit . ' (' . $item->kode_unit . ')',
            ];
            $map[Str::lower($item->nama_unit)] = [
                'id' => $item->id,
                'label' => $item->nama_unit . ' (' . $item->kode_unit . ')',
            ];
        }

        return $map;
    }
}
