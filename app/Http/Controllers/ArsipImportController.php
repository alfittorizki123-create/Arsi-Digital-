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
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
            'file' => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
            'unit_id' => ['nullable', 'exists:units,id'],
        ], [], [
            'files' => 'file-file excel',
            'file' => 'file excel',
            'unit_id' => 'unit/UPT/UP',
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $uploadedFiles = $request->file('files');
        } elseif ($request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        if (empty($uploadedFiles)) {
            return redirect()->back()->with('error', 'Silakan pilih minimal 1 file Excel untuk diunggah.');
        }

        $unitId = $request->filled('unit_id') ? (int) $request->unit_id : null;

        $allUploadedSheets = [];
        foreach ($uploadedFiles as $uploadedExcel) {
            if (!$uploadedExcel->isValid()) continue;
            
            $fileName = $uploadedExcel->getClientOriginalName();
            $import = new ArsipPreviewImport;
            Excel::import($import, $uploadedExcel);

            $sheets = $import->sheets ?? [];
            foreach ($sheets as $sName => $rows) {
                // Disambiguate sheet names across multiple Excel files if needed
                $keyName = (count($uploadedFiles) > 1) ? "{$sName} ({$fileName})" : $sName;
                $allUploadedSheets[$keyName] = $rows;
            }
        }

        $sheets = $allUploadedSheets;
        if (empty($sheets)) {
            return redirect()->back()->with('error', 'File Excel kosong atau format tidak didukung.');
        }

        $preview = [];

        foreach ($sheets as $sheetName => $rows) {
            // Skip Rekap, Audit, dan Sheet Bulanan (misal: "Februari 2023", "REKAP ARSIP 2023")
            $upperSheetName = strtoupper(trim($sheetName));
            if (str_contains($upperSheetName, 'REKAP') || preg_match('/(JANUARI|FEBRUARI|MARET|APRIL|MEI|JUNI|JULI|AGUS|SEPT|OKT|NOV|DES)\s*\d{4}/i', $sheetName)) {
                continue;
            }
            if (str_contains($upperSheetName, 'DATAR AUDIT')) {
                continue;
            }

            // Coba cocokkan nama sheet dengan nama unit di database (Super Smart Fuzzy Matching)
            $matchedUnitId = null;
            $detectedUnitName = '-';

            $exactMap = [
                'UP TAPUNG HILIR' => 'UP Tapung Hilir',
                'UPT TAPUNG' => 'UP Tapung',
                'UP TAPUNG' => 'UP Tapung',
                'E-SAMSAT' => 'E-Samsat',
                'E SAMSAT' => 'E-Samsat',
                'UP BANDAR SEKIJANG' => 'UP Sekijang',
                'UP KEPENUHAN' => 'UP Kepenuhan',
                'UP SAMKEL 1' => 'UP Samsat Keliling 1',
                'UP SAMKEL 2' => 'UP Samsat Keliling 2',
                'SAMKEL INHU' => 'UP Samsat Keliling Inhu',
                'UP. KEMPAS' => 'UP Kempas Inhil',
                'UP. BELILAS' => 'UP Belilas',
                'UP.PINGGIR' => 'UP Pinggir',
                'UJUNG TANJUNG' => 'UP Ujung Tanjung',
                'UP AIR MOLEK' => 'UPT Air Molek',
                'UP. RUPAT' => 'UP Rupat',
                'KUBANG' => 'UPT Kubang',
                'SIAK' => 'UPT Siak',
                'BENGKALIS' => 'UPT Bengkalis',
                'TEMBILAHAN' => 'UPT Tembilahan',
                'RENGAT' => 'UPT Rengat',
                'BANGKINANG' => 'UPT Bangkinang',
                'UP PANGKALAN KURAS' => 'UP PKL Kuras',
                'UP UJUNG BATU' => 'UP Pengelolaan Pendapatan Ujung Batu',
                'RUMBAI' => 'UPT Rumbai',
                'UP DURI' => 'UP Duri',
                'PERAWANG' => 'UPT Perawang',
                'SLAT PNJNG' => 'UPT Selat Panjang',
                'PKU KOTA' => 'UPT Pekanbaru Kota',
                'SAMSAT MPP' => 'Samsat MPP',
                'SIMPANG TIGA' => 'UPT Simpang Tiga',
                'PANAM' => 'UPT Panam',
                'PELALAWAN' => 'UPT Pelalawan',
                'P PANGARAIAN' => 'UPT Pasir Pangaraian',
                'BAGA SIAPIAPI' => 'UPT Bagan Siapi-Api',
                'BAGAN BATU' => 'UPT Bagan Batu',
                'KUANTAN SINGINGI' => 'UPT Taluk Kuantan',
                'TELUK KUANTAN' => 'UPT Taluk Kuantan',
            ];

            $cleanUpperSheet = trim(strtoupper($sheetName));
            $foundUnit = null;
            $headerUnitText = null;

            // LANGKAH 1 (PRIORITAS UTAMA): Scan Teks Header Resmi Baris 1-8 ("UNIT KERJA : ...")
            for ($rIdx = 0; $rIdx < min(8, count($rows)); $rIdx++) {
                $rowArr = is_array($rows[$rIdx]) ? $rows[$rIdx] : $rows[$rIdx]->toArray();
                $rowText = strtoupper(implode(' ', array_map(function($v) { return is_scalar($v) ? (string)$v : ''; }, $rowArr)));
                
                // Cari teks khusus "UNIT KERJA : ..."
                if (preg_match('/UNIT\s+KERJA\s*:?\s*(.+)$/i', trim($rowText), $m)) {
                    $rawHeader = trim($m[1]);
                    if (preg_match('/^([^\n\r;]+?)(?=\s+(?:ORGANISASI|NO\b|KODE|KLASIFIKASI|KURUN|JUMLAH|$))/i', $rawHeader, $cut)) {
                        $rawHeader = $cut[1];
                    }
                    $rawHeader = preg_replace('/^[\s:\-\.]+/', '', $rawHeader);
                    $cleanHeader = trim(ucwords(strtolower($rawHeader)));
                    if (!empty($cleanHeader) && strlen($cleanHeader) >= 3) {
                        $headerUnitText = $cleanHeader;
                        break;
                    }
                }
            }

            // Jika ada Teks Header Resmi "UNIT KERJA", cari unit di database yang cocok dengan Teks Header tersebut
            if (!empty($headerUnitText)) {
                $upperHeader = strtoupper($headerUnitText);
                foreach (Unit::all() as $u) {
                    $cleanDbName = trim(str_replace(['UPT ', 'UP '], '', strtoupper($u->nama_unit)));
                    if (!empty($cleanDbName) && strlen($cleanDbName) >= 3 && str_contains($upperHeader, $cleanDbName)) {
                        $foundUnit = $u;
                        break;
                    }
                }
            }

            // LANGKAH 2: HANYA jika TIDAK ada Teks Header Resmi ("UNIT KERJA"), baru gunakan Nama Tab Sheet
            if (! $foundUnit && empty($headerUnitText)) {
                if (isset($exactMap[$cleanUpperSheet])) {
                    $foundUnit = Unit::where('nama_unit', $exactMap[$cleanUpperSheet])->first();
                }
            }

            // LANGKAH 3: Fuzzy search pada Nama Tab Sheet jika tidak ada Teks Header Resmi
            if (! $foundUnit && empty($headerUnitText)) {
                $cleanSheetName = trim(str_replace(['UPT ', 'UP '], '', strtoupper($sheetName)));
                foreach (Unit::all() as $u) {
                    $cleanDbName = trim(str_replace(['UPT ', 'UP '], '', strtoupper($u->nama_unit)));
                    if (!empty($cleanSheetName) && strlen($cleanSheetName) >= 3) {
                        if (str_contains($cleanDbName, $cleanSheetName) || str_contains($cleanSheetName, $cleanDbName)) {
                            $foundUnit = $u;
                            break;
                        }
                    }
                }
            }

            // LANGKAH 4 (AI GROQ): Panggil AI Groq jika belum ketemu
            $groqKey = config('services.groq.key') ?: env('GROQ_API_KEY');
            if (! $foundUnit && !empty($groqKey)) {
                $headerCombinedText = '';
                for ($rIdx = 0; $rIdx < min(8, count($rows)); $rIdx++) {
                    $rowArr = is_array($rows[$rIdx]) ? $rows[$rIdx] : $rows[$rIdx]->toArray();
                    $headerCombinedText .= implode(' ', array_map(function($v) { return is_scalar($v) ? (string)$v : ''; }, $rowArr)) . ' ';
                }

                $groqService = new \App\Services\GroqService();
                $allUnits = Unit::orderBy('nama_unit')->get(['id', 'nama_unit'])->toArray();
                $aiResult = $groqService->matchUnitWithAI($sheetName, $headerCombinedText, $allUnits);

                if (!empty($aiResult['matched_unit_id'])) {
                    $foundUnit = Unit::find($aiResult['matched_unit_id']);
                }
            }

            // Verifikasi Akhir: Jika ada Teks Header Resmi (UNIT KERJA), pastikan match tidak bertentangan!
            if (!empty($headerUnitText)) {
                $upperHeader = strtoupper($headerUnitText);
                if ($foundUnit) {
                    $cleanDb = trim(str_replace(['UPT ', 'UP '], '', strtoupper($foundUnit->nama_unit)));
                    if (!empty($cleanDb) && !str_contains($upperHeader, $cleanDb)) {
                        // Teks header resmi dokumen secara eksplisit menunjuk ke unit lain (misal KARTAMA vs PANDAU).
                        // Utamakan Teks Header Resmi dokumen!
                        $foundUnit = null;
                    }
                }
            }

            if ($foundUnit) {
                $matchedUnitId = $foundUnit->id;
                $detectedUnitName = $foundUnit->nama_unit;
            } elseif (!empty($headerUnitText)) {
                $matchedUnitId = '+new'; // Auto-select '+new' karena ini unit baru dari header resmi!
                $detectedUnitName = $headerUnitText;
            } else {
                $matchedUnitId = null;
                $detectedUnitName = '-';
            }

            $sheetMatchStatus = 'unmatched';
            if ($foundUnit) {
                if (strtoupper(trim($foundUnit->nama_unit)) === strtoupper(trim($sheetName))) {
                    $sheetMatchStatus = 'exact';
                } else {
                    $sheetMatchStatus = 'warning';
                }
            } elseif (!empty($headerUnitText)) {
                $sheetMatchStatus = 'warning'; // Unit baru dari header
            }

            $sheetSummary[$sheetName] = [
                'sheet_name' => $sheetName,
                'unit_id' => $matchedUnitId,
                'unit_name' => $detectedUnitName,
                'status' => $sheetMatchStatus,
                'total_rows' => 0,
                'valid_rows' => 0,
            ];

            $headerIndex = -1;
            $numberingRowIndex = -1;

            foreach ($rows as $index => $row) {
                $rowArray = $row->toArray();
                $normalizedKeys = array_map(function($val) {
                    return $this->normalizeHeader((string) $val);
                }, $rowArray);

                $numericCount = 0;
                foreach ($rowArray as $v) {
                    if (is_numeric($v) && (int)$v >= 1 && (int)$v <= 11) $numericCount++;
                }
                if ($numericCount >= 5) {
                    $numberingRowIndex = $index;
                    continue;
                }

                $matches = 0;
                if (in_array('kode_klasifikasi', $normalizedKeys) || in_array('kode', $normalizedKeys) || in_array('klasifikasi', $normalizedKeys)) $matches++;
                if (in_array('uraian_informasi_arsip', $normalizedKeys) || in_array('uraian', $normalizedKeys) || in_array('informasi', $normalizedKeys)) $matches++;
                if (in_array('kurun_waktu', $normalizedKeys) || in_array('tahun', $normalizedKeys)) $matches++;

                if ($matches >= 2) {
                    $headerIndex = $index;
                }
            }

            $skipToIndex = max($headerIndex, $numberingRowIndex);
            if ($skipToIndex === -1) {
                continue;
            }

            foreach ($rows as $index => $row) {
                if ($index <= $skipToIndex) continue;

                $line = $index + 1;
                $r = $row->toArray();

                $no = $r[0] ?? null;
                $kode = $r[1] ?? null;
                $noArsipBerkas = $r[2] ?? null;
                $uraian = $r[3] ?? null;
                $kurun = $r[4] ?? 2023;
                $jumlah = $r[5] ?? 1;
                $satuan = $r[6] ?? 'Berkas';
                $tingkat = $r[7] ?? null;
                $noBoks = $r[8] ?? null;
                $kondisi = $r[9] ?? null;
                $keamanan = $r[10] ?? null;

                $assocRow = [
                    'kode_klasifikasi' => $kode,
                    'nomor_arsip_berkas' => $noArsipBerkas,
                    'uraian_informasi_arsip' => $uraian,
                    'kurun_waktu' => $kurun,
                    'jumlah' => $jumlah,
                    'satuan' => $satuan,
                    'tingkat_perkembangan' => $tingkat,
                    'nomor_boks' => $noBoks,
                    'kondisi' => $kondisi,
                    'klasifikasi_keamanan' => $keamanan,
                ];

                if ($this->isSkipRow($assocRow)) {
                    continue;
                }

                $sheetSummary[$sheetName]['total_rows']++;

                $bulanInt = $this->normalizeBulan(null);
                if (preg_match('/bulan\s+([a-z]+)/i', (string)$uraian, $m)) {
                    $bulanInt = $this->normalizeBulan($m[1]);
                }

                $errors = [];
                $data = [
                    'kode_klasifikasi' => !empty($kode) ? trim((string)$kode) : '900.1.13.1',
                    'nomor_arsip_berkas' => !empty($noArsipBerkas) ? trim((string)$noArsipBerkas) : null,
                    'uraian_informasi_arsip' => trim((string)$uraian),
                    'kurun_waktu' => (int) $kurun,
                    'bulan' => $bulanInt,
                    'jumlah' => is_numeric($jumlah) ? (int) $jumlah : 1,
                    'satuan' => !empty($satuan) ? trim((string)$satuan) : 'Berkas',
                    'tingkat_perkembangan' => $this->normalizeTingkat($tingkat),
                    'nomor_boks' => !empty($noBoks) ? trim((string)$noBoks) : null,
                    'kondisi' => $this->normalizeKondisi($kondisi),
                    'klasifikasi_keamanan' => $this->normalizeKeamanan($keamanan),
                    'tipe_arsip' => $this->detectTipe($assocRow),
                    'status' => 'inaktif',
                    'unit_id' => $matchedUnitId,
                    'jenis_pajak_id' => null,
                ];

                $validator = Validator::make($data, [
                    'kurun_waktu' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
                    'bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
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
                if (! $matchedUnitId) {
                    $errors[] = 'Unit/UPT belum ditentukan untuk sheet ini.';
                }

                if (count($errors) === 0) {
                    $sheetSummary[$sheetName]['valid_rows']++;
                }

                $preview[] = [
                    'line' => $line,
                    'sheet' => $sheetName,
                    'unit_name' => $detectedUnitName,
                    'valid' => count($errors) === 0,
                    'errors' => $errors,
                    'data' => $data,
                ];
            }
        }

        // Filter out empty sheets (like "Sheet1") that have 0 total_rows
        $sheetSummary = array_filter($sheetSummary, function($s) {
            return ($s['total_rows'] ?? 0) > 0;
        });

        if (count($preview) === 0) {
            return redirect()
                ->route('arsips.import')
                ->with('error', 'File Excel kosong atau format kolom tidak dikenali.');
        }

        $token = Str::random(40);

        session([
            "arsip_import_preview.{$token}" => $preview,
            "arsip_import_unit_id.{$token}" => $unitId,
        ]);

        $validCount = collect($preview)->where('valid', true)->count();
        $errorCount = count($preview) - $validCount;
        $units = Unit::orderBy('nama_unit')->get();

        return view('arsips.import-preview', compact('preview', 'validCount', 'errorCount', 'sheetSummary', 'units', 'token'));
    }

    public function confirm(Request $request)
    {
        $token = $request->input('import_token');
        $preview = session("arsip_import_preview.{$token}");
        $unitId = session("arsip_import_unit_id.{$token}");

        if (! is_array($preview) || count($preview) === 0) {
            return redirect()
                ->route('arsips.import')
                ->with('error', 'Tidak ada data preview. Silakan unggah ulang file Excel.');
        }

        $rawSelected = $request->input('selected_sheets', []);
        $selectedSheets = array_map(function($v) {
            $decoded = base64_decode($v, true);
            return ($decoded !== false && base64_encode($decoded) === $v) ? $decoded : $v;
        }, $rawSelected);

        $rawUnits = $request->input('sheet_units', []);
        $sheetUnits = [];
        foreach ($rawUnits as $k => $v) {
            $decodedKey = base64_decode($k, true);
            $finalKey = ($decodedKey !== false && base64_encode($decodedKey) === $k) ? $decodedKey : $k;
            $sheetUnits[$finalKey] = $v;
        }

        // Buat unit baru jika pengguna memilih '+new'
        foreach ($sheetUnits as $sName => $uVal) {
            if ($uVal === '+new') {
                // Cari apakah ada detected unit_name dari preview untuk sheet ini
                $detectedName = null;
                foreach ($preview as $pRow) {
                    if ($pRow['sheet'] === $sName && !empty($pRow['unit_name']) && $pRow['unit_name'] !== '-') {
                        $detectedName = $pRow['unit_name'];
                        break;
                    }
                }

                $cleanUnitName = !empty($detectedName) ? $detectedName : ('UPT ' . ucwords(strtolower(trim(str_replace(['UPT', 'UP', '[', ']'], '', $sName)))));
                
                $existingUnit = Unit::where('nama_unit', $cleanUnitName)->first();
                if (!$existingUnit) {
                    $nextNum = (Unit::max('id') ?? 0) + 1;
                    $kodeUnit = 'UPT-' . sprintf('%03d', $nextNum);
                    
                    while (Unit::where('kode_unit', $kodeUnit)->exists()) {
                        $nextNum++;
                        $kodeUnit = 'UPT-' . sprintf('%03d', $nextNum);
                    }

                    $existingUnit = Unit::create([
                        'nama_unit' => $cleanUnitName,
                        'kode_unit' => $kodeUnit,
                    ]);
                }
                $sheetUnits[$sName] = (int) $existingUnit->id;
            }
        }

        $imported = 0;
        $skipped = 0;

        foreach ($preview as $row) {
            $sheetName = $row['sheet'];

            // Skip jika sheet di-uncheck oleh user
            if (!empty($selectedSheets) && !in_array($sheetName, $selectedSheets)) {
                $skipped++;
                continue;
            }

            $data = $row['data'];

            // Timpa unit_id dengan pilihan pengguna dari dropdown mapping sheet jika ada
            $targetUnitId = $sheetUnits[$sheetName] ?? $data['unit_id'] ?? null;
            if (!is_numeric($targetUnitId) || (int)$targetUnitId <= 0) {
                $skipped++;
                continue;
            }

            $data['unit_id'] = (int) $targetUnitId;

            // Otomatis buat / cari Boks berdasarkan nomor_boks dan kurun_waktu
            if (!empty($data['nomor_boks']) && !empty($data['kurun_waktu'])) {
                $boks = \App\Models\Boks::findOrCreateFromNomor($data['nomor_boks'], $data['kurun_waktu'], $data['unit_id']);
                if ($boks) $data['boks_id'] = $boks->id;
            }

            Arsip::create($data);
            $imported++;
        }

        $unitId = session("arsip_import_unit_id.{$token}");
        session()->forget(["arsip_import_preview.{$token}", "arsip_import_unit_id.{$token}"]);

        $route = $unitId ? route('arsips.index', ['unit_id' => $unitId]) : route('arsips.index');

        return redirect($route)->with('success', "Import selesai. Berhasil: {$imported}, dilewati: {$skipped}.");
    }

    public function previewAjax(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
        ], [], ['file' => 'file excel']);

        $uploaded = $request->file('file');
        if (!$uploaded || !$uploaded->isValid()) {
            return response()->json(['success' => false, 'error' => 'File tidak valid.'], 422);
        }

        $import = new \App\Imports\ArsipPreviewImport;
        \Maatwebsite\Excel\Facades\Excel::import($import, $uploaded);
        $sheets = $import->sheets ?? [];

        if (empty($sheets)) {
            return response()->json(['success' => false, 'error' => 'File Excel kosong atau format tidak didukung.'], 422);
        }

        // Proses file yang sama seperti preview()
        $token = Str::random(40);
        $allUploadedSheets = [];
        foreach (new \Illuminate\Support\Collection([$uploaded]) as $uploadedExcel) {
            $import2 = new \App\Imports\ArsipPreviewImport;
            \Maatwebsite\Excel\Facades\Excel::import($import2, $uploadedExcel);
            foreach ($import2->sheets ?? [] as $sName => $rows) {
                $allUploadedSheets[$sName] = $rows;
            }
        }
        $sheets = $allUploadedSheets;

        $preview = [];
        $sheetSummary = [];
        $matchedUnitId = null;
        $detectedUnitName = '-';

        foreach ($sheets as $sheetName => $rows) {
            $upperSheetName = strtoupper(trim($sheetName));
            if (str_contains($upperSheetName, 'REKAP') || preg_match('/(JANUARI|FEBRUARI|MARET|APRIL|MEI|JUNI|JULI|AGUS|SEPT|OKT|NOV|DES)\s*\d{4}/i', $sheetName)) continue;
            if (str_contains($upperSheetName, 'DATAR AUDIT')) continue;

            $foundUnit = null;
            $detectedUnitName = '-';
            $matchedUnitId = null;

            // Exact map
            $exactMap = ['UP TAPUNG HILIR' => 'UP Tapung Hilir', 'UPT TAPUNG' => 'UP Tapung', 'UP TAPUNG' => 'UP Tapung', 'E-SAMSAT' => 'E-Samsat', 'E SAMSAT' => 'E-Samsat', 'UP BANDAR SEKIJANG' => 'UP Sekijang', 'UP KEPENUHAN' => 'UP Kepenuhan', 'UP SAMKEL 1' => 'UP Samsat Keliling 1', 'UP SAMKEL 2' => 'UP Samsat Keliling 2', 'SAMKEL INHU' => 'UP Samsat Keliling Inhu', 'UP. KEMPAS' => 'UP Kempas Inhil', 'UP. BELILAS' => 'UP Belilas', 'UP.PINGGIR' => 'UP Pinggir', 'UJUNG TANJUNG' => 'UP Ujung Tanjung', 'UP AIR MOLEK' => 'UPT Air Molek', 'UP. RUPAT' => 'UP Rupat', 'KUBANG' => 'UPT Kubang', 'SIAK' => 'UPT Siak', 'BENGKALIS' => 'UPT Bengkalis', 'TEMBILAHAN' => 'UPT Tembilahan', 'RENGAT' => 'UPT Rengat', 'BANGKINANG' => 'UPT Bangkinang', 'UP PANGKALAN KURAS' => 'UP PKL Kuras', 'UP UJUNG BATU' => 'UP Pengelolaan Pendapatan Ujung Batu', 'RUMBAI' => 'UPT Rumbai', 'UP DURI' => 'UP Duri', 'PERAWANG' => 'UPT Perawang', 'SLAT PNJNG' => 'UPT Selat Panjang', 'PKU KOTA' => 'UPT Pekanbaru Kota', 'SAMSAT MPP' => 'Samsat MPP', 'SIMPANG TIGA' => 'UPT Simpang Tiga', 'PANAM' => 'UPT Panam', 'PELALAWAN' => 'UPT Pelalawan', 'P PANGARAIAN' => 'UPT Pasir Pangaraian', 'BAGA SIAPIAPI' => 'UPT Bagan Siapi-Api', 'BAGAN BATU' => 'UPT Bagan Batu', 'KUANTAN SINGINGI' => 'UPT Taluk Kuantan', 'TELUK KUANTAN' => 'UPT Taluk Kuantan'];

            $foundUnit = Unit::where('nama_unit', $exactMap[trim(strtoupper($sheetName))] ?? '')->first();
            if (!$foundUnit) {
                $cleanSheetName = trim(str_replace(['UPT ', 'UP '], '', strtoupper($sheetName)));
                foreach (Unit::all() as $u) {
                    $cleanDbName = trim(str_replace(['UPT ', 'UP '], '', strtoupper($u->nama_unit)));
                    if (!empty($cleanSheetName) && strlen($cleanSheetName) >= 3 && (str_contains($cleanDbName, $cleanSheetName) || str_contains($cleanSheetName, $cleanDbName))) { $foundUnit = $u; break; }
                }
            }

            if ($foundUnit) { $matchedUnitId = $foundUnit->id; $detectedUnitName = $foundUnit->nama_unit; }

            $sheetSummary[$sheetName] = [
                'sheet_name' => $sheetName, 'unit_id' => $matchedUnitId, 'unit_name' => $detectedUnitName,
                'status' => $foundUnit ? 'warning' : 'unmatched', 'total_rows' => 0, 'valid_rows' => 0,
            ];

            $headerIndex = -1;
            $numberingRowIndex = -1;

            foreach ($rows as $index => $row) {
                $rowArray = $row->toArray();
                $normalizedKeys = array_map(fn($v) => $this->normalizeHeader((string) $v), $rowArray);
                $numericCount = count(array_filter($rowArray, fn($v) => is_numeric($v) && (int)$v >= 1 && (int)$v <= 11));
                if ($numericCount >= 5) { $numberingRowIndex = $index; continue; }
                $matches = 0;
                if (in_array('kode_klasifikasi', $normalizedKeys) || in_array('kode', $normalizedKeys) || in_array('klasifikasi', $normalizedKeys)) $matches++;
                if (in_array('uraian_informasi_arsip', $normalizedKeys) || in_array('uraian', $normalizedKeys) || in_array('informasi', $normalizedKeys)) $matches++;
                if (in_array('kurun_waktu', $normalizedKeys) || in_array('tahun', $normalizedKeys)) $matches++;
                if ($matches >= 2) $headerIndex = $index;
            }

            $skipToIndex = max($headerIndex, $numberingRowIndex);
            if ($skipToIndex === -1) continue;

            foreach ($rows as $index => $row) {
                if ($index <= $skipToIndex) continue;
                $line = $index + 1;
                $r = $row->toArray();
                $kode = $r[1] ?? null; $uraian = $r[3] ?? null; $kurun = $r[4] ?? 2023;
                $jumlah = $r[5] ?? 1; $satuan = $r[6] ?? 'Berkas';
                $tingkat = $r[7] ?? null; $noBoks = $r[8] ?? null; $kondisi = $r[9] ?? null; $keamanan = $r[10] ?? null;

                $assocRow = ['kode_klasifikasi' => $kode, 'nomor_arsip_berkas' => $r[2] ?? null, 'uraian_informasi_arsip' => $uraian, 'kurun_waktu' => $kurun, 'jumlah' => $jumlah, 'satuan' => $satuan, 'tingkat_perkembangan' => $tingkat, 'nomor_boks' => $noBoks, 'kondisi' => $kondisi, 'klasifikasi_keamanan' => $keamanan];
                if ($this->isSkipRow($assocRow)) continue;
                $sheetSummary[$sheetName]['total_rows']++;

                $bulanInt = $this->normalizeBulan(null);
                if (preg_match('/bulan\s+([a-z]+)/i', (string)$uraian, $m)) $bulanInt = $this->normalizeBulan($m[1]);

                $errors = [];
                $data = [
                    'kode_klasifikasi' => !empty($kode) ? trim((string)$kode) : '900.1.13.1',
                    'nomor_arsip_berkas' => !empty($r[2] ?? null) ? trim((string)($r[2])) : null,
                    'uraian_informasi_arsip' => trim((string)$uraian), 'kurun_waktu' => (int)$kurun,
                    'bulan' => $bulanInt, 'jumlah' => is_numeric($jumlah) ? (int)$jumlah : 1,
                    'satuan' => !empty($satuan) ? trim((string)$satuan) : 'Berkas',
                    'tingkat_perkembangan' => $this->normalizeTingkat($tingkat),
                    'nomor_boks' => !empty($noBoks) ? trim((string)$noBoks) : null,
                    'kondisi' => $this->normalizeKondisi($kondisi),
                    'klasifikasi_keamanan' => $this->normalizeKeamanan($keamanan),
                    'tipe_arsip' => $this->detectTipe($assocRow), 'status' => 'inaktif', 'unit_id' => $matchedUnitId,
                ];

                $validator = \Illuminate\Support\Facades\Validator::make($data, [
                    'kurun_waktu' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
                    'bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
                    'jumlah' => ['nullable', 'integer', 'min:0'],
                    'tipe_arsip' => ['required', 'in:rekap,detail'],
                    'status' => ['required', 'in:aktif,inaktif'],
                ]);
                if ($validator->fails()) $errors = array_merge($errors, $validator->errors()->all());
                if (empty($data['uraian_informasi_arsip']) && empty($data['kode_klasifikasi'])) $errors[] = 'Uraian atau kode klasifikasi wajib diisi.';
                if (!$matchedUnitId) $errors[] = 'Unit/UPT belum ditentukan untuk sheet ini.';
                if (count($errors) === 0) $sheetSummary[$sheetName]['valid_rows']++;
                $preview[] = ['line' => $line, 'sheet' => $sheetName, 'unit_name' => $detectedUnitName, 'valid' => count($errors) === 0, 'errors' => $errors, 'data' => $data];
            }
        }

        $sheetSummary = array_filter($sheetSummary, fn($s) => ($s['total_rows'] ?? 0) > 0);

        if (count($preview) === 0) {
            return response()->json(['success' => false, 'error' => 'File Excel kosong atau format kolom tidak dikenali.'], 422);
        }

        session(["arsip_import_preview.{$token}" => $preview, "arsip_import_unit_id.{$token}" => null]);

        return response()->json([
            'success' => true,
            'token' => $token,
            'redirect' => route('arsips.import.show_preview', ['token' => $token]),
        ]);
    }

    public function showPreview($token)
    {
        $preview = session("arsip_import_preview.{$token}");

        if (!is_array($preview) || count($preview) === 0) {
            return redirect()->route('arsips.import')->with('error', 'Sesi preview tidak ditemukan.');
        }

        $validCount = collect($preview)->where('valid', true)->count();
        $errorCount = count($preview) - $validCount;
        $units = Unit::orderBy('nama_unit')->get();

        $sheetNames = collect($preview)->pluck('sheet')->unique();
        $sheetSummary = [];
        foreach ($sheetNames as $sName) {
            $rows = collect($preview)->where('sheet', $sName);
            $first = $rows->first();
            $detectedUnit = $first['unit_name'] ?? '-';
            $matchedId = null;
            if ($detectedUnit !== '-') {
                $unit = Unit::where('nama_unit', $detectedUnit)->first();
                $matchedId = $unit?->id ?? '+new';
            }
            $sheetSummary[$sName] = [
                'sheet_name' => $sName, 'unit_id' => $matchedId, 'unit_name' => $detectedUnit,
                'status' => $matchedId ? 'warning' : 'unmatched',
                'total_rows' => $rows->count(), 'valid_rows' => $rows->where('valid', true)->count(),
            ];
        }

        return view('arsips.import-preview', compact('preview', 'validCount', 'errorCount', 'sheetSummary', 'units', 'token'));
    }

    public function cancel()
    {
        $unitId = session('arsip_import_unit_id');
        session()->forget(['arsip_import_preview', 'arsip_import_unit_id']);

        $route = $unitId ? route('arsips.index', ['unit_id' => $unitId]) : route('arsips.import');

        return redirect($route)->with('success', 'Preview import dibatalkan.');
    }

    private function normalizeHeader(string $header): string
    {
        $header = Str::lower(trim($header));
        $header = str_replace([' ', '-', '/', '\\', '.'], '_', $header);
        $header = preg_replace('/_+/', '_', $header);

        return trim($header, '_');
    }

    private function isSkipRow(array $data): bool
    {
        $uraian = trim((string)($data['uraian_informasi_arsip'] ?? ''));
        $kode = trim((string)($data['kode_klasifikasi'] ?? ''));
        $noArsip = trim((string)($data['nomor_arsip_berkas'] ?? ''));

        if (empty($uraian) && empty($kode) && empty($noArsip)) {
            return true;
        }

        $rowText = strtolower(implode(' ', array_filter(array_map('strval', $data), 'is_string')));
        $skipWords = [
            'pekanbaru', 'yang memindahkan', 'yang menerima', 'sekretaris', 'selaku', 'nip.', 'nip ', 
            'kepala', 'penata', 'pembina', 'jumlah', '.....', '---', 'mengetahui',
            'pakar', 'selaku kepala', 'selaku ketua', 'hamdanil', 'ruslan'
        ];

        foreach ($skipWords as $word) {
            if (str_contains($rowText, $word)) {
                return true;
            }
        }

        // Must contain valid report keywords or bulan or "surat" or "laporan"
        if (!preg_match('/(laporan|surat|pertanggungjawaban|penerimaan|pajak|bulan|januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember|realisasi|daftar|skpd)/i', $rowText)) {
            return true;
        }

        return false;
    }

    private function normalizeTingkat($val): string
    {
        if (empty($val)) return 'Asli';
        $val = Str::lower(trim((string) $val));
        if (str_contains($val, 'asli') && str_contains($val, 'copy')) return 'Asli/Copy';
        if (str_contains($val, 'asli')) return 'Asli';
        if (str_contains($val, 'copy')) return 'Copy';
        return 'Asli';
    }

    private function normalizeBulan($val): ?int
    {
        if (empty($val)) return null;
        if (is_numeric($val) && (int) $val >= 1 && (int) $val <= 12) {
            return (int) $val;
        }

        $val = Str::lower(trim((string) $val));
        $months = [
            1 => ['jan', 'januari', 'january'],
            2 => ['feb', 'februari', 'february'],
            3 => ['mar', 'maret', 'march'],
            4 => ['apr', 'april'],
            5 => ['mei', 'may'],
            6 => ['jun', 'juni', 'june'],
            7 => ['jul', 'juli', 'july'],
            8 => ['agu', 'agust', 'agustus', 'august'],
            9 => ['sep', 'september'],
            10 => ['okt', 'oktober', 'october'],
            11 => ['nov', 'november'],
            12 => ['des', 'desember', 'december'],
        ];

        foreach ($months as $num => $names) {
            foreach ($names as $name) {
                if (str_starts_with($val, $name)) {
                    return $num;
                }
            }
        }

        return null;
    }

    private function normalizeKondisi($val): string
    {
        if (empty($val)) return 'Baik';
        $val = Str::lower(trim((string) $val));
        if (str_contains($val, 'rusak')) return 'Rusak';
        return 'Baik';
    }

    private function normalizeKeamanan($val): string
    {
        if (empty($val)) return 'Terbuka';
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
}