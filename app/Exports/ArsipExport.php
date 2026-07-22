<?php

namespace App\Exports;

use App\Models\Arsip;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ArsipExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection(): Collection
    {
        $query = Arsip::with(['jenisPajak', 'unit'])->latest();

        if (! empty($this->filters['jenis_pajak_id'])) {
            $query->where('jenis_pajak_id', $this->filters['jenis_pajak_id']);
        }
        if (! empty($this->filters['unit_id'])) {
            $query->where('unit_id', $this->filters['unit_id']);
        }
        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (! empty($this->filters['tahun'])) {
            $query->where('tahun_arsip', $this->filters['tahun']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nomor Arsip',
            'Jenis Pajak',
            'Kode Jenis Pajak',
            'Nama Wajib Pajak',
            'Tahun Arsip',
            'Nomor Rak',
            'Unit/UPT',
            'Kode Unit',
            'Status',
            'Tanggal Dicatat',
        ];
    }

    public function map($arsip): array
    {
        return [
            $arsip->nomor_arsip,
            $arsip->jenisPajak->nama_jenis_pajak ?? '',
            $arsip->jenisPajak->kode ?? '',
            $arsip->nama_wajib_pajak,
            $arsip->tahun_arsip,
            $arsip->nomor_rak ?? '',
            $arsip->unit->nama_unit ?? '',
            $arsip->unit->kode_unit ?? '',
            $arsip->status,
            optional($arsip->created_at)->format('Y-m-d H:i'),
        ];
    }
}
