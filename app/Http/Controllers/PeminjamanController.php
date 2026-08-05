<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Peminjaman;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function json(Peminjaman $peminjaman)
    {
        $peminjaman->load(['arsips.unit', 'arsips.boks']);
        
        $arsipsData = $peminjaman->arsips->map(function($a) {
            $boksStr = '-';
            if ($a->boks) {
                // Determine absolute position of this arsip inside its boks to keep numbering consistent
                $no = \App\Models\Arsip::where('boks_id', $a->boks_id)
                                        ->where('id', '<=', $a->id)
                                        ->count();
                $boksStr = "Boks {$a->boks->nomor_boks} No {$no}";
            }

            return [
                'id' => $a->id,
                'kode' => $a->kode_klasifikasi ?? '-',
                'uraian' => $a->uraian_informasi_arsip ?? '-',
                'unit' => $a->unit?->nama_unit ?? '-',
                'boks' => $boksStr,
                'kurun' => $a->kurun_waktu ?? '-',
                'label' => ($a->kode_klasifikasi ?? 'Tanpa Kode') . ' - ' . ($a->uraian_informasi_arsip ?? 'Tanpa Uraian') . ' [' . ($a->unit?->nama_unit ?? '-') . ']',
            ];
        });

        return response()->json([
            'id' => $peminjaman->id,
            'arsip_ids' => $arsipsData->pluck('id'),
            'arsips' => $arsipsData,
            'nama_peminjam' => $peminjaman->nama_peminjam,
            'instansi' => $peminjaman->instansi,
            'telp' => $peminjaman->telp,
            'keperluan' => $peminjaman->keperluan,
            'tanggal_pinjam' => $peminjaman->tanggal_pinjam?->format('d/m/Y'),
            'tanggal_pinjam_raw' => $peminjaman->tanggal_pinjam?->format('Y-m-d'),
            'tanggal_kembali_rencana' => $peminjaman->tanggal_kembali_rencana?->format('d/m/Y'),
            'tanggal_kembali_rencana_raw' => $peminjaman->tanggal_kembali_rencana?->format('Y-m-d'),
            'tanggal_dikembalikan' => $peminjaman->tanggal_dikembalikan?->format('d/m/Y H:i'),
            'status' => $peminjaman->status,
            'status_label' => $peminjaman->status_label,
            'status_badge' => $peminjaman->status_badge,
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

    public function arsipsByUnit(Request $request)
    {
        $unitId = $request->get('unit_id');
        if (!$unitId) {
            return response()->json([]);
        }

        $arsips = Arsip::with(['unit', 'boks'])
            ->where('unit_id', $unitId)
            ->orderBy('nomor_boks')
            ->orderBy('id')
            ->get()
            ->groupBy(fn($a) => $a->boks_id ? "boks_{$a->boks_id}" : 'tanpa_boks');

        $result = [];
        foreach ($arsips as $groupKey => $items) {
            $boks = $items->first()->boks;
            $groupLabel = $boks ? "Boks {$boks->nomor_boks}" : 'Tanpa Boks';
            
            $counter = 1;

            $result[] = [
                'group' => $groupLabel,
                'items' => $items->map(function($a) use (&$counter, $boks) {
                    $itemNo = $boks ? "Boks {$boks->nomor_boks} No {$counter}" : "No {$counter}";
                    $counter++;
                    return [
                        'id' => $a->id,
                        'kode' => $a->kode_klasifikasi ?? '-',
                        'boks_label' => $itemNo,
                        'uraian' => $a->uraian_informasi_arsip ?? '-',
                        'label' => ($a->kode_klasifikasi ?? 'Tanpa Kode') . ' - ' . ($a->uraian_informasi_arsip ?? 'Tanpa Uraian'),
                    ];
                }),
            ];
        }

        return response()->json($result);
    }

    public function index(Request $request)
    {
        $query = Peminjaman::with(['arsips.unit'])->latest('id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_peminjam', 'like', "%{$s}%")
                  ->orWhere('instansi', 'like', "%{$s}%")
                  ->orWhere('keperluan', 'like', "%{$s}%")
                  ->orWhereHas('arsips', function ($q2) use ($s) {
                      $q2->where('kode_klasifikasi', 'like', "%{$s}%")
                         ->orWhere('uraian_informasi_arsip', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $peminjamen = $query->paginate(7)->withQueryString();
        $units = Unit::orderBy('nama_unit')->get();

        return view('peminjaman.index', compact('peminjamen', 'units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'arsip_ids' => ['required', 'array', 'min:1'],
            'arsip_ids.*' => ['required', 'exists:arsips,id'],
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'telp' => ['nullable', 'string', 'max:50'],
            'keperluan' => ['nullable', 'string'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['nullable', 'date', 'after_or_equal:tanggal_pinjam'],
            'keterangan' => ['nullable', 'string'],
        ], [], [
            'arsip_ids' => 'arsip',
            'nama_peminjam' => 'nama peminjam',
            'instansi' => 'instansi',
            'tanggal_pinjam' => 'tanggal pinjam',
            'tanggal_kembali_rencana' => 'tanggal kembali rencana',
        ]);

        $existingIds = DB::table('peminjaman_arsip')
            ->join('peminjamen', 'peminjaman_arsip.peminjaman_id', '=', 'peminjamen.id')
            ->whereIn('peminjaman_arsip.arsip_id', $data['arsip_ids'])
            ->where('peminjamen.status', 'dipinjam')
            ->pluck('peminjaman_arsip.arsip_id')
            ->unique()
            ->toArray();

        $conflictIds = array_intersect($data['arsip_ids'], $existingIds);
        if (!empty($conflictIds)) {
            return back()->withInput()->with('error', 'Beberapa arsip sedang dipinjam dan belum dikembalikan.');
        }

        $data['status'] = 'dipinjam';
        $arsipIds = $data['arsip_ids'];
        unset($data['arsip_ids']);

        $peminjaman = Peminjaman::create($data);
        $peminjaman->arsips()->sync($arsipIds);

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dicatat.');
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $data = $request->validate([
            'arsip_ids' => ['required', 'array', 'min:1'],
            'arsip_ids.*' => ['required', 'exists:arsips,id'],
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'telp' => ['nullable', 'string', 'max:50'],
            'keperluan' => ['nullable', 'string'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['nullable', 'date', 'after_or_equal:tanggal_pinjam'],
            'keterangan' => ['nullable', 'string'],
        ], [], [
            'arsip_ids' => 'arsip',
            'nama_peminjam' => 'nama peminjam',
            'instansi' => 'instansi',
            'tanggal_pinjam' => 'tanggal pinjam',
            'tanggal_kembali_rencana' => 'tanggal kembali rencana',
        ]);

        $existingIds = DB::table('peminjaman_arsip')
            ->join('peminjamen', 'peminjaman_arsip.peminjaman_id', '=', 'peminjamen.id')
            ->whereIn('peminjaman_arsip.arsip_id', $data['arsip_ids'])
            ->where('peminjamen.id', '!=', $peminjaman->id)
            ->where('peminjamen.status', 'dipinjam')
            ->pluck('peminjaman_arsip.arsip_id')
            ->unique()
            ->toArray();

        $conflictIds = array_intersect($data['arsip_ids'], $existingIds);
        if (!empty($conflictIds)) {
            return back()->withInput()->with('error', 'Beberapa arsip sedang dipinjam orang lain dan belum dikembalikan.');
        }

        $arsipIds = $data['arsip_ids'];
        unset($data['arsip_ids']);

        $peminjaman->update($data);
        $peminjaman->arsips()->sync($arsipIds);

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

    public function batalKembali(Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'dipinjam') {
            return redirect()->route('peminjaman.index')->with('error', 'Arsip ini sudah berstatus dipinjam.');
        }

        $peminjaman->update([
            'tanggal_dikembalikan' => null,
            'status' => 'dipinjam',
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Arsip berhasil ditandai sedang dipinjam.');
    }
}
