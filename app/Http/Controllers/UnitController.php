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
        ], [], [
            'nama_unit' => 'nama unit',
            'kode_unit' => 'kode unit',
        ]);

        Unit::create($data);

        return redirect()
            ->route('pengaturan', ['tab' => 'unit'])
            ->with('success', 'Unit/UPT berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'nama_unit' => ['required', 'string', 'max:255'],
            'kode_unit' => ['required', 'string', 'max:50', Rule::unique('units', 'kode_unit')->ignore($unit->id)],
        ], [], [
            'nama_unit' => 'nama unit',
            'kode_unit' => 'kode unit',
        ]);

        $unit->update($data);

        return redirect()
            ->route('pengaturan', ['tab' => 'unit'])
            ->with('success', 'Unit/UPT berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->arsips()->exists()) {
            return redirect()
                ->route('pengaturan', ['tab' => 'unit'])
                ->with('error', 'Unit/UPT tidak dapat dihapus karena masih dipakai data arsip.');
        }

        $unit->delete();

        return redirect()
            ->route('pengaturan', ['tab' => 'unit'])
            ->with('success', 'Unit/UPT berhasil dihapus.');
    }
}
