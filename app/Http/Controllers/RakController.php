<?php

namespace App\Http\Controllers;

use App\Models\Boks;
use App\Models\Rak;
use Illuminate\Http\Request;

class RakController extends Controller
{
    public function index(Request $request)
    {
        $query = Rak::with(['boks.unit', 'boks.arsips']);

        if ($request->filled('search')) {
            $query->where('nomor_rak', 'like', '%' . $request->search . '%')
                ->orWhere('keterangan', 'like', '%' . $request->search . '%');
        }

        $raks = $query->orderBy('nomor_rak')->paginate(12)->withQueryString();

        // Ambil boks yang belum dimasukkan ke rak manapun
        $unassignedBoks = Boks::with(['unit', 'arsips'])
            ->whereNull('rak_id')
            ->orderBy('tahun', 'desc')
            ->orderBy('nomor_boks', 'asc')
            ->get();

        // Pre-compute range_berkas untuk semua boks dalam 1 batch query
        $allBoks = $raks->getCollection()->flatMap->boks->merge($unassignedBoks);
        Boks::preloadRangeBerkas($allBoks);

        return view('raks.index', compact('raks', 'unassignedBoks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_rak' => ['required', 'string', 'max:50', 'unique:raks,nomor_rak'],
            'keterangan' => ['nullable', 'string'],
        ], [], [
            'nomor_rak' => 'nomor rak',
        ]);

        Rak::create($request->only(['nomor_rak', 'keterangan']));

        return redirect()->route('raks.index')->with('success', 'Rak berhasil ditambahkan.');
    }

    public function update(Request $request, Rak $rak)
    {
        $request->validate([
            'nomor_rak' => ['required', 'string', 'max:50', 'unique:raks,nomor_rak,' . $rak->id],
            'keterangan' => ['nullable', 'string'],
        ], [], [
            'nomor_rak' => 'nomor rak',
        ]);

        $rak->update($request->only(['nomor_rak', 'keterangan']));

        return redirect()->route('raks.index')->with('success', 'Data rak berhasil diperbarui.');
    }

    public function destroy(Rak $rak)
    {
        // Set boks.rak_id = null saat rak dihapus
        $rak->boks()->update(['rak_id' => null]);
        $rak->delete();

        return redirect()->route('raks.index')->with('success', 'Rak berhasil dihapus.');
    }

    public function assignBoks(Request $request, Rak $rak)
    {
        $request->validate([
            'boks_ids' => ['nullable', 'array'],
            'boks_ids.*' => ['exists:boks,id'],
        ]);

        // Lepas boks lama yang sebelumnya di rak ini jika tidak terpilih lagi
        Boks::where('rak_id', $rak->id)->update(['rak_id' => null]);

        // Tetapkan boks baru ke rak ini
        if ($request->filled('boks_ids')) {
            Boks::whereIn('id', $request->boks_ids)->update(['rak_id' => $rak->id]);
        }

        return redirect()->route('raks.index')->with('success', 'Boks berhasil dimasukkan ke Rak ' . $rak->nomor_rak);
    }
}
