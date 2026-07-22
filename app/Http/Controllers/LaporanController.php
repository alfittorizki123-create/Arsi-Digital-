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
        $filters = $request->only(['jenis_pajak_id', 'unit_id', 'status', 'tahun']);

        $query = Arsip::with(['jenisPajak', 'unit'])->latest();
        $this->applyFilters($query, $filters);
        $arsips = $query->paginate(20)->withQueryString();

        $rekapJenis = DB::table('arsips')
            ->join('jenis_pajaks', 'arsips.jenis_pajak_id', '=', 'jenis_pajaks.id')
            ->select('jenis_pajaks.id', 'jenis_pajaks.nama_jenis_pajak', 'jenis_pajaks.kode', DB::raw('count(arsips.id) as total'))
            ->when($filters['unit_id'] ?? null, fn ($q, $v) => $q->where('arsips.unit_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('arsips.status', $v))
            ->when($filters['tahun'] ?? null, fn ($q, $v) => $q->where('arsips.tahun_arsip', $v))
            ->when($filters['jenis_pajak_id'] ?? null, fn ($q, $v) => $q->where('arsips.jenis_pajak_id', $v))
            ->groupBy('jenis_pajaks.id', 'jenis_pajaks.nama_jenis_pajak', 'jenis_pajaks.kode')
            ->orderByDesc('total')
            ->get();

        $rekapUnit = DB::table('arsips')
            ->join('units', 'arsips.unit_id', '=', 'units.id')
            ->select('units.id', 'units.nama_unit', 'units.kode_unit', DB::raw('count(arsips.id) as total'))
            ->when($filters['jenis_pajak_id'] ?? null, fn ($q, $v) => $q->where('arsips.jenis_pajak_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('arsips.status', $v))
            ->when($filters['tahun'] ?? null, fn ($q, $v) => $q->where('arsips.tahun_arsip', $v))
            ->when($filters['unit_id'] ?? null, fn ($q, $v) => $q->where('arsips.unit_id', $v))
            ->groupBy('units.id', 'units.nama_unit', 'units.kode_unit')
            ->orderByDesc('total')
            ->limit(15)
            ->get();

        $rekapTahun = DB::table('arsips')
            ->select('tahun_arsip', DB::raw('count(id) as total'))
            ->when($filters['jenis_pajak_id'] ?? null, fn ($q, $v) => $q->where('jenis_pajak_id', $v))
            ->when($filters['unit_id'] ?? null, fn ($q, $v) => $q->where('unit_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['tahun'] ?? null, fn ($q, $v) => $q->where('tahun_arsip', $v))
            ->groupBy('tahun_arsip')
            ->orderByDesc('tahun_arsip')
            ->get();

        $statusQuery = Arsip::query();
        $this->applyFilters($statusQuery, array_diff_key($filters, ['status' => true]));
        $rekapStatus = [
            'aktif' => (clone $statusQuery)->where('status', 'aktif')->count(),
            'inaktif' => (clone $statusQuery)->where('status', 'inaktif')->count(),
        ];

        $jenisPajaks = JenisPajak::orderBy('nama_jenis_pajak')->get();
        $units = Unit::orderBy('nama_unit')->get();
        $tahuns = Arsip::select('tahun_arsip')->distinct()->orderByDesc('tahun_arsip')->pluck('tahun_arsip');

        return view('laporan.index', compact(
            'arsips',
            'rekapJenis',
            'rekapUnit',
            'rekapTahun',
            'rekapStatus',
            'jenisPajaks',
            'units',
            'tahuns',
            'filters'
        ));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['jenis_pajak_id', 'unit_id', 'status', 'tahun']);
        $filename = 'laporan-arsip-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new ArsipExport($filters), $filename);
    }

    private function applyFilters($query, array $filters): void
    {
        if (! empty($filters['jenis_pajak_id'])) {
            $query->where('jenis_pajak_id', $filters['jenis_pajak_id']);
        }
        if (! empty($filters['unit_id'])) {
            $query->where('unit_id', $filters['unit_id']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['tahun'])) {
            $query->where('tahun_arsip', $filters['tahun']);
        }
    }
}
