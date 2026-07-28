<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ArsipFile;

class StoreArsipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe_arsip' => ['required', Rule::in(['rekap', 'detail'])],
            'kode_klasifikasi' => ['nullable', 'string', 'max:50'],
            'nomor_arsip_berkas' => ['nullable', 'string', 'max:100'],
            'uraian_informasi_arsip' => ['nullable', 'string'],
            'kurun_waktu' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
            'jumlah' => ['nullable', 'integer', 'min:0'],
            'satuan' => ['nullable', 'string', 'max:20'],
            'tingkat_perkembangan' => ['nullable', Rule::in(['Asli', 'Copy', 'Asli/Copy'])],
            'nomor_boks' => ['nullable', 'string', 'max:50'],
            'kondisi' => ['nullable', Rule::in(['Baik', 'Rusak'])],
            'klasifikasi_keamanan' => ['nullable', Rule::in(['Terbuka', 'Terbatas', 'Rahasia'])],
            'status' => ['required', Rule::in(['aktif', 'inaktif'])],
            'unit_id' => ['nullable', 'exists:units,id'],
            'jenis_pajak_id' => ['nullable', 'exists:jenis_pajaks,id'],
            'jenis_pajak_ids' => ['nullable', 'array'],
            'jenis_pajak_ids.*' => ['nullable', 'exists:jenis_pajaks,id'],
            'file_arsip' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx', 'max:102400'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx', 'max:102400'],
            'uploaded_file_ids' => ['nullable', 'array'],
            'uploaded_file_ids.*' => ['nullable', 'integer', function ($attribute, $value, $fail) {
                $exists = ArsipFile::where('id', $value)->whereNull('arsip_id')->exists();
                if (!$exists) {
                    $fail('File ID #' . $value . ' tidak valid atau sudah terasosiasi dengan arsip lain.');
                }
            }],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipe_arsip' => 'tipe arsip',
            'kode_klasifikasi' => 'kode klasifikasi',
            'nomor_arsip_berkas' => 'nomor arsip/berkas',
            'uraian_informasi_arsip' => 'uraian informasi arsip',
            'kurun_waktu' => 'kurun waktu',
            'jumlah' => 'jumlah',
            'satuan' => 'satuan',
            'tingkat_perkembangan' => 'tingkat perkembangan',
            'nomor_boks' => 'nomor boks',
            'kondisi' => 'kondisi',
            'klasifikasi_keamanan' => 'klasifikasi keamanan',
            'status' => 'status',
            'unit_id' => 'unit/UPT',
            'jenis_pajak_id' => 'jenis pajak',
            'file_arsip' => 'file arsip',
        ];
    }
}