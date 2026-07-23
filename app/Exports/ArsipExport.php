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
        if (! empty($this->filters['kurun_waktu'])) {
            $query->where('kurun_waktu', $this->filters['kurun_waktu']);
        }
        if (! empty($this->filters['tipe_arsip'])) {
            $query->where('tipe_arsip', $this->filters['tipe_arsip']);
        }
        if (! empty($this->filters['kondisi'])) {
            $query->where('kondisi', $this->filters['kondisi']);
        }
        if (! empty($this->filters['klasifikasi_keamanan'])) {
            $query->where('klasifikasi_keamanan', $this->filters['klasifikasi_keamanan']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE KLASIFIKASI',
            'NO ARSIP/BERKAS',
            'URAIAN INFORMASI ARSIP',
            'KURUN WAKTU',
            'JUMLAH',
            'Satuan',
            'TINGKAT PERKEMBANGAN',
            'NO. BOKS',
            'KONDISI ARSIP',
            'KLASIFIKASI KEAMANAN',
            'TIPE ARSIP',
            'UNIT',
            'STATUS',
        ];
    }

    public function map($arsip): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $arsip->kode_klasifikasi ?? '',
            $arsip->nomor_arsip_berkas ?? '',
            $arsip->uraian_informasi_arsip ?? '',
            $arsip->kurun_waktu ?? '',
            $arsip->jumlah ?? '',
            $arsip->satuan ?? 'Berkas',
            $arsip->tingkat_perkembangan ?? '',
            $arsip->nomor_boks ?? '',
            $arsip->kondisi ?? '',
            $arsip->klasifikasi_keamanan ?? '',
            $arsip->tipe_arsip,
            $arsip->unit->nama_unit ?? '',
            $arsip->status,
        ];
    }
}