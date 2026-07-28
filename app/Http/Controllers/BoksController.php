<?php

namespace App\Http\Controllers;

use App\Models\Boks;
use App\Models\Rak;
use App\Models\Unit;
use Illuminate\Http\Request;

class BoksController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nomor_boks' => ['required', 'integer', 'min:1'],
            'tahun' => ['required', 'integer', 'min:1990', 'max:2099'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'rak_id' => ['nullable', 'exists:raks,id'],
            'keterangan' => ['nullable', 'string'],
        ], [], [
            'nomor_boks' => 'nomor boks',
            'tahun' => 'tahun',
            'unit_id' => 'unit',
            'rak_id' => 'rak',
        ]);

        Boks::create($request->only(['nomor_boks', 'tahun', 'unit_id', 'rak_id', 'keterangan']));

        return redirect()->back()->with('success', 'Boks baru berhasil ditambahkan.');
    }

    public function update(Request $request, Boks $boks)
    {
        $request->validate([
            'nomor_boks' => ['required', 'integer', 'min:1'],
            'tahun' => ['required', 'integer', 'min:1990', 'max:2099'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'rak_id' => ['nullable', 'exists:raks,id'],
            'keterangan' => ['nullable', 'string'],
        ], [], [
            'nomor_boks' => 'nomor boks',
            'tahun' => 'tahun',
            'unit_id' => 'unit',
            'rak_id' => 'rak',
        ]);

        $boks->update($request->only(['nomor_boks', 'tahun', 'unit_id', 'rak_id', 'keterangan']));

        return redirect()->back()->with('success', 'Data boks berhasil diperbarui.');
    }

    public function destroy(Boks $boks)
    {
        // Unlink boks from arsips first
        $boks->arsips()->update(['boks_id' => null, 'nomor_boks' => null]);
        $boks->delete();

        return redirect()->back()->with('success', 'Boks berhasil dihapus.');
    }
}
