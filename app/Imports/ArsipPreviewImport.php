<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class ArsipPreviewImport implements ToCollection, WithEvents
{
    public array $sheets = [];
    private string $currentSheetName = '';

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $this->currentSheetName = $event->getSheet()->getTitle();
            }
        ];
    }

    public function collection(Collection $rows): void
    {
        if (!empty($this->currentSheetName)) {
            $this->sheets[$this->currentSheetName] = $rows;
        } else {
            $this->sheets['Sheet' . (count($this->sheets) + 1)] = $rows;
        }
    }
}