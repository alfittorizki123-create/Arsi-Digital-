<?php

namespace App\Http\Controllers;

use App\Exports\ArsipExport;
use App\Models\Arsip;
use App\Models\JenisPajak;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['jenis_pajak_id', 'unit_id', 'status', 'kurun_waktu', 'tipe_arsip', 'kondisi', 'klasifikasi_keamanan']);

        $query = Arsip::with(['jenisPajak', 'unit'])->latest();
        $this->applyFilters($query, $filters);
        $arsips = $query->paginate(20)->withQueryString();

        $rekapUnit = DB::table('arsips')
            ->join('units', 'arsips.unit_id', '=', 'units.id')
            ->select('units.id', 'units.nama_unit', 'units.kode_unit', DB::raw('count(arsips.id) as total'), DB::raw('coalesce(sum(arsips.jumlah),0) as total_berkas'))
            ->when($filters['jenis_pajak_id'] ?? null, fn ($q, $v) => $q->where('arsips.jenis_pajak_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('arsips.status', $v))
            ->when($filters['kurun_waktu'] ?? null, fn ($q, $v) => $q->where('arsips.kurun_waktu', $v))
            ->when($filters['tipe_arsip'] ?? null, fn ($q, $v) => $q->where('arsips.tipe_arsip', $v))
            ->when($filters['kondisi'] ?? null, fn ($q, $v) => $q->where('arsips.kondisi', $v))
            ->when($filters['klasifikasi_keamanan'] ?? null, fn ($q, $v) => $q->where('arsips.klasifikasi_keamanan', $v))
            ->when($filters['unit_id'] ?? null, fn ($q, $v) => $q->where('arsips.unit_id', $v))
            ->groupBy('units.id', 'units.nama_unit', 'units.kode_unit')
            ->orderByDesc('total')
            ->limit(15)
            ->get();

        $rekapTahun = DB::table('arsips')
            ->select('kurun_waktu', DB::raw('count(id) as total'), DB::raw('coalesce(sum(jumlah),0) as total_berkas'))
            ->when($filters['jenis_pajak_id'] ?? null, fn ($q, $v) => $q->where('jenis_pajak_id', $v))
            ->when($filters['unit_id'] ?? null, fn ($q, $v) => $q->where('unit_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['tipe_arsip'] ?? null, fn ($q, $v) => $q->where('tipe_arsip', $v))
            ->when($filters['kondisi'] ?? null, fn ($q, $v) => $q->where('kondisi', $v))
            ->when($filters['klasifikasi_keamanan'] ?? null, fn ($q, $v) => $q->where('klasifikasi_keamanan', $v))
            ->when($filters['kurun_waktu'] ?? null, fn ($q, $v) => $q->where('kurun_waktu', $v))
            ->groupBy('kurun_waktu')
            ->orderByDesc('kurun_waktu')
            ->get();

        $rekapTipe = DB::table('arsips')
            ->select('tipe_arsip', DB::raw('count(id) as total'), DB::raw('coalesce(sum(jumlah),0) as total_berkas'))
            ->when($filters['jenis_pajak_id'] ?? null, fn ($q, $v) => $q->where('jenis_pajak_id', $v))
            ->when($filters['unit_id'] ?? null, fn ($q, $v) => $q->where('unit_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['kurun_waktu'] ?? null, fn ($q, $v) => $q->where('kurun_waktu', $v))
            ->when($filters['kondisi'] ?? null, fn ($q, $v) => $q->where('kondisi', $v))
            ->when($filters['klasifikasi_keamanan'] ?? null, fn ($q, $v) => $q->where('klasifikasi_keamanan', $v))
            ->when($filters['tipe_arsip'] ?? null, fn ($q, $v) => $q->where('tipe_arsip', $v))
            ->groupBy('tipe_arsip')
            ->get();

        $statusQuery = Arsip::query();
        $this->applyFilters($statusQuery, array_diff_key($filters, ['status' => true]));
        $rekapStatus = [
            'aktif' => (clone $statusQuery)->where('status', 'aktif')->count(),
            'inaktif' => (clone $statusQuery)->where('status', 'inaktif')->count(),
        ];

        $jenisPajaks = JenisPajak::orderBy('nama_jenis_pajak')->get();
        $units = Unit::orderBy('nama_unit')->get();
        $tahuns = Arsip::select('kurun_waktu')->distinct()->orderByDesc('kurun_waktu')->pluck('kurun_waktu');

        return view('laporan.index', compact(
            'arsips',
            'rekapUnit',
            'rekapTahun',
            'rekapTipe',
            'rekapStatus',
            'jenisPajaks',
            'units',
            'tahuns',
            'filters'
        ));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['jenis_pajak_id', 'unit_id', 'status', 'kurun_waktu', 'tipe_arsip', 'kondisi', 'klasifikasi_keamanan']);
        $filename = 'laporan-arsip-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new ArsipExport($filters), $filename);
    }

    private function applyFilters($query, array $filters): void
    {
        if (! empty($filters['jenis_pajak_id'])) $query->where('jenis_pajak_id', $filters['jenis_pajak_id']);
        if (! empty($filters['unit_id'])) $query->where('unit_id', $filters['unit_id']);
        if (! empty($filters['status'])) $query->where('status', $filters['status']);
        if (! empty($filters['kurun_waktu'])) $query->where('kurun_waktu', $filters['kurun_waktu']);
        if (! empty($filters['tipe_arsip'])) $query->where('tipe_arsip', $filters['tipe_arsip']);
        if (! empty($filters['kondisi'])) $query->where('kondisi', $filters['kondisi']);
        if (! empty($filters['klasifikasi_keamanan'])) $query->where('klasifikasi_keamanan', $filters['klasifikasi_keamanan']);
    }
}