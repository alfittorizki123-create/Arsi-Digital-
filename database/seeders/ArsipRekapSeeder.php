<?php

namespace Database\Seeders;

use App\Models\Arsip;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ArsipRekapSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['unit' => 'UPT Pekanbaru Kota', 'kode' => '900.1.13.1', 'uraian' => 'UPT Pekanbaru Kota ( Boks 1 : No. 1-20)', 'boks' => '1', 'jumlah' => 21],
            ['unit' => 'Samsat MPP', 'kode' => '900.1.13.1', 'uraian' => 'Samsat MPP ( Boks 1 : No. 1-9)', 'boks' => '2', 'jumlah' => 9],
            ['unit' => 'UPT Bagan Batu', 'kode' => '900.1.13.1', 'uraian' => 'UPT Bagan Batu ( Boks 1 : No. 1-6; Boks 2 : No. 7-9)', 'boks' => '3,4', 'jumlah' => 9],
            ['unit' => 'UP Pujud', 'kode' => '900.1.13.1', 'uraian' => 'UP Pujud ( Boks 1 : No. 1-9 )', 'boks' => '5', 'jumlah' => 9],
            ['unit' => 'UPT Selat Panjang', 'kode' => '900.1.13.1', 'uraian' => 'UPT Selat Panjang ( Boks 1 : No. 1-12 )', 'boks' => '6', 'jumlah' => 12],
            ['unit' => 'UPT Bangkinang', 'kode' => '900.1.13.1', 'uraian' => 'UPT Bangkinang ( Boks 1 : No. 1-9 ; Boks 2 : No. 10-14 )', 'boks' => '7,8', 'jumlah' => 14],
            ['unit' => 'UPT Bagan Siapi-Api', 'kode' => '900.1.13.1', 'uraian' => 'UPT Bagan Siapi-Api  ( Boks 1 : No. 1-12 )', 'boks' => '9', 'jumlah' => 12],
            ['unit' => 'UP Ujung Tanjung', 'kode' => '900.1.13.1', 'uraian' => 'UP Ujung Tanjung ( Boks 1 : No. 1-12 )', 'boks' => '10', 'jumlah' => 12],
            ['unit' => 'UPT Tembilahan', 'kode' => '900.1.13.1', 'uraian' => 'UPT Tembilahan ( Boks 1 : No. 1- 9 ; Boks 2 : No.10-12) ', 'boks' => '11,12', 'jumlah' => 12],
            ['unit' => 'UP Kateman', 'kode' => '900.1.13.1', 'uraian' => 'UP Kateman ( Boks 1 : No. 1-13 )', 'boks' => '13', 'jumlah' => 13],
            ['unit' => 'UP Keritang', 'kode' => '900.1.13.2', 'uraian' => 'UP Keritang ( Boks 1 : No.1-12 )', 'boks' => '14', 'jumlah' => 12],
            ['unit' => 'UP Kempas Inhil', 'kode' => '900.1.13.3', 'uraian' => 'UP Kempas Inhil ( Boks 1 : No. 1-11)', 'boks' => '15', 'jumlah' => 11],
            ['unit' => 'UPT Rengat', 'kode' => '900.1.13.4', 'uraian' => 'UPT rengat (Boks 1 : No. 1-8 ; Boks 2 : No. 9-11 )', 'boks' => '16,17', 'jumlah' => 11],
            ['unit' => 'UPT Air Molek', 'kode' => '900.1.13.5', 'uraian' => 'UPT Air Molek ( Boks 1 : No. 1-7 ; Boks 2 : No. 8-12 )', 'boks' => '18,19', 'jumlah' => 12],
            ['unit' => 'UP Belilas', 'kode' => '900.1.13.6', 'uraian' => 'UP Belilas ( Boks 1 : No. 1-12 ) ', 'boks' => '20', 'jumlah' => 12],
            ['unit' => 'UP Samsat Keliling Inhu', 'kode' => '900.1.13.7', 'uraian' => 'UP Samsat Keliling Inhu ( Boks 1 : No. 1-11)', 'boks' => '21', 'jumlah' => 11],
            ['unit' => 'UPT Dumai', 'kode' => '900.1.13.8', 'uraian' => 'UPT Dumai ( Boks 1 : No. 1-12 )', 'boks' => '22', 'jumlah' => 12],
            ['unit' => 'UP Duri', 'kode' => '900.1.13.9', 'uraian' => 'UP Duri ( Boks 1 : No. 1-12 )', 'boks' => '23', 'jumlah' => 12],
            ['unit' => 'UP Pinggir', 'kode' => '900.1.13.10', 'uraian' => 'UP Pinggir ( Boks 1 : No. 1-10 )', 'boks' => '24', 'jumlah' => 10],
            ['unit' => 'UP Rupat', 'kode' => '900.1.13.11', 'uraian' => 'UP Rupat ( Boks 1 : No. 1-13 )', 'boks' => '25', 'jumlah' => 13],
            ['unit' => 'UPT Kubang', 'kode' => '900.1.13.12', 'uraian' => 'UPT Kubang ( Boks 1 : No.1-25 ) ', 'boks' => '26', 'jumlah' => 25],
            ['unit' => 'UP Kampar Kiri', 'kode' => '900.1.13.13', 'uraian' => 'UP Kampar Kiri ( Boks 1 : No. 1-10 )', 'boks' => '27', 'jumlah' => 10],
            ['unit' => 'UPT Siak', 'kode' => '900.1.13.14', 'uraian' => ' UPT Siak ( Boks 1 : No. 1-6 ; Boks 2 : No. 7-12 )', 'boks' => '28,29', 'jumlah' => 12],
            ['unit' => 'UPT Bengkalis', 'kode' => '900.1.13.15', 'uraian' => 'UPT Bengkalis ( Boks 1 : No. 1-11 )', 'boks' => '30', 'jumlah' => 11],
            ['unit' => 'UPT Simpang Tiga', 'kode' => '900.1.13.16', 'uraian' => 'UPT Simpang Tiga ( Boks 1 : No.1-10 )', 'boks' => '31', 'jumlah' => 10],
            ['unit' => 'UP Samkel 1', 'kode' => '900.1.13.17', 'uraian' => 'UP Samkel 1 ( Boks 1 : No. 1-20 )', 'boks' => '32', 'jumlah' => 20],
            ['unit' => 'UP Samkel 2', 'kode' => '900.1.13.18', 'uraian' => 'UP Samkel 2 ( Boks 1 : No. 1- 20 )', 'boks' => '33', 'jumlah' => 30],
            ['unit' => 'UPT Taluk Kuantan', 'kode' => '900.1.13.19', 'uraian' => 'UPT Taluk Kuantan ( Boks 1 : No. 1-10 ; Boks 2 :  No.11- 20 )', 'boks' => '34,35', 'jumlah' => 20],
            ['unit' => 'UP Singingi Hilir', 'kode' => '900.1.13.20', 'uraian' => 'UP Singingi Hilir ( Boks 1 : No. 1-11 )', 'boks' => '36', 'jumlah' => 11],
            ['unit' => 'UP Baserah', 'kode' => '900.1.13.21', 'uraian' => 'UP Baserah ( Boks 1 : No. 1-11 )', 'boks' => '37', 'jumlah' => 11],
            ['unit' => 'E-Samsat', 'kode' => '900.1.13.22', 'uraian' => 'E-Samsat ( Boks 1 : No. 1-8 )', 'boks' => '38', 'jumlah' => 8],
            ['unit' => 'UP Kuantan Mudik', 'kode' => '900.1.13.23', 'uraian' => 'UP Kuantan Mudik ( Boks 1 : No. 1-10 )', 'boks' => '39', 'jumlah' => 10],
            ['unit' => 'UP Kandis', 'kode' => '900.1.13.24', 'uraian' => 'UP Kandis ( Boks 1 : No. 1-9 )', 'boks' => '40', 'jumlah' => 9],
            ['unit' => 'UPT Perawang', 'kode' => '900.1.13.25', 'uraian' => 'UPT Perawang ( Boks 1 : No. 1- 6 )', 'boks' => '41', 'jumlah' => 6],
            ['unit' => 'UP Lubuk Dalam', 'kode' => '900.1.13.26', 'uraian' => 'UP Lubuk Dalam ( Boks 1 : No. 1-8 ) ', 'boks' => '42', 'jumlah' => 8],
            ['unit' => 'UPT PKL Kerinci', 'kode' => '900.1.13.27', 'uraian' => 'UPT PKL Kerinci ( Boks 1 : No. 1-13 ; Boks 2 : No. 14-19 )', 'boks' => '43,44', 'jumlah' => 19],
            ['unit' => 'UP PKL Kuras', 'kode' => '900.1.13.28', 'uraian' => 'UP PKL Kuras ( Boks 1 : No. 1-9 )', 'boks' => '45', 'jumlah' => 9],
            ['unit' => 'UP Ukui', 'kode' => '900.1.13.29', 'uraian' => 'UP Ukui ( Boks 1 : No. 1-11 )', 'boks' => '46', 'jumlah' => 11],
            ['unit' => 'UP Sekijang', 'kode' => '900.1.13.30', 'uraian' => 'UP Sekijang ( Boks 1 : No. 1-9 )', 'boks' => '47', 'jumlah' => 9],
            ['unit' => 'UPT Pasir Pangaraian', 'kode' => '900.1.13.31', 'uraian' => 'UPT Pasir Pangaraian ( Boks 1 : No. 1-6 ; Boks 2 : No. 7-12 )', 'boks' => '48,49', 'jumlah' => 12],
            ['unit' => 'UP Ujung Batu', 'kode' => '900.1.13.32', 'uraian' => 'UP Ujung Batu ( Boks 1 : No. 1-12 )', 'boks' => '50', 'jumlah' => 12],
            ['unit' => 'UP Kepenuhan', 'kode' => '900.1.13.33', 'uraian' => 'UP Kepenuhan ( Boks 1 : No. 1-12 )', 'boks' => '51', 'jumlah' => 12],
            ['unit' => 'UP Tambusai', 'kode' => '900.1.13.34', 'uraian' => 'UP Tambusai ( Boks 1 : No. 1-12 )', 'boks' => '52', 'jumlah' => 12],
            ['unit' => 'UP Tapung', 'kode' => '900.1.13.35', 'uraian' => 'UP Tapung ( Boks 1 : No. 1-12 )', 'boks' => '53', 'jumlah' => 12],
            ['unit' => 'UP Tapung Hilir', 'kode' => '900.1.13.36', 'uraian' => 'UP Tapung Hilir ( Boks 1 : No. 1-12 )', 'boks' => '54', 'jumlah' => 12],
            ['unit' => 'UPT Rumbai', 'kode' => '900.1.13.37', 'uraian' => 'UPT Rumbai ( Boks 1 : No. 1-12 )', 'boks' => '55', 'jumlah' => 12],
            ['unit' => 'UPT Panam', 'kode' => '900.1.13.38', 'uraian' => 'UPT Panam ( Boks 1 : No. 1-6 ; Boks 2 : No. 7-9 ) ', 'boks' => '56,56', 'jumlah' => 9],
        ];

        foreach ($data as $row) {
            $unit = Unit::where('nama_unit', $row['unit'])->first();

            Arsip::create([
                'tipe_arsip' => 'rekap',
                'kode_klasifikasi' => $row['kode'],
                'nomor_arsip_berkas' => null,
                'uraian_informasi_arsip' => $row['uraian'],
                'kurun_waktu' => 2023,
                'jumlah' => $row['jumlah'],
                'satuan' => 'Berkas',
                'tingkat_perkembangan' => 'Asli/Copy',
                'nomor_boks' => $row['boks'],
                'kondisi' => 'Baik',
                'klasifikasi_keamanan' => 'Terbuka',
                'status' => 'inaktif',
                'unit_id' => $unit?->id,
            ]);
        }
    }
}