<?php

namespace App\Http\Controllers;

use App\Models\JenisPajak;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JenisPajakController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_jenis_pajak' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', 'unique:jenis_pajaks,kode'],
        ], [], [
            'nama_jenis_pajak' => 'nama jenis pajak',
            'kode' => 'kode',
        ]);

        JenisPajak::create($data);

        return redirect()
            ->route('pengaturan', ['tab' => 'jenis-pajak'])
            ->with('success', 'Jenis pajak berhasil ditambahkan.');
    }

    public function update(Request $request, JenisPajak $jenisPajak)
    {
        $data = $request->validate([
            'nama_jenis_pajak' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', Rule::unique('jenis_pajaks', 'kode')->ignore($jenisPajak->id)],
        ], [], [
            'nama_jenis_pajak' => 'nama jenis pajak',
            'kode' => 'kode',
        ]);

        $jenisPajak->update($data);

        return redirect()
            ->route('pengaturan', ['tab' => 'jenis-pajak'])
            ->with('success', 'Jenis pajak berhasil diperbarui.');
    }

    public function destroy(JenisPajak $jenisPajak)
    {
        if ($jenisPajak->arsips()->exists()) {
            return redirect()
                ->route('pengaturan', ['tab' => 'jenis-pajak'])
                ->with('error', 'Jenis pajak tidak dapat dihapus karena masih dipakai data arsip.');
        }

        $jenisPajak->delete();

        return redirect()
            ->route('pengaturan', ['tab' => 'jenis-pajak'])
            ->with('success', 'Jenis pajak berhasil dihapus.');
    }
}
