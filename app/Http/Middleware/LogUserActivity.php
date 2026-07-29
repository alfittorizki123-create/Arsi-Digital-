<?php

namespace App\Http\Middleware;

use App\Models\Unit;
use App\Services\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $beforeData = $this->getBeforeData($request);

        $response = $next($request);

        if (auth()->check() && $this->shouldLog($request, $response)) {
            ActivityLogger::log(
                $this->getAction($request),
                $this->getModule($request),
                $this->getDescription($request, $beforeData),
                [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'route' => optional($request->route())->getName(),
                    'status' => $response->getStatusCode(),
                    'input' => $this->safeInput($request),
                ],
                $request
            );
        }

        return $response;
    }

    private function shouldLog(Request $request, Response $response): bool
    {
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        if ($request->routeIs('logout')) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        return ! str_starts_with((string) optional($request->route())->getName(), 'logs.');
    }

    private function getAction(Request $request): string
    {
        if ($request->isMethod('DELETE')) {
            return 'hapus';
        }

        if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
            return 'edit';
        }

        $routeName = (string) optional($request->route())->getName();

        if (str_contains($routeName, 'import')) {
            return 'import';
        }

        if (str_contains($routeName, 'upload')) {
            return 'upload';
        }

        if (str_contains($routeName, 'kembalikan')) {
            return 'kembalikan';
        }

        return 'tambah';
    }

    private function getModule(Request $request): string
    {
        $routeName = (string) optional($request->route())->getName();

        return match (true) {
            str_starts_with($routeName, 'arsips.') => 'Arsip',
            str_starts_with($routeName, 'arsip-files.') => 'File Arsip',
            str_starts_with($routeName, 'peminjaman.') => 'Peminjaman',
            str_starts_with($routeName, 'raks.') => 'Rak',
            str_starts_with($routeName, 'boks.') => 'Boks',
            str_starts_with($routeName, 'unit.') => 'Unit',
            str_starts_with($routeName, 'jenis-pajak.') => 'Jenis Pajak',
            default => ucfirst(explode('.', $routeName)[0] ?: 'Sistem'),
        };
    }

    private function getDescription(Request $request, array $beforeData = []): string
    {
        $action = $this->getAction($request);
        $module = $this->getModule($request);
        $input = $this->safeInput($request);

        if ($module === 'Arsip') {
            return $this->getArsipDescription($action, $input, $beforeData);
        }

        if ($module === 'Boks') {
            return $this->getBoksDescription($action, $input);
        }

        if ($module === 'Rak') {
            return $this->getRakDescription($action, $input);
        }

        if ($module === 'Peminjaman') {
            return $this->getPeminjamanDescription($action, $input);
        }

        return "{$this->actionLabel($action)} data {$module}";
    }

    private function getBeforeData(Request $request): array
    {
        $routeName = (string) optional($request->route())->getName();

        if ($request->isMethod('PUT') || $request->isMethod('PATCH') || $request->isMethod('DELETE')) {
            if (str_starts_with($routeName, 'arsips.')) {
                $arsip = $request->route('arsip');

                if ($arsip) {
                    $arsip->loadMissing('unit');

                    return [
                        'tipe_arsip' => $arsip->tipe_arsip,
                        'kode_klasifikasi' => $arsip->kode_klasifikasi,
                        'nomor_arsip_berkas' => $arsip->nomor_arsip_berkas,
                        'uraian_informasi_arsip' => $arsip->uraian_informasi_arsip,
                        'kurun_waktu' => $arsip->kurun_waktu,
                        'bulan' => $arsip->bulan,
                        'jumlah' => $arsip->jumlah,
                        'satuan' => $arsip->satuan,
                        'tingkat_perkembangan' => $arsip->tingkat_perkembangan,
                        'nomor_boks' => $arsip->nomor_boks,
                        'kondisi' => $arsip->kondisi,
                        'klasifikasi_keamanan' => $arsip->klasifikasi_keamanan,
                        'status' => $arsip->status,
                        'unit_id' => $arsip->unit_id,
                        'unit_name' => $arsip->unit?->nama_unit,
                    ];
                }
            }
        }

        return [];
    }

    private function getArsipDescription(string $action, array $input, array $beforeData = []): string
    {
        if ($action === 'edit' && ! empty($beforeData)) {
            $changes = $this->changedFields($input, $beforeData);

            if (! empty($changes)) {
                return 'Mengubah arsip: ' . implode('; ', $changes);
            }
        }

        if ($action === 'hapus' && ! empty($beforeData)) {
            return 'Menghapus arsip ' . $this->arsipIdentity($beforeData);
        }

        $parts = [];

        if (! empty($input['bulan'])) {
            $parts[] = 'bulan laporan ' . $this->bulanName((int) $input['bulan']);
        }

        if (! empty($input['kurun_waktu'])) {
            $parts[] = 'tahun ' . $input['kurun_waktu'];
        }

        if (! empty($input['unit_id'])) {
            $parts[] = 'dari ' . $this->unitName($input['unit_id']);
        }

        if (! empty($input['nomor_boks'])) {
            $parts[] = 'boks ' . $input['nomor_boks'];
        }

        if (! empty($input['uraian_informasi_arsip'])) {
            $parts[] = 'uraian "' . $this->limitText($input['uraian_informasi_arsip'], 80) . '"';
        }

        $detail = empty($parts) ? 'data arsip' : 'arsip ' . implode(', ', $parts);

        return "{$this->actionLabel($action)} {$detail}";
    }

    private function changedFields(array $input, array $beforeData): array
    {
        $labels = [
            'bulan' => 'bulan laporan',
            'kurun_waktu' => 'tahun laporan',
            'unit_id' => 'unit/UPT',
            'nomor_boks' => 'nomor boks',
            'status' => 'status',
            'kondisi' => 'kondisi',
            'jumlah' => 'jumlah berkas',
            'satuan' => 'satuan',
            'kode_klasifikasi' => 'kode klasifikasi',
            'nomor_arsip_berkas' => 'nomor arsip/berkas',
            'klasifikasi_keamanan' => 'klasifikasi keamanan',
            'tingkat_perkembangan' => 'tingkat perkembangan',
            'uraian_informasi_arsip' => 'uraian informasi arsip',
        ];

        $changes = [];

        foreach ($labels as $field => $label) {
            if (! array_key_exists($field, $input)) {
                continue;
            }

            $oldValue = $beforeData[$field] ?? null;
            $newValue = $input[$field] ?? null;

            if ((string) $oldValue === (string) $newValue) {
                continue;
            }

            $changes[] = "{$label} dari {$this->formatFieldValue($field, $oldValue, $beforeData)} menjadi {$this->formatFieldValue($field, $newValue)}";
        }

        return $changes;
    }

    private function arsipIdentity(array $data): string
    {
        $parts = [];

        if (! empty($data['bulan'])) {
            $parts[] = 'bulan laporan ' . $this->bulanName((int) $data['bulan']);
        }

        if (! empty($data['kurun_waktu'])) {
            $parts[] = 'tahun ' . $data['kurun_waktu'];
        }

        if (! empty($data['unit_name'])) {
            $parts[] = 'dari ' . $data['unit_name'];
        }

        if (! empty($data['nomor_boks'])) {
            $parts[] = 'boks ' . $data['nomor_boks'];
        }

        if (! empty($data['uraian_informasi_arsip'])) {
            $parts[] = 'uraian "' . $this->limitText($data['uraian_informasi_arsip'], 80) . '"';
        }

        return empty($parts) ? 'terpilih' : implode(', ', $parts);
    }

    private function formatFieldValue(string $field, $value, array $beforeData = []): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if ($field === 'bulan') {
            return $this->bulanName((int) $value);
        }

        if ($field === 'unit_id') {
            return ! empty($beforeData['unit_name']) && (string) ($beforeData['unit_id'] ?? '') === (string) $value
                ? $beforeData['unit_name']
                : $this->unitName($value);
        }

        if ($field === 'uraian_informasi_arsip') {
            return '"' . $this->limitText((string) $value, 80) . '"';
        }

        return '"' . $this->limitText((string) $value, 80) . '"';
    }

    private function getBoksDescription(string $action, array $input): string
    {
        $parts = [];

        if (! empty($input['nomor_boks'])) {
            $parts[] = 'nomor boks ' . $input['nomor_boks'];
        }

        if (! empty($input['tahun'])) {
            $parts[] = 'tahun ' . $input['tahun'];
        }

        if (! empty($input['unit_id'])) {
            $parts[] = 'untuk ' . $this->unitName($input['unit_id']);
        }

        $detail = empty($parts) ? 'data boks' : 'boks ' . implode(', ', $parts);

        return "{$this->actionLabel($action)} {$detail}";
    }

    private function getRakDescription(string $action, array $input): string
    {
        $namaRak = $input['nama_rak'] ?? $input['nomor_rak'] ?? $input['kode_rak'] ?? null;

        return $namaRak
            ? "{$this->actionLabel($action)} rak {$namaRak}"
            : "{$this->actionLabel($action)} data rak";
    }

    private function getPeminjamanDescription(string $action, array $input): string
    {
        $nama = $input['nama_peminjam'] ?? $input['peminjam'] ?? null;
        $keperluan = $input['keperluan'] ?? null;

        $detail = $nama ? "peminjaman oleh {$nama}" : 'data peminjaman';

        if ($keperluan) {
            $detail .= ' untuk ' . $this->limitText($keperluan, 80);
        }

        return "{$this->actionLabel($action)} {$detail}";
    }

    private function actionLabel(string $action): string
    {
        return match ($action) {
            'tambah' => 'Menambahkan',
            'edit' => 'Mengubah',
            'hapus' => 'Menghapus',
            'import' => 'Mengimpor',
            'upload' => 'Mengunggah',
            'kembalikan' => 'Mengembalikan',
            default => ucfirst($action),
        };
    }

    private function bulanName(int $bulan): string
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ][$bulan] ?? (string) $bulan;
    }

    private function unitName($unitId): string
    {
        $unit = Unit::find($unitId);

        return $unit?->nama_unit ?: 'unit #' . $unitId;
    }

    private function limitText(string $text, int $limit): string
    {
        return strlen($text) > $limit ? substr($text, 0, $limit) . '...' : $text;
    }

    private function safeInput(Request $request): array
    {
        return collect($request->except([
            'password',
            'password_confirmation',
            '_token',
            '_method',
        ]))->map(function ($value) {
            if (is_string($value) && strlen($value) > 300) {
                return substr($value, 0, 300) . '...';
            }

            return $value;
        })->toArray();
    }
}