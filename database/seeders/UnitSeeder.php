<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        // Data dummy ±50 UPT Bapenda Provinsi Riau (contoh — data asli menyusul)
        $units = [
            ['nama_unit' => 'Sekretariat Bapenda', 'kode_unit' => 'UPT-001'],
            ['nama_unit' => 'Bidang Pendataan dan Penilaian', 'kode_unit' => 'UPT-002'],
            ['nama_unit' => 'Bidang Penindakan dan Pengawasan', 'kode_unit' => 'UPT-003'],
            ['nama_unit' => 'Bidang Pelayanan Pajak', 'kode_unit' => 'UPT-004'],
            ['nama_unit' => 'Bidang Manajemen dan Tata Usaha', 'kode_unit' => 'UPT-005'],
            ['nama_unit' => 'UPT Pekanbaru Selatan', 'kode_unit' => 'UPT-006'],
            ['nama_unit' => 'UPT Pekanbaru Kota', 'kode_unit' => 'UPT-007'],
            ['nama_unit' => 'UPT Pekanbaru Utara', 'kode_unit' => 'UPT-008'],
            ['nama_unit' => 'UPT Pekanbaru Timur', 'kode_unit' => 'UPT-009'],
            ['nama_unit' => 'UPT Pekanbaru Barat', 'kode_unit' => 'UPT-010'],
            ['nama_unit' => 'UPT Pekanbaru Tampan', 'kode_unit' => 'UPT-011'],
            ['nama_unit' => 'UPT Pekanbaru Marpoyan Damai', 'kode_unit' => 'UPT-012'],
            ['nama_unit' => 'UPT Pekanbaru Bukit Raya', 'kode_unit' => 'UPT-013'],
            ['nama_unit' => 'UPT Pekanbaru Payung Sekaki', 'kode_unit' => 'UPT-014'],
            ['nama_unit' => 'UPT Pekanbaru Tenayan Raya', 'kode_unit' => 'UPT-015'],
            ['nama_unit' => 'UPT Siak', 'kode_unit' => 'UPT-016'],
            ['nama_unit' => 'UPT Kampar', 'kode_unit' => 'UPT-017'],
            ['nama_unit' => 'UPT Rokan Hulu', 'kode_unit' => 'UPT-018'],
            ['nama_unit' => 'UPT Rokan Hilir', 'kode_unit' => 'UPT-019'],
            ['nama_unit' => 'UPT Bengkalis', 'kode_unit' => 'UPT-020'],
            ['nama_unit' => 'UPT Bengkalis Selatan', 'kode_unit' => 'UPT-021'],
            ['nama_unit' => 'UPT Siak Sri Indrapura', 'kode_unit' => 'UPT-022'],
            ['nama_unit' => 'UPT Pangkalan Kerinci', 'kode_unit' => 'UPT-023'],
            ['nama_unit' => 'UPT Pasir Pengaraian', 'kode_unit' => 'UPT-024'],
            ['nama_unit' => 'UPT Bagansiapiapi', 'kode_unit' => 'UPT-025'],
            ['nama_unit' => 'UPT Dumai Kota', 'kode_unit' => 'UPT-026'],
            ['nama_unit' => 'UPT Dumai Timur', 'kode_unit' => 'UPT-027'],
            ['nama_unit' => 'UPT Dumai Barat', 'kode_unit' => 'UPT-028'],
            ['nama_unit' => 'UPT Dumai Selatan', 'kode_unit' => 'UPT-029'],
            ['nama_unit' => 'UPT Indragiri Hulu', 'kode_unit' => 'UPT-030'],
            ['nama_unit' => 'UPT Indragiri Hilir', 'kode_unit' => 'UPT-031'],
            ['nama_unit' => 'UPT Rengat', 'kode_unit' => 'UPT-032'],
            ['nama_unit' => 'UPT Tembilahan', 'kode_unit' => 'UPT-033'],
            ['nama_unit' => 'UPT Selatpanjang', 'kode_unit' => 'UPT-034'],
            ['nama_unit' => 'UPT Duri', 'kode_unit' => 'UPT-035'],
            ['nama_unit' => 'UPT Minas', 'kode_unit' => 'UPT-036'],
            ['nama_unit' => 'UPT Taluk Kuantan', 'kode_unit' => 'UPT-037'],
            ['nama_unit' => 'UPT Ujungbatu', 'kode_unit' => 'UPT-038'],
            ['nama_unit' => 'UPT AIR Tiris', 'kode_unit' => 'UPT-039'],
            ['nama_unit' => 'UPT Bangkinang', 'kode_unit' => 'UPT-040'],
            ['nama_unit' => 'UPT Siak Hulu', 'kode_unit' => 'UPT-041'],
            ['nama_unit' => 'UPT Kerinci Rokan', 'kode_unit' => 'UPT-042'],
            ['nama_unit' => 'UPT Pujud', 'kode_unit' => 'UPT-043'],
            ['nama_unit' => 'UPT Tanah Putih', 'kode_unit' => 'UPT-044'],
            ['nama_unit' => 'UPT Bagan Batu', 'kode_unit' => 'UPT-045'],
            ['nama_unit' => 'UPT Bukit Kapur', 'kode_unit' => 'UPT-046'],
            ['nama_unit' => 'UPT Sungai Apit', 'kode_unit' => 'UPT-047'],
            ['nama_unit' => 'UPT Pemayung', 'kode_unit' => 'UPT-048'],
            ['nama_unit' => 'UPT Kateman', 'kode_unit' => 'UPT-049'],
            ['nama_unit' => 'UPT Mandah', 'kode_unit' => 'UPT-050'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}