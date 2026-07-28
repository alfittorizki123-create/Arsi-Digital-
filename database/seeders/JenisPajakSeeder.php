<?php

namespace Database\Seeders;

use App\Models\JenisPajak;
use Illuminate\Database\Seeder;

class JenisPajakSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = [
            ['nama_jenis_pajak' => 'Pajak Kendaraan Bermotor (PKB)', 'kode' => 'PKB'],
            ['nama_jenis_pajak' => 'Bea Balik Nama Kendaraan Bermotor (BBNKB)', 'kode' => 'BBNKB'],
            ['nama_jenis_pajak' => 'Pajak Bahan Bakar Kendaraan Bermotor (PBBKB)', 'kode' => 'PBBKB'],
            ['nama_jenis_pajak' => 'Pajak Air Permukaan (PAP)', 'kode' => 'PAP'],
            ['nama_jenis_pajak' => 'Pajak Rokok', 'kode' => 'PR'],
        ];

        foreach ($jenis as $item) {
            JenisPajak::create($item);
        }
    }
}