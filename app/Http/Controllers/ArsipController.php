<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArsipRequest;
use App\Http\Requests\UpdateArsipRequest;
use App\Models\Arsip;
use App\Models\JenisPajak;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = Arsip::with(['jenisPajak', 'unit'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_klasifikasi', 'like', "%{$search}%")
                    ->orWhere('uraian_informasi_arsip', 'like', "%{$search}%")
                    ->orWhere('nomor_arsip_berkas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_pajak_id')) {
            $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        }
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kurun_waktu')) {
            $query->where('kurun_waktu', $request->kurun_waktu);
        }
        if ($request->filled('tipe_arsip')) {
            $query->where('tipe_arsip', $request->tipe_arsip);
        }
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->filled('klasifikasi_keamanan')) {
            $query->where('klasifikasi_keamanan', $request->klasifikasi_keamanan);
        }

        $arsips = $query->paginate(15)->withQueryString();
        $jenisPajaks = JenisPajak::orderBy('nama_jenis_pajak')->get();
        $units = Unit::orderBy('nama_unit')->get();
        $tahuns = Arsip::select('kurun_waktu')
            ->distinct()
            ->orderByDesc('kurun_waktu')
            ->pluck('kurun_waktu');

        return view('arsips.index', compact('arsips', 'jenisPajaks', 'units', 'tahuns'));
    }

    public function create()
    {
        $jenisPajaks = JenisPajak::orderBy('nama_jenis_pajak')->get();
        $units = Unit::orderBy('nama_unit')->get();

        return view('arsips.create', compact('jenisPajaks', 'units'));
    }

    public function store(StoreArsipRequest $request)
    {
        $data = $request->safe()->except(['file_arsip']);

        if (empty($data['satuan'])) {
            $data['satuan'] = 'Berkas';
        }
        if (empty($data['kondisi'])) {
            $data['kondisi'] = 'Baik';
        }
        if (empty($data['klasifikasi_keamanan'])) {
            $data['klasifikasi_keamanan'] = 'Terbuka';
        }

        if ($request->hasFile('file_arsip')) {
            $uploaded = $this->storeFile($request->file('file_arsip'));
            $data['path_file'] = $uploaded['path'];
            $data['tipe_file'] = $uploaded['tipe'];
        }

        Arsip::create($data);

        return redirect()
            ->route('arsips.index')
            ->with('success', 'Data arsip berhasil ditambahkan.');
    }

    public function show(Arsip $arsip)
    {
        $arsip->load(['jenisPajak', 'unit']);

        return view('arsips.show', compact('arsip'));
    }

    public function edit(Arsip $arsip)
    {
        $jenisPajaks = JenisPajak::orderBy('nama_jenis_pajak')->get();
        $units = Unit::orderBy('nama_unit')->get();

        return view('arsips.edit', compact('arsip', 'jenisPajaks', 'units'));
    }

    public function update(UpdateArsipRequest $request, Arsip $arsip)
    {
        $data = $request->safe()->except(['file_arsip', 'hapus_file']);

        if ($request->boolean('hapus_file') && $arsip->path_file) {
            $this->deleteFile($arsip->path_file);
            $data['path_file'] = null;
            $data['tipe_file'] = null;
        }

        if ($request->hasFile('file_arsip')) {
            if ($arsip->path_file) {
                $this->deleteFile($arsip->path_file);
            }
            $uploaded = $this->storeFile($request->file('file_arsip'));
            $data['path_file'] = $uploaded['path'];
            $data['tipe_file'] = $uploaded['tipe'];
        }

        $arsip->update($data);

        return redirect()
            ->route('arsips.index')
            ->with('success', 'Data arsip berhasil diperbarui.');
    }

    public function destroy(Arsip $arsip)
    {
        if ($arsip->path_file) {
            $this->deleteFile($arsip->path_file);
        }

        $arsip->delete();

        return redirect()
            ->route('arsips.index')
            ->with('success', 'Data arsip berhasil dihapus.');
    }

    private function storeFile($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $basename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = time() . '_' . ($basename ?: 'arsip') . '.' . $extension;
        $file->storeAs('arsip', $filename, 'public');

        $mime = $file->getMimeType();

        return [
            'path' => $filename,
            'tipe' => str_starts_with($mime, 'image/') ? 'image' : 'pdf',
        ];
    }

    private function deleteFile(string $filename): void
    {
        Storage::disk('public')->delete('arsip/' . $filename);
    }
}