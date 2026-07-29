<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArsipRequest;
use App\Http\Requests\UpdateArsipRequest;
use App\Models\Arsip;
use App\Models\ArsipFile;
use App\Models\Boks;
use App\Models\JenisPajak;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArsipController extends Controller
{
    public function pilihUnitUpload()
    {
        $rekapBoksMap = [
            'pekanbaru kota'   => ['order' => 1,  'boks' => 'Boks 1'],
            'mpp'               => ['order' => 2,  'boks' => 'Boks 2'],
            'bagan batu'       => ['order' => 3,  'boks' => 'Boks 3, 4'],
            'pujud'            => ['order' => 5,  'boks' => 'Boks 5'],
            'selat panjang'    => ['order' => 6,  'boks' => 'Boks 6'],
            'bangkinang'       => ['order' => 7,  'boks' => 'Boks 7, 8'],
            'bagan siapi'      => ['order' => 9,  'boks' => 'Boks 9'],
            'ujung tanjung'    => ['order' => 10, 'boks' => 'Boks 10'],
            'tembilahan'       => ['order' => 11, 'boks' => 'Boks 11, 12'],
            'kateman'          => ['order' => 13, 'boks' => 'Boks 13'],
            'kota baru'        => ['order' => 14, 'boks' => 'Boks 14'],
            'kempas'           => ['order' => 15, 'boks' => 'Boks 15'],
            'rengat'           => ['order' => 16, 'boks' => 'Boks 16, 17'],
            'air molek'        => ['order' => 18, 'boks' => 'Boks 18, 19'],
            'belilas'          => ['order' => 20, 'boks' => 'Boks 20'],
            'samkel inhu'      => ['order' => 21, 'boks' => 'Boks 21'],
            'dumai'            => ['order' => 22, 'boks' => 'Boks 22, 23'],
            'pinggir'          => ['order' => 24, 'boks' => 'Boks 24'],
            'duri'             => ['order' => 25, 'boks' => 'Boks 25'],
            'kubang'           => ['order' => 26, 'boks' => 'Boks 26'],
            'kampar kiri'      => ['order' => 27, 'boks' => 'Boks 27'],
            'siak'             => ['order' => 28, 'boks' => 'Boks 28, 29'],
            'bengkalis'        => ['order' => 30, 'boks' => 'Boks 30'],
            'simpang tiga'     => ['order' => 31, 'boks' => 'Boks 31'],
            'samkel 1'         => ['order' => 32, 'boks' => 'Boks 32'],
            'samkel 2'         => ['order' => 33, 'boks' => 'Boks 33'],
            'taluk kuantan'    => ['order' => 34, 'boks' => 'Boks 34, 35'],
            'singingi'         => ['order' => 36, 'boks' => 'Boks 36'],
            'baserah'          => ['order' => 37, 'boks' => 'Boks 37'],
            'kuantan mudik'    => ['order' => 39, 'boks' => 'Boks 39'],
            'kandis'           => ['order' => 40, 'boks' => 'Boks 40'],
            'perawang'         => ['order' => 41, 'boks' => 'Boks 41, 42'],
            'pangkalan kerinci'=> ['order' => 43, 'boks' => 'Boks 43, 44'],
            'pangkalan kuras'  => ['order' => 45, 'boks' => 'Boks 45'],
            'ukui'             => ['order' => 46, 'boks' => 'Boks 46'],
            'bandar sekijang'  => ['order' => 47, 'boks' => 'Boks 47'],
            'pasir pangaraian' => ['order' => 48, 'boks' => 'Boks 48, 49'],
            'ujung batu'       => ['order' => 50, 'boks' => 'Boks 50'],
            'kepenuhan'        => ['order' => 51, 'boks' => 'Boks 51'],
            'tambusai'         => ['order' => 52, 'boks' => 'Boks 52'],
            'tapung hilir'     => ['order' => 54, 'boks' => 'Boks 54'],
            'tapung'           => ['order' => 53, 'boks' => 'Boks 53'],
            'rumbai'           => ['order' => 55, 'boks' => 'Boks 55'],
            'panam'            => ['order' => 56, 'boks' => 'Boks 56'],
            'rupat'            => ['order' => 57, 'boks' => 'Boks 57'],
        ];

        $units = Unit::withCount('arsips')
            ->with(['arsips' => function($q) {
                $q->select('id', 'unit_id', 'nomor_boks')->whereNotNull('nomor_boks');
            }])
            ->get();

        $units->each(function($unit) use ($rekapBoksMap) {
            $lower = strtolower($unit->nama_unit);
            $matched = null;
            foreach ($rekapBoksMap as $kw => $info) {
                if (str_contains($lower, $kw)) {
                    $matched = $info;
                    break;
                }
            }

            if ($matched) {
                $unit->sort_order = $matched['order'];
                $unit->boks_display = $matched['boks'];
            } else {
                $unit->sort_order = 999;
                $unit->boks_display = null;
            }
        });

        $units = $units->sortBy([
            ['sort_order', 'asc'],
            ['nama_unit', 'asc'],
        ])->values();

        return view('arsips.pilih_unit', compact('units'));
    }

    public function index(Request $request)
    {
        // 1. Simpan unit aktif ke session jika user memilih unit
        if ($request->filled('unit_id')) {
            session(['last_unit_id' => $request->unit_id]);
        }

        // 2. Jika tidak ada unit_id di URL dan tidak ada filter aktif, otomatis gunakan unit terakhir dari session
        if (!$request->filled('unit_id') && !$request->filled('search') && !$request->filled('jenis_pajak_id') && !$request->filled('kurun_waktu') && !$request->filled('bulan') && !$request->filled('tipe_arsip') && !$request->filled('status') && !$request->filled('kondisi') && !$request->filled('klasifikasi_keamanan')) {
            if (session()->has('last_unit_id') && Unit::where('id', session('last_unit_id'))->exists()) {
                return redirect()->route('arsips.index', ['unit_id' => session('last_unit_id')]);
            }
            return redirect()->route('arsips.pilih_unit');
        }

        $query = Arsip::with(['jenisPajaks', 'unit', 'boks.rak', 'files'])->oldest();

        if ($request->filled('search')) {
            $rawSearch = trim($request->search);
            $terms = array_filter(explode(' ', preg_replace('/\s+/', ' ', $rawSearch)));

            foreach ($terms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('kode_klasifikasi', 'like', "%{$term}%")
                      ->orWhere('uraian_informasi_arsip', 'like', "%{$term}%")
                      ->orWhere('nomor_arsip_berkas', 'like', "%{$term}%")
                      ->orWhere('kurun_waktu', 'like', "%{$term}%")
                      ->orWhere('nomor_boks', 'like', "%{$term}%")
                      ->orWhere('kondisi', 'like', "%{$term}%")
                      ->orWhere('klasifikasi_keamanan', 'like', "%{$term}%")
                      ->orWhereHas('unit', function ($uQuery) use ($term) {
                          $uQuery->where('nama_unit', 'like', "%{$term}%")
                                 ->orWhere('kode_unit', 'like', "%{$term}%");
                      })
                       ->orWhereHas('jenisPajaks', function ($jpQuery) use ($term) {
                           $jpQuery->where('nama_jenis_pajak', 'like', "%{$term}%")
                                  ->orWhere('kode', 'like', "%{$term}%");
                       });
                });
            }
        }

        if ($request->filled('jenis_pajak_id')) {
            $jpId = $request->jenis_pajak_id;
            $query->whereHas('jenisPajaks', function ($q) use ($jpId) {
                $q->where('jenis_pajaks.id', $jpId);
            });
        }
        $currentUnit = null;
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
            $currentUnit = Unit::find($request->unit_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kurun_waktu')) {
            $query->where('kurun_waktu', $request->kurun_waktu);
        }
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
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

        $arsips = $query->paginate(30)->withQueryString();
        $groupedArsips = $arsips->getCollection()->groupBy(function ($item) use ($currentUnit) {
            if ($currentUnit) {
                return $item->kurun_waktu;
            }
            return ($item->unit_id ?? '0') . '_' . ($item->kurun_waktu ?? '0');
        });
        $jenisPajaks = JenisPajak::orderBy('nama_jenis_pajak')->get();
        $units = Unit::orderBy('nama_unit')->get();
        $tahuns = Arsip::select('kurun_waktu')
            ->distinct()
            ->orderByDesc('kurun_waktu')
            ->pluck('kurun_waktu');

        return view('arsips.index', compact('arsips', 'groupedArsips', 'jenisPajaks', 'units', 'tahuns', 'currentUnit'));
    }

    public function create()
    {
        $jenisPajaks = JenisPajak::orderBy('nama_jenis_pajak')->get();
        $units = Unit::orderBy('nama_unit')->get();

        return view('arsips.create', compact('jenisPajaks', 'units'));
    }

    public function store(StoreArsipRequest $request)
    {
        $data = $request->safe()->except(['file_arsip', 'files']);

        if (empty($data['satuan'])) {
            $data['satuan'] = 'Berkas';
        }
        if (empty($data['kondisi'])) {
            $data['kondisi'] = 'Baik';
        }
        if (empty($data['klasifikasi_keamanan'])) {
            $data['klasifikasi_keamanan'] = 'Terbuka';
        }

        if (!empty($data['nomor_boks']) && !empty($data['kurun_waktu'])) {
            $boks = Boks::findOrCreateFromNomor($data['nomor_boks'], $data['kurun_waktu'], $data['unit_id'] ?? null);
            if ($boks) $data['boks_id'] = $boks->id;
        } else {
            $data['boks_id'] = null;
        }

        $arsip = Arsip::create($data);

        if ($request->filled('jenis_pajak_ids') && is_array($request->jenis_pajak_ids)) {
            $jpIds = array_filter($request->jenis_pajak_ids);
            $arsip->jenisPajaks()->sync($jpIds);
        }

        // Asosiasikan file yang sudah diunggah via AJAX (uploaded_file_ids)
        if ($request->filled('uploaded_file_ids') && is_array($request->input('uploaded_file_ids'))) {
            ArsipFile::whereIn('id', $request->input('uploaded_file_ids'))->update(['arsip_id' => $arsip->id]);
        }

        // Prosess Upload Multiple Files (files[])
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $this->storeArsipFile($arsip, $file);
                }
            }
        }

        // Single file legacy fallback
        if ($request->hasFile('file_arsip') && $request->file('file_arsip')->isValid()) {
            $this->storeArsipFile($arsip, $request->file('file_arsip'));
        }

        $redirectUrl = !empty($arsip->unit_id)
            ? route('arsips.index', ['unit_id' => $arsip->unit_id])
            : route('arsips.index');

        return redirect($redirectUrl)
            ->with('success', 'Data arsip berhasil ditambahkan.');
    }

    public function show(Arsip $arsip)
    {
        $arsip->load(['jenisPajaks', 'unit', 'files']);

        return view('arsips.show', compact('arsip'));
    }

    public function edit(Arsip $arsip)
    {
        $arsip->load(['files', 'jenisPajaks']);
        $jenisPajaks = JenisPajak::orderBy('nama_jenis_pajak')->get();
        $units = Unit::orderBy('nama_unit')->get();

        return view('arsips.edit', compact('arsip', 'jenisPajaks', 'units'));
    }

    public function update(UpdateArsipRequest $request, Arsip $arsip)
    {
        $data = $request->safe()->except(['file_arsip', 'files', 'jenis_pajak_ids']);

        if (!empty($data['nomor_boks']) && !empty($data['kurun_waktu'])) {
            $boks = Boks::findOrCreateFromNomor($data['nomor_boks'], $data['kurun_waktu'], $data['unit_id'] ?? $arsip->unit_id);
            if ($boks) $data['boks_id'] = $boks->id;
        } else {
            $data['boks_id'] = null;
        }

        if ($request->boolean('hapus_file') && $arsip->path_file) {
            $this->deleteFile($arsip->path_file);
            $data['path_file'] = null;
            $data['tipe_file'] = null;
        }

        $arsip->update($data);

        if ($request->has('jenis_pajak_ids')) {
            $jpIds = is_array($request->jenis_pajak_ids) ? array_filter($request->jenis_pajak_ids) : [];
            $arsip->jenisPajaks()->sync($jpIds);
        }

        // Upload new multiple files if provided
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $this->storeArsipFile($arsip, $file);
                }
            }
        }

        if ($request->hasFile('file_arsip') && $request->file('file_arsip')->isValid()) {
            $this->storeArsipFile($arsip, $request->file('file_arsip'));
        }

        $unitId = $arsip->unit_id;
        $redirectUrl = !empty($unitId)
            ? route('arsips.index', ['unit_id' => $unitId])
            : route('arsips.index');

        return redirect($redirectUrl)
            ->with('success', 'Data arsip berhasil diperbarui.');
    }

    public function destroy(Arsip $arsip)
    {
        foreach ($arsip->files as $file) {
            Storage::disk('public')->delete($file->path_file);
            $file->delete();
        }

        if ($arsip->path_file) {
            $this->deleteFile($arsip->path_file);
        }

        $unitId = $arsip->unit_id;

        $arsip->delete();

        return redirect()
            ->route('arsips.index', $unitId ? ['unit_id' => $unitId] : [])
            ->with('success', 'Data arsip berhasil dihapus.');
    }

    public function destroyFile(\App\Models\ArsipFile $arsipFile)
    {
        Storage::disk('public')->delete($arsipFile->path_file);
        $arsipFile->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'File lampiran berhasil dihapus.');
    }

    public function uploadSingleFile(Request $request, Arsip $arsip)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:102400'],
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $allowedExts = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'];

        if (!in_array($ext, $allowedExts)) {
            return response()->json(['error' => 'Ekstensi file tidak didukung: .' . $ext], 422);
        }

        if (!$file->isValid()) {
            return response()->json(['error' => 'File tidak valid atau rusak.'], 422);
        }

        $arsipFile = $this->storeArsipFile($arsip, $file);

        return response()->json([
            'success' => true,
            'file' => [
                'id' => $arsipFile->id,
                'nama_file' => $arsipFile->nama_file,
                'url' => $arsipFile->url,
                'ukuran' => $arsipFile->formatted_size,
                'is_image' => $arsipFile->is_image,
                'is_pdf' => $arsipFile->is_pdf,
            ]
        ]);
    }

    public function uploadTempFile(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:102400'],
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $allowedExts = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'];

        if (!in_array($ext, $allowedExts)) {
            return response()->json(['error' => 'Ekstensi file tidak didukung: .' . $ext], 422);
        }

        if (!$file->isValid()) {
            return response()->json(['error' => 'File tidak valid.'], 422);
        }

        $origName = $file->getClientOriginalName();
        $basename = Str::slug(pathinfo($origName, PATHINFO_FILENAME));
        $filename = time() . '_' . Str::random(5) . '_' . ($basename ?: 'arsip') . '.' . $ext;
        
        $file->storeAs('arsip', $filename, 'public');
        $mime = $file->getMimeType();
        $size = $file->getSize();

        $arsipFile = \App\Models\ArsipFile::create([
            'arsip_id' => null,
            'nama_file' => $origName,
            'path_file' => 'arsip/' . $filename,
            'tipe_file' => $mime,
            'ukuran_file' => $size,
        ]);

        return response()->json([
            'success' => true,
            'file' => [
                'id' => $arsipFile->id,
                'nama_file' => $arsipFile->nama_file,
                'url' => $arsipFile->url,
                'ukuran' => $arsipFile->formatted_size,
                'is_image' => $arsipFile->is_image,
                'is_pdf' => $arsipFile->is_pdf,
            ]
        ]);
    }

    private function storeArsipFile(Arsip $arsip, $file): \App\Models\ArsipFile
    {
        $origName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $basename = Str::slug(pathinfo($origName, PATHINFO_FILENAME));
        $filename = time() . '_' . Str::random(5) . '_' . ($basename ?: 'arsip') . '.' . $extension;
        
        $file->storeAs('arsip', $filename, 'public');

        $mime = $file->getMimeType();
        $size = $file->getSize();

        if (empty($arsip->path_file)) {
            $arsip->update([
                'path_file' => $filename,
                'tipe_file' => str_starts_with($mime, 'image/') ? 'image' : 'pdf',
            ]);
        }

        return \App\Models\ArsipFile::create([
            'arsip_id' => $arsip->id,
            'nama_file' => $origName,
            'path_file' => 'arsip/' . $filename,
            'tipe_file' => $mime,
            'ukuran_file' => $size,
        ]);
    }

    private function deleteFile(string $filename): void
    {
        Storage::disk('public')->delete('arsip/' . $filename);
    }
}