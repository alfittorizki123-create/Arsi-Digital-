<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArsipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_arsip' => ['required', 'string', 'max:100', 'unique:arsips,nomor_arsip'],
            'jenis_pajak_id' => ['required', 'exists:jenis_pajaks,id'],
            'nama_wajib_pajak' => ['required', 'string', 'max:255'],
            'tahun_arsip' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'nomor_rak' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['aktif', 'inaktif'])],
            'unit_id' => ['required', 'exists:units,id'],
            'file_arsip' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nomor_arsip' => 'nomor arsip',
            'jenis_pajak_id' => 'jenis pajak',
            'nama_wajib_pajak' => 'nama wajib pajak',
            'tahun_arsip' => 'tahun arsip',
            'nomor_rak' => 'nomor rak',
            'status' => 'status',
            'unit_id' => 'unit/UPT',
            'file_arsip' => 'file arsip',
        ];
    }
}
