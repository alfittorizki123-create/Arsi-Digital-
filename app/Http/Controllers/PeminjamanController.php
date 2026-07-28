<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function json(Peminjaman $peminjaman)
    {
        $a = $peminjaman->arsip;
        $arsipLabel = ($a->kode_klasifikasi ?? 'Tanpa Kode') . ' - ' . ($a->uraian_informasi_arsip ?? 'Tanpa Uraian') . ' [' . ($a->unit?->nama_unit ?? '-') . ']';

        return response()->json([
            'arsip_id' => $peminjaman->arsip_id,
            'arsip_label' => $arsipLabel,
            'nama_peminjam' => $peminjaman->nama_peminjam,
            'instansi' => $peminjaman->instansi,
            'telp' => $peminjaman->telp,
            'keperluan' => $peminjaman->keperluan,
            'tanggal_pinjam' => $peminjaman->tanggal_pinjam->format('Y-m-d'),
            'tanggal_kembali_rencana' => $peminjaman->tanggal_kembali_rencana?->format('Y-m-d'),
            'keterangan' => $peminjaman->keterangan,
        ]);
    }

    public function searchArsip(Request $request)
    {
        $q = $request->get('q', '');
        if (mb_strlen(trim($q)) < 1) {
            return response()->json([]);
        }

        $results = Arsip::with('unit')
            ->where(function ($query) use ($q) {
                $query->where('kode_klasifikasi', 'like', "%{$q}%")
                      ->orWhere('uraian_informasi_arsip', 'like', "%{$q}%")
                      ->orWhereHas('unit', function ($u) use ($q) {
                          $u->where('nama_unit', 'like', "%{$q}%")
                            ->orWhere('kode_unit', 'like', "%{$q}%");
                      });
            })
            ->orderBy('kode_klasifikasi')
            ->limit(15)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'label' => ($a->kode_klasifikasi ?? 'Tanpa Kode') . ' - ' . ($a->uraian_informasi_arsip ?? 'Tanpa Uraian') . ' [' . ($a->unit?->nama_unit ?? '-') . ']',
            ]);

        return response()->json($results);
    }

    public function index(Request $request)
    {
        $query = Peminjaman::with(['arsip.unit'])->latest('tanggal_pinjam');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_peminjam', 'like', "%{$s}%")
                  ->orWhere('instansi', 'like', "%{$s}%")
                  ->orWhere('keperluan', 'like', "%{$s}%")
                  ->orWhereHas('arsip', function ($q2) use ($s) {
                      $q2->where('kode_klasifikasi', 'like', "%{$s}%")
                         ->orWhere('uraian_informasi_arsip', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $peminjamen = $query->paginate(20)->withQueryString();

        return view('peminjaman.index', compact('peminjamen'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'arsip_id' => ['required', 'exists:arsips,id'],
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'telp' => ['nullable', 'string', 'max:50'],
            'keperluan' => ['nullable', 'string'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['nullable', 'date', 'after_or_equal:tanggal_pinjam'],
            'keterangan' => ['nullable', 'string'],
        ], [], [
            'arsip_id' => 'arsip',
            'nama_peminjam' => 'nama peminjam',
            'instansi' => 'instansi',
            'tanggal_pinjam' => 'tanggal pinjam',
            'tanggal_kembali_rencana' => 'tanggal kembali rencana',
        ]);

        $existingPinjam = Peminjaman::where('arsip_id', $data['arsip_id'])
            ->where('status', 'dipinjam')
            ->exists();
        if ($existingPinjam) {
            return back()->withInput()->with('error', 'Arsip ini sedang dipinjam dan belum dikembalikan.');
        }

        $data['status'] = 'dipinjam';

        Peminjaman::create($data);

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dicatat.');
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $data = $request->validate([
            'arsip_id' => ['required', 'exists:arsips,id'],
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'telp' => ['nullable', 'string', 'max:50'],
            'keperluan' => ['nullable', 'string'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['nullable', 'date', 'after_or_equal:tanggal_pinjam'],
            'keterangan' => ['nullable', 'string'],
        ], [], [
            'arsip_id' => 'arsip',
            'nama_peminjam' => 'nama peminjam',
            'instansi' => 'instansi',
            'tanggal_pinjam' => 'tanggal pinjam',
            'tanggal_kembali_rencana' => 'tanggal kembali rencana',
        ]);

        $existingPinjam = Peminjaman::where('arsip_id', $data['arsip_id'])
            ->where('id', '!=', $peminjaman->id)
            ->where('status', 'dipinjam')
            ->exists();
        if ($existingPinjam) {
            return back()->withInput()->with('error', 'Arsip ini sedang dipinjam orang lain dan belum dikembalikan.');
        }

        $peminjaman->update($data);

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        $peminjaman->delete();

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    public function kembalikan(Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'dikembalikan') {
            return redirect()->route('peminjaman.index')->with('error', 'Arsip ini sudah ditandai dikembalikan sebelumnya.');
        }

        $peminjaman->update([
            'tanggal_dikembalikan' => now(),
            'status' => 'dikembalikan',
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Arsip berhasil ditandai sudah dikembalikan.');
    }
}
