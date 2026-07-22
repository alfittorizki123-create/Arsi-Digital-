<?php

namespace Database\Seeders;

use App\Models\JenisPajak;
use Illuminate\Database\Seeder;

class JenisPajakSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = [
            ['nama_jenis_pajak' => 'Pajak Kendaraan Bermotor', 'kode' => 'PKB'],
            ['nama_jenis_pajak' => 'Pajak Alat Berat', 'kode' => 'PAB'],
            ['nama_jenis_pajak' => 'Pajak Bahan Bakar Kendaraan Bermotor', 'kode' => 'PBBKB'],
            ['nama_jenis_pajak' => 'Pajak Air Tanah', 'kode' => 'PAT'],
            ['nama_jenis_pajak' => 'Pajak Mineral Bukan Logam dan Batuan', 'kode' => 'PMBLB'],
            ['nama_jenis_pajak' => 'Pajak Hotel', 'kode' => 'PH'],
            ['nama_jenis_pajak' => 'Pajak Restoran', 'kode' => 'PR'],
            ['nama_jenis_pajak' => 'Pajak Hiburan', 'kode' => 'PHB'],
            ['nama_jenis_pajak' => 'Pajak Reklame', 'kode' => 'PRK'],
            ['nama_jenis_pajak' => 'Pajak Penerangan Jalan', 'kode' => 'PPJ'],
            ['nama_jenis_pajak' => 'Pajak Parkir', 'kode' => 'PPK'],
            ['nama_jenis_pajak' => 'Pajak Bumi dan Bangunan Perdesaan', 'kode' => 'PBP'],
        ];

        foreach ($jenis as $item) {
            JenisPajak::create($item);
        }
    }
}