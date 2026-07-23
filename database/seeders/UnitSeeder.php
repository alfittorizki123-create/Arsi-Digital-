<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['nama_unit' => 'UPT Pekanbaru Kota', 'kode_unit' => 'UPT-001'],
            ['nama_unit' => 'Samsat MPP', 'kode_unit' => 'UPT-002'],
            ['nama_unit' => 'UPT Bagan Batu', 'kode_unit' => 'UPT-003'],
            ['nama_unit' => 'UP Pujud', 'kode_unit' => 'UPT-004'],
            ['nama_unit' => 'UPT Selat Panjang', 'kode_unit' => 'UPT-005'],
            ['nama_unit' => 'UPT Bangkinang', 'kode_unit' => 'UPT-006'],
            ['nama_unit' => 'UPT Bagan Siapi-Api', 'kode_unit' => 'UPT-007'],
            ['nama_unit' => 'UP Ujung Tanjung', 'kode_unit' => 'UPT-008'],
            ['nama_unit' => 'UPT Tembilahan', 'kode_unit' => 'UPT-009'],
            ['nama_unit' => 'UP Kateman', 'kode_unit' => 'UPT-010'],
            ['nama_unit' => 'UP Keritang', 'kode_unit' => 'UPT-011'],
            ['nama_unit' => 'UP Kempas Inhil', 'kode_unit' => 'UPT-012'],
            ['nama_unit' => 'UPT Rengat', 'kode_unit' => 'UPT-013'],
            ['nama_unit' => 'UPT Air Molek', 'kode_unit' => 'UPT-014'],
            ['nama_unit' => 'UP Belilas', 'kode_unit' => 'UPT-015'],
            ['nama_unit' => 'UP Samsat Keliling Inhu', 'kode_unit' => 'UPT-016'],
            ['nama_unit' => 'UPT Dumai', 'kode_unit' => 'UPT-017'],
            ['nama_unit' => 'UP Duri', 'kode_unit' => 'UPT-018'],
            ['nama_unit' => 'UP Pinggir', 'kode_unit' => 'UPT-019'],
            ['nama_unit' => 'UP Rupat', 'kode_unit' => 'UPT-020'],
            ['nama_unit' => 'UPT Kubang', 'kode_unit' => 'UPT-021'],
            ['nama_unit' => 'UP Kampar Kiri', 'kode_unit' => 'UPT-022'],
            ['nama_unit' => 'UPT Siak', 'kode_unit' => 'UPT-023'],
            ['nama_unit' => 'UPT Bengkalis', 'kode_unit' => 'UPT-024'],
            ['nama_unit' => 'UPT Simpang Tiga', 'kode_unit' => 'UPT-025'],
            ['nama_unit' => 'UP Samkel 1', 'kode_unit' => 'UPT-026'],
            ['nama_unit' => 'UP Samkel 2', 'kode_unit' => 'UPT-027'],
            ['nama_unit' => 'UPT Taluk Kuantan', 'kode_unit' => 'UPT-028'],
            ['nama_unit' => 'UP Singingi Hilir', 'kode_unit' => 'UPT-029'],
            ['nama_unit' => 'UP Baserah', 'kode_unit' => 'UPT-030'],
            ['nama_unit' => 'E-Samsat', 'kode_unit' => 'UPT-031'],
            ['nama_unit' => 'UP Kuantan Mudik', 'kode_unit' => 'UPT-032'],
            ['nama_unit' => 'UP Kandis', 'kode_unit' => 'UPT-033'],
            ['nama_unit' => 'UPT Perawang', 'kode_unit' => 'UPT-034'],
            ['nama_unit' => 'UP Lubuk Dalam', 'kode_unit' => 'UPT-035'],
            ['nama_unit' => 'UPT PKL Kerinci', 'kode_unit' => 'UPT-036'],
            ['nama_unit' => 'UP PKL Kuras', 'kode_unit' => 'UPT-037'],
            ['nama_unit' => 'UP Ukui', 'kode_unit' => 'UPT-038'],
            ['nama_unit' => 'UP Sekijang', 'kode_unit' => 'UPT-039'],
            ['nama_unit' => 'UPT Pasir Pangaraian', 'kode_unit' => 'UPT-040'],
            ['nama_unit' => 'UP Ujung Batu', 'kode_unit' => 'UPT-041'],
            ['nama_unit' => 'UP Kepenuhan', 'kode_unit' => 'UPT-042'],
            ['nama_unit' => 'UP Tambusai', 'kode_unit' => 'UPT-043'],
            ['nama_unit' => 'UP Tapung', 'kode_unit' => 'UPT-044'],
            ['nama_unit' => 'UP Tapung Hilir', 'kode_unit' => 'UPT-045'],
            ['nama_unit' => 'UPT Rumbai', 'kode_unit' => 'UPT-046'],
            ['nama_unit' => 'UPT Panam', 'kode_unit' => 'UPT-047'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(
                ['kode_unit' => $unit['kode_unit']],
                $unit
            );
        }
    }
}
