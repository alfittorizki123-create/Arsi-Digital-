<?php

namespace App\Exports;

use App\Models\Arsip;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ArsipPerUnitSheet implements FromCollection, WithEvents, WithTitle
{
    private Collection $data;

    public function __construct(
        private Unit $unit,
        private array $filters = []
    ) {
        $this->data = $this->fetchData();
    }

    /**
     * Empty collection — all data is written manually via AfterSheet event
     * to achieve exact government template formatting.
     */
    public function collection(): Collection
    {
        return collect([]);
    }

    /**
     * Sheet tab name (max 31 chars for Excel).
     */
    public function title(): string
    {
        $name = $this->unit->kode_unit
            ? $this->unit->kode_unit . ' - ' . $this->unit->nama_unit
            : $this->unit->nama_unit;

        // Remove characters invalid for Excel sheet names
        $name = str_replace(['\\', '/', '*', '?', ':', '[', ']'], '', $name);

        return mb_substr(trim($name), 0, 31);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // --- Default Font Family ---
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(12);

                // --- Page Setup ---
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_FOLIO);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                // --- Column Widths (matching official Bapenda template) ---
                $widths = [
                    'A' => 8,    // NO
                    'B' => 16,   // KODE KLASIFIKASI
                    'C' => 10,   // NO ARSIP/BERKAS
                    'D' => 75,   // URAIAN INFORMASI ARSIP
                    'E' => 10,   // KURUN WAKTU
                    'F' => 6,    // JUMLAH (angka)
                    'G' => 10,   // SATUAN ("Berkas")
                    'H' => 17,   // TINGKAT PERKEMBANGAN
                    'I' => 10,   // NO. BOKS
                    'J' => 13,   // KONDISI ARSIP
                    'K' => 16,   // KLASIFIKASI KEAMANAN
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // ═══════════════════════════════════════════
                // HEADER SECTION
                // ═══════════════════════════════════════════

                // Row 1: top margin
                $sheet->getRowDimension(1)->setRowHeight(15);

                // Row 2: Dynamic Title based on status (Aktif vs Inaktif)
                $statusFilter = strtolower($this->filters['status'] ?? '');
                if ($statusFilter === 'aktif') {
                    $titleText = 'DAFTAR ARSIP AKTIF YANG DIPINDAHKAN';
                } elseif ($statusFilter === 'inaktif') {
                    $titleText = 'DAFTAR ARSIP INAKTIF YANG DIPINDAHKAN';
                } else {
                    $hasAktif = $this->data->contains('status', 'aktif');
                    $hasInaktif = $this->data->contains('status', 'inaktif');
                    if ($hasAktif && !$hasInaktif) {
                        $titleText = 'DAFTAR ARSIP AKTIF YANG DIPINDAHKAN';
                    } elseif ($hasInaktif && !$hasAktif) {
                        $titleText = 'DAFTAR ARSIP INAKTIF YANG DIPINDAHKAN';
                    } else {
                        $titleText = 'DAFTAR ARSIP YANG DIPINDAHKAN';
                    }
                }

                $sheet->getRowDimension(2)->setRowHeight(24);
                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A2', $titleText);
                $sheet->getStyle('A2')->getFont()->setName('Arial')->setBold(true)->setSize(18);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Row 3: blank spacer
                $sheet->getRowDimension(3)->setRowHeight(6);

                // Row 4: ORGANISASI
                $sheet->getRowDimension(4)->setRowHeight(18);
                $sheet->mergeCells('A4:C4');
                $sheet->setCellValue('A4', 'ORGANISASI');
                $sheet->mergeCells('D4:J4');
                $sheet->setCellValue('D4', ': BADAN PENDAPATAN DAERAH PROVINSI RIAU');
                $sheet->getStyle('A4:D4')->getFont()->setName('Arial')->setSize(14);

                // Row 5: UNIT KERJA
                $sheet->mergeCells('A5:C5');
                $sheet->setCellValue('A5', 'UNIT KERJA');
                $sheet->mergeCells('D5:J5');
                $sheet->setCellValue('D5', ': ' . mb_strtoupper($this->unit->nama_unit));
                $sheet->getStyle('A5:J5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                ]);

                // Row 6: blank spacer before table
                $sheet->getRowDimension(6)->setRowHeight(6);

                // ═══════════════════════════════════════════
                // TABLE HEADERS (Rows 7–8)
                // ═══════════════════════════════════════════
                $this->writeTableHeaders($sheet);

                // ═══════════════════════════════════════════
                // COLUMN NUMBERING ROW (Row 9)
                // ═══════════════════════════════════════════
                $cols = range('A', 'J');
                foreach ($cols as $idx => $col) {
                    $sheet->setCellValue($col . '9', $idx + 1);
                }
                $sheet->getStyle('A9:J9')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // ═══════════════════════════════════════════
                // DATA ROWS
                // ═══════════════════════════════════════════
                $currentRow = 10;
                $no = 1;

                foreach ($this->data as $arsip) {
                    $this->writeDataRow($sheet, $currentRow, $no, $arsip);
                    $no++;
                    $currentRow++;
                }

                // ═══════════════════════════════════════════
                // JUMLAH (TOTAL) ROW
                // ═══════════════════════════════════════════
                $this->writeJumlahRow($sheet, $currentRow);
                $currentRow++;

                // ═══════════════════════════════════════════
                // SIGNATURE BLOCK
                // ═══════════════════════════════════════════
                $this->writeSignatureBlock($sheet, $currentRow + 2);
            },
        ];
    }

    // ─────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────

    /**
     * Write the two-row merged header that matches the government template.
     *
     * Columns A-J (10 columns):
     *   A = NO
     *   B = KODE KLASIFIKASI
     *   C = NO ARSIP/BERKAS
     *   D = URAIAN INFORMASI ARSIP
     *   E = KURUN WAKTU
     *   F = JUMLAH  (angka + satuan in one cell)
     *   G = TINGKAT PERKEMBANGAN
     *   H+I = KETERANGAN  (NO. BOKS | KONDISI ARSIP)
     *   J = KLASIFIKASI KEAMANAN DAN AKSES ARSIP
     */
    private function writeTableHeaders($sheet): void
    {
        // Single-column headers merged across rows 7-8
        $sheet->mergeCells('A7:A8');
        $sheet->setCellValue('A7', 'NO');

        $sheet->mergeCells('B7:B8');
        $sheet->setCellValue('B7', 'KODE KLASIFIKASI');

        $sheet->mergeCells('C7:C8');
        $sheet->setCellValue('C7', 'NO ARSIP/ BERKAS');

        $sheet->mergeCells('D7:D8');
        $sheet->setCellValue('D7', 'URAIAN INFORMASI ARSIP');

        $sheet->mergeCells('E7:E8');
        $sheet->setCellValue('E7', 'KURUN WAKTU');

        // JUMLAH — single column, merged rows 7-8
        $sheet->mergeCells('F7:F8');
        $sheet->setCellValue('F7', 'JUMLAH');

        // TINGKAT PERKEMBANGAN — single column, merged rows 7-8
        $sheet->mergeCells('G7:G8');
        $sheet->setCellValue('G7', 'TINGKAT PERKEMBANGAN');

        // KETERANGAN — spans H+I in row 7, sub-headers in row 8
        $sheet->mergeCells('H7:I7');
        $sheet->setCellValue('H7', 'KETERANGAN');
        $sheet->setCellValue('H8', 'NO. BOKS');
        $sheet->setCellValue('I8', 'KONDISI ARSIP');

        // KLASIFIKASI KEAMANAN — single column, merged rows 7-8
        $sheet->mergeCells('J7:J8');
        $sheet->setCellValue('J7', 'KLASIFIKASI KEAMANAN DAN AKSES ARSIP');

        // Style all header cells (no background color, borders only)
        $sheet->getStyle('A7:J8')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // Taller rows for wrapped header text
        $sheet->getRowDimension(7)->setRowHeight(30);
        $sheet->getRowDimension(8)->setRowHeight(25);
    }

    /**
     * Write a single data row matching the 10-column layout.
     * JUMLAH column combines number + satuan in one cell (e.g. "1\nBerkas").
     */
    private function writeDataRow($sheet, int $row, int $no, Arsip $arsip): void
    {
        $jumlah = $arsip->jumlah ?? '';
        $satuan = $arsip->satuan ?? 'Berkas';
        $jumlahText = $jumlah !== '' ? $jumlah . "\n" . $satuan : '';

        $sheet->setCellValue("A{$row}", $no);
        $sheet->setCellValue("B{$row}", $arsip->kode_klasifikasi ?? '');
        $sheet->setCellValue("C{$row}", $arsip->nomor_arsip_berkas ?? '');
        $sheet->setCellValue("D{$row}", $arsip->uraian_informasi_arsip ?? '');
        $sheet->setCellValue("E{$row}", $arsip->kurun_waktu ?? '');
        $sheet->setCellValue("F{$row}", $jumlahText);
        $sheet->setCellValue("G{$row}", $arsip->tingkat_perkembangan ?? '');
        $sheet->setCellValue("H{$row}", $arsip->nomor_boks ?? '');
        $sheet->setCellValue("I{$row}", $arsip->kondisi ?? '');
        $sheet->setCellValue("J{$row}", $arsip->klasifikasi_keamanan ?? '');

        // Borders + font + wrap for all data cells
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 11, 'bold' => false],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        // Center-align all columns EXCEPT Column D (Uraian Informasi Arsip)
        foreach (['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I', 'J'] as $c) {
            $sheet->getStyle("{$c}{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Left-align Column D (Uraian Informasi Arsip)
        $sheet->getStyle("D{$row}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    /**
     * Write the JUMLAH total row at the bottom of the data table.
     */
    private function writeJumlahRow($sheet, int $row): void
    {
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue("A{$row}", 'JUMLAH ………………………………………………………');

        $total = $this->data->sum(fn ($a) => (int) ($a->jumlah ?? 0));
        $sheet->setCellValue("F{$row}", $total . "\n" . 'Berkas');

        $sheet->mergeCells("G{$row}:J{$row}");

        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    /**
     * Write the dual signature block:
     *   Left  = "Yang memindahkan" (Kepala Unit)
     *   Right = "Yang menerima" (Sekretaris Bapenda)
     */
    private function writeSignatureBlock($sheet, int $r): void
    {
        // Indonesian month names (locale-independent)
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $tanggal = now()->format('d') . ' ' . $bulan[(int) now()->format('m')] . ' ' . now()->format('Y');

        // ── Right side: Date ──
        $sheet->mergeCells("G{$r}:J{$r}");
        $sheet->setCellValue("G{$r}", "Pekanbaru, {$tanggal}");
        $sheet->getStyle("G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $r++;

        // blank spacer
        $r++;

        // ── Left: "Yang memindahkan," | Right: "Yang menerima," ──
        $sheet->mergeCells("A{$r}:E{$r}");
        $sheet->setCellValue("A{$r}", 'Yang memindahkan,');
        $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("G{$r}:J{$r}");
        $sheet->setCellValue("G{$r}", 'Yang menerima,');
        $sheet->getStyle("G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $r++;

        // ── Left: KEPALA [UNIT NAME] ──
        $sheet->mergeCells("A{$r}:E{$r}");
        $sheet->setCellValue("A{$r}", 'KEPALA ' . mb_strtoupper($this->unit->nama_unit));
        $sheet->getStyle("A{$r}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);

        // ── Right: SEKRETARIS line 1 ──
        $sheet->mergeCells("G{$r}:J{$r}");
        $sheet->setCellValue("G{$r}", 'SEKRETARIS BADAN PENDAPATAN DAERAH');
        $sheet->getStyle("G{$r}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $r++;

        // ── Left: "SELAKU KEPALA UNIT PENGOLAH," ──
        $sheet->mergeCells("A{$r}:E{$r}");
        $sheet->setCellValue("A{$r}", 'SELAKU KEPALA UNIT PENGOLAH,');
        $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Right: PROVINSI RIAU ──
        $sheet->mergeCells("G{$r}:J{$r}");
        $sheet->setCellValue("G{$r}", 'PROVINSI RIAU');
        $sheet->getStyle("G{$r}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $r++;

        // ── Right: "SELAKU KETUA UNIT KEARSIPAN," ──
        $sheet->mergeCells("A{$r}:E{$r}");
        $sheet->mergeCells("G{$r}:J{$r}");
        $sheet->setCellValue("G{$r}", 'SELAKU KETUA UNIT KEARSIPAN,');
        $sheet->getStyle("G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $r++;

        // ── Signature space (4 blank rows) ──
        $r += 4;

        // ── Left: Name placeholder ──
        $sheet->mergeCells("A{$r}:E{$r}");
        $sheet->setCellValue("A{$r}", '............................................');
        $sheet->getStyle("A{$r}")->applyFromArray([
            'font' => ['bold' => true, 'underline' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Right: Name placeholder ──
        $sheet->mergeCells("G{$r}:J{$r}");
        $sheet->setCellValue("G{$r}", '............................................');
        $sheet->getStyle("G{$r}")->applyFromArray([
            'font' => ['bold' => true, 'underline' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $r++;

        // ── Left: Rank placeholder ──
        $sheet->mergeCells("A{$r}:E{$r}");
        $sheet->setCellValue("A{$r}", '............................................');
        $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Right: Rank placeholder ──
        $sheet->mergeCells("G{$r}:J{$r}");
        $sheet->setCellValue("G{$r}", '............................................');
        $sheet->getStyle("G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $r++;

        // ── Left: NIP ──
        $sheet->mergeCells("A{$r}:E{$r}");
        $sheet->setCellValue("A{$r}", 'NIP.');
        $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Right: NIP ──
        $sheet->mergeCells("G{$r}:J{$r}");
        $sheet->setCellValue("G{$r}", 'NIP.');
        $sheet->getStyle("G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    /**
     * Fetch arsip data for this unit, with filters applied.
     */
    private function fetchData(): Collection
    {
        if (! $this->unit->exists) {
            return collect([]);
        }

        $query = Arsip::query()->where('arsips.unit_id', $this->unit->id);

        if (! empty($this->filters['status'])) {
            $query->where('arsips.status', $this->filters['status']);
        }
        if (! empty($this->filters['tipe_arsip'])) {
            $query->where('arsips.tipe_arsip', $this->filters['tipe_arsip']);
        }
        if (! empty($this->filters['kurun_waktu'])) {
            $query->where('arsips.kurun_waktu', $this->filters['kurun_waktu']);
        }
        if (! empty($this->filters['kondisi'])) {
            $query->where('arsips.kondisi', $this->filters['kondisi']);
        }
        if (! empty($this->filters['klasifikasi_keamanan'])) {
            $query->where('arsips.klasifikasi_keamanan', $this->filters['klasifikasi_keamanan']);
        }
        if (! empty($this->filters['jenis_pajak_id'])) {
            $query->where('arsips.jenis_pajak_id', $this->filters['jenis_pajak_id']);
        }
        if (! empty($this->filters['selected_ids'])) {
            $ids = is_array($this->filters['selected_ids']) ? $this->filters['selected_ids'] : explode(',', $this->filters['selected_ids']);
            $query->whereIn('arsips.id', array_filter($ids));
        }

        return $query
            ->leftJoin('boks', 'arsips.boks_id', '=', 'boks.id')
            ->select('arsips.*')
            ->orderByRaw('COALESCE(boks.nomor_boks, arsips.nomor_boks, 9999) ASC')
            ->orderByRaw('CAST(COALESCE(arsips.nomor_arsip_berkas, arsips.bulan, arsips.id) AS UNSIGNED) ASC')
            ->orderBy('arsips.id', 'asc')
            ->get();
    }
}
