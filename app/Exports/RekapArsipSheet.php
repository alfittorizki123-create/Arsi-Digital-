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

class RekapArsipSheet implements FromCollection, WithEvents, WithTitle
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection(): Collection
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'REKAP ARSIP';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set Default Font to Arial 12pt (matching official Bapenda Excel)
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(12);

                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_FOLIO);

                // Exact Column Widths matching Official Bapenda Template (Col A-K)
                $widths = [
                    'A' => 8,    // NO
                    'B' => 16,   // KODE KLASIFIKASI
                    'C' => 10,   // NO ARSIP/ BERKAS
                    'D' => 75,   // URAIAN INFORMASI ARSIP (Nama Unit & Rincian Boks)
                    'E' => 10,   // KURUN WAKTU
                    'F' => 6,    // JUMLAH (angka)
                    'G' => 10,   // SATUAN ("Berkas")
                    'H' => 17,   // TINGKAT PERKEMBANGAN ("Asli\copy")
                    'I' => 10,   // NO. BOKS (di bawah KETERANGAN)
                    'J' => 13,   // KONDISI ARSIP (di bawah KETERANGAN)
                    'K' => 16,   // KLASIFIKASI KEAMANAN DAN AKSES ARSIP
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // Row 1: Top Margin Height
                $sheet->getRowDimension(1)->setRowHeight(15);

                // Dynamic Title Header based on status filter
                $statusFilter = strtolower($this->filters['status'] ?? '');
                if ($statusFilter === 'aktif') {
                    $titleText = 'DAFTAR ARSIP AKTIF YANG DIPINDAHKAN';
                    $unitKerjaText = ': REKAP ARSIP AKTIF';
                } elseif ($statusFilter === 'inaktif') {
                    $titleText = 'DAFTAR ARSIP INAKTIF YANG DIPINDAHKAN';
                    $unitKerjaText = ': REKAP ARSIP IN AKTIF';
                } else {
                    $titleText = 'DAFTAR ARSIP YANG DIPINDAHKAN';
                    $unitKerjaText = ': REKAP ARSIP';
                }

                if (!empty($this->filters['kurun_waktu'])) {
                    $unitKerjaText .= ' ' . $this->filters['kurun_waktu'];
                } else {
                    $unitKerjaText .= ' 2023';
                }

                // Row 2: Title Header (Arial 18pt BOLD)
                $sheet->getRowDimension(2)->setRowHeight(24);
                $sheet->mergeCells('A2:K2');
                $sheet->setCellValue('A2', $titleText);
                $sheet->getStyle('A2')->getFont()->setName('Arial')->setBold(true)->setSize(18);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Row 4: Organisasi (Arial 14pt)
                $sheet->getRowDimension(4)->setRowHeight(18);
                $sheet->mergeCells('A4:C4');
                $sheet->setCellValue('A4', 'ORGANISASI');
                $sheet->mergeCells('D4:K4');
                $sheet->setCellValue('D4', ': BADAN PENDAPATAN DAERAH PROVINSI RIAU');
                $sheet->getStyle('A4:D4')->getFont()->setName('Arial')->setSize(14);

                // Row 5: Unit Kerja (Arial 14pt)
                $sheet->getRowDimension(5)->setRowHeight(18);
                $sheet->mergeCells('A5:C5');
                $sheet->setCellValue('A5', 'UNIT KERJA');
                $sheet->mergeCells('D5:K5');
                $sheet->setCellValue('D5', $unitKerjaText);
                $sheet->getStyle('A5:D5')->getFont()->setName('Arial')->setSize(14);

                $sheet->getRowDimension(6)->setRowHeight(10);

                // Row 7 & 8: Table Headers (Arial 10pt BOLD)
                $sheet->getRowDimension(7)->setRowHeight(25.5);
                $sheet->getRowDimension(8)->setRowHeight(57);

                $sheet->mergeCells('A7:A8');
                $sheet->setCellValue('A7', 'NO');

                $sheet->mergeCells('B7:B8');
                $sheet->setCellValue('B7', 'KODE KLASIFIKASI');

                $sheet->mergeCells('C7:C8');
                $sheet->setCellValue('C7', "NO ARSIP/\nBERKAS");

                $sheet->mergeCells('D7:D8');
                $sheet->setCellValue('D7', 'URAIAN INFORMASI ARSIP');

                $sheet->mergeCells('E7:E8');
                $sheet->setCellValue('E7', "KURUN\nWAKTU");

                // F7:G7 merged for JUMLAH
                $sheet->mergeCells('F7:G7');
                $sheet->setCellValue('F7', 'JUMLAH');

                $sheet->mergeCells('H7:H8');
                $sheet->setCellValue('H7', "TINGKAT\nPERKEMBANGAN");

                // I7:J7 merged for KETERANGAN
                $sheet->mergeCells('I7:J7');
                $sheet->setCellValue('I7', 'KETERANGAN');
                $sheet->setCellValue('I8', 'NO. BOKS');
                $sheet->setCellValue('J8', "KONDISI\nARSIP");

                $sheet->mergeCells('K7:K8');
                $sheet->setCellValue('K7', "KLASIFIKASI\nKEAMANAN DAN\nAKSES ARSIP");

                $sheet->getStyle('A7:K8')->getFont()->setName('Arial')->setBold(true)->setSize(10);
                $sheet->getStyle('A7:K8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A7:K8')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A7:K8')->getAlignment()->setWrapText(true);

                // Row 9: Column Numbering (Arial 10pt)
                $sheet->getRowDimension(9)->setRowHeight(15);
                $colNumbers = [
                    'A' => '1', 'B' => '3', 'C' => '2', 'D' => '4', 'E' => '5',
                    'F' => '6', 'G' => '', 'H' => '7', 'I' => '8', 'J' => '9', 'K' => '10',
                ];
                foreach ($colNumbers as $col => $num) {
                    $sheet->setCellValue("{$col}9", $num);
                }
                $sheet->getStyle('A9:K9')->getFont()->setName('Arial')->setSize(10);
                $sheet->getStyle('A9:K9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Fetch Data Grouped by Unit
                $unitIds = Arsip::query()
                    ->when(!empty($this->filters['unit_id']), fn($q) => $q->where('unit_id', $this->filters['unit_id']))
                    ->when(!empty($this->filters['status']), fn($q) => $q->where('status', $this->filters['status']))
                    ->when(!empty($this->filters['kurun_waktu']), fn($q) => $q->where('kurun_waktu', $this->filters['kurun_waktu']))
                    ->whereNotNull('unit_id')
                    ->distinct()
                    ->pluck('unit_id');

                $units = Unit::whereIn('id', $unitIds)->get();

                $rekapRows = [];

                foreach ($units as $u) {
                    $uItems = Arsip::where('unit_id', $u->id)
                        ->when(!empty($this->filters['status']), fn($q) => $q->where('status', $this->filters['status']))
                        ->when(!empty($this->filters['kurun_waktu']), fn($q) => $q->where('kurun_waktu', $this->filters['kurun_waktu']))
                        ->get();

                    if ($uItems->isEmpty()) continue;

                    // Group items by NOMOR_BOKS
                    $uWithRows = $uItems->values()->map(function($it, $idx) {
                        $it->table_row_no = $idx + 1;
                        return $it;
                    });

                    $boksGroups = $uWithRows->groupBy(function($item) {
                        $bNum = $item->boks ? $item->boks->nomor_boks : ($item->nomor_boks ?: null);
                        return $bNum ? "BOKS_{$bNum}" : 'TANPA_BOKS';
                    });

                    $rincianParts = [];
                    $globalBoksNums = [];
                    $numericBoksList = [];
                    $localBoksCounter = 1;

                    foreach ($boksGroups as $groupKey => $bItems) {
                        $bNum = $bItems->first()->boks ? $bItems->first()->boks->nomor_boks : ($bItems->first()->nomor_boks ?? null);

                        if ($bNum) {
                            preg_match('/\d+/', $bNum, $m);
                            if (isset($m[0])) {
                                $numericBoksList[] = (int)$m[0];
                            }
                        }

                        $explicitNos = $bItems->pluck('nomor_arsip_berkas')->map(fn($v) => (int)$v)->filter(fn($v) => $v > 0)->sort()->values();
                        if ($explicitNos->isNotEmpty()) {
                            $minNo = $explicitNos->first();
                            $maxNo = $explicitNos->last();
                        } else {
                            $rowNos = $bItems->pluck('table_row_no')->sort()->values();
                            $minNo = $rowNos->first();
                            $maxNo = $rowNos->last();
                        }

                        $rangeStr = ($minNo === $maxNo) ? "No. {$minNo}" : "No. {$minNo}-{$maxNo}";
                        $localBoksLabel = "Boks {$localBoksCounter}";
                        $localBoksCounter++;

                        $rincianParts[] = "{$localBoksLabel}: {$rangeStr}";

                        if ($bNum) {
                            $globalBoksNums[] = $bNum;
                        }
                    }

                    sort($numericBoksList);
                    $minBoksNum = !empty($numericBoksList) ? $numericBoksList[0] : 999999;

                    $unitLabelWithRincian = $u->nama_unit;
                    if (!empty($rincianParts)) {
                        $unitLabelWithRincian .= " ( " . implode('; ', $rincianParts) . " )";
                    }

                    $jumlahBerkas = $uItems->count();
                    $kurunWaktuStr = $uItems->pluck('kurun_waktu')->unique()->filter()->implode(', ') ?: '2023';
                    $globalBoksStr = !empty($globalBoksNums) ? implode(',', array_unique($globalBoksNums)) : '-';

                    $rekapRows[] = [
                        'unit' => $u,
                        'nama_unit' => $u->nama_unit,
                        'min_boks_num' => $minBoksNum,
                        'unitLabelWithRincian' => $unitLabelWithRincian,
                        'kurunWaktuStr' => $kurunWaktuStr,
                        'jumlahBerkas' => $jumlahBerkas,
                        'globalBoksStr' => $globalBoksStr,
                    ];
                }

                // Sort rekap rows by min_boks_num ascending (and nama_unit as secondary)
                $rekapRows = collect($rekapRows)->sortBy([
                    ['min_boks_num', 'asc'],
                    ['nama_unit', 'asc'],
                ])->values();

                $r = 10;
                $no = 1;

                foreach ($rekapRows as $row) {
                    $kodeKlasifikasi = '900.1.13.1';
                    $kondisiStr = 'Baik';
                    $keamananStr = 'Terbuka';
                    $tingkatPerkembangan = 'Asli\copy';

                    // Generate exact sheet tab name matching ArsipPerUnitSheet::title()
                    $uObj = $row['unit'];
                    $sheetTabName = $uObj->kode_unit
                        ? $uObj->kode_unit . ' - ' . $uObj->nama_unit
                        : $uObj->nama_unit;
                    $sheetTabName = str_replace(['\\', '/', '*', '?', ':', '[', ']'], '', $sheetTabName);
                    $sheetTabName = mb_substr(trim($sheetTabName), 0, 31);

                    $sheet->setCellValue("A{$r}", $no++);
                    $sheet->setCellValue("B{$r}", $kodeKlasifikasi);
                    $sheet->setCellValue("C{$r}", '');
                    $sheet->setCellValue("D{$r}", $row['unitLabelWithRincian']);
                    
                    // Internal Sheet Hyperlink
                    $sheet->getCell("D{$r}")->getHyperlink()->setUrl("sheet://'{$sheetTabName}'!A1");

                    $sheet->setCellValue("E{$r}", $row['kurunWaktuStr']);
                    $sheet->setCellValue("F{$r}", $row['jumlahBerkas']);
                    $sheet->setCellValue("G{$r}", 'Berkas');
                    $sheet->setCellValue("H{$r}", $tingkatPerkembangan);
                    $sheet->setCellValue("I{$r}", $row['globalBoksStr']);
                    $sheet->setCellValue("J{$r}", $kondisiStr);
                    $sheet->setCellValue("K{$r}", $keamananStr);

                    $sheet->getStyle("A{$r}:K{$r}")->getFont()->setName('Arial')->setSize(12);
                    $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E{$r}:K{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("D{$r}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK))->setUnderline(false);

                    $r++;
                }

                // Apply Thin Black Borders matching Official Bapenda Excel
                $lastDataRow = $r - 1;
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                $sheet->getStyle("A7:K{$lastDataRow}")->applyFromArray($styleArray);
            },
        ];
    }
}
