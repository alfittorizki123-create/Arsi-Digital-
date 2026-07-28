<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_unit' => ['required', 'string', 'max:255'],
            'kode_unit' => ['required', 'string', 'max:50', 'unique:units,kode_unit'],
            'nomor_rak' => ['nullable', 'string', 'max:100'],
        ], [], [
            'nama_unit' => 'nama unit',
            'kode_unit' => 'kode unit',
            'nomor_rak' => 'nomor rak',
        ]);

        Unit::create($data);

        return back()->with('success', 'Unit/UPT berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'nama_unit' => ['required', 'string', 'max:255'],
            'kode_unit' => ['required', 'string', 'max:50', Rule::unique('units', 'kode_unit')->ignore($unit->id)],
            'nomor_rak' => ['nullable', 'string', 'max:100'],
        ], [], [
            'nama_unit' => 'nama unit',
            'kode_unit' => 'kode unit',
            'nomor_rak' => 'nomor rak',
        ]);

        $unit->update($data);

        return back()->with('success', 'Unit/UPT berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->arsips()->exists()) {
            return back()->with('error', 'Unit/UPT tidak dapat dihapus karena masih dipakai data arsip.');
        }

        $unit->delete();

        return back()->with('success', 'Unit/UPT berhasil dihapus.');
    }
}
