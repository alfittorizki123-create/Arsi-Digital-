<?php

namespace App\Exports;

use App\Models\Arsip;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ArsipExport implements WithMultipleSheets
{
    public function __construct(private array $filters = [])
    {
    }

    /**
     * Generate one Excel sheet per unit/UPT/UP.
     *
     * - If a specific unit_id is filtered, generates a single sheet for that unit.
     * - Otherwise, generates one sheet per unit that has matching arsip data.
     */
    public function sheets(): array
    {
        $sheets = [];

        if (! empty($this->filters['unit_id'])) {
            // ── Single unit export ──
            $unit = Unit::find($this->filters['unit_id']);
            if ($unit) {
                $sheets[] = new ArsipPerUnitSheet($unit, $this->filters);
            }
        } else {
            // ── Multi-unit export: Add REKAP ARSIP sheet first, then one sheet per unit ──
            $sheets[] = new RekapArsipSheet($this->filters);

            $query = Arsip::query()->whereNotNull('unit_id');
            $this->applyFilters($query);
            $unitIds = $query->distinct()->pluck('unit_id');

            $units = Unit::whereIn('id', $unitIds)->orderBy('nama_unit')->get();

            foreach ($units as $unit) {
                $unitFilters = $this->filters;
                $unitFilters['unit_id'] = $unit->id;
                $sheets[] = new ArsipPerUnitSheet($unit, $unitFilters);
            }
        }

        // Fallback: at least one sheet is required by maatwebsite/excel
        if (empty($sheets)) {
            $placeholder = new Unit(['nama_unit' => 'Tidak Ada Data', 'kode_unit' => 'NA']);
            $sheets[] = new ArsipPerUnitSheet($placeholder, $this->filters);
        }

        return $sheets;
    }

    /**
     * Apply user-selected filters to narrow which units have data.
     */
    private function applyFilters($query): void
    {
        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (! empty($this->filters['tipe_arsip'])) {
            $query->where('tipe_arsip', $this->filters['tipe_arsip']);
        }
        if (! empty($this->filters['kurun_waktu'])) {
            $query->where('kurun_waktu', $this->filters['kurun_waktu']);
        }
        if (! empty($this->filters['kondisi'])) {
            $query->where('kondisi', $this->filters['kondisi']);
        }
        if (! empty($this->filters['klasifikasi_keamanan'])) {
            $query->where('klasifikasi_keamanan', $this->filters['klasifikasi_keamanan']);
        }
        if (! empty($this->filters['jenis_pajak_id'])) {
            $query->where('jenis_pajak_id', $this->filters['jenis_pajak_id']);
        }
        if (! empty($this->filters['selected_ids'])) {
            $ids = is_array($this->filters['selected_ids']) ? $this->filters['selected_ids'] : explode(',', $this->filters['selected_ids']);
            $query->whereIn('id', array_filter($ids));
        }
    }
}