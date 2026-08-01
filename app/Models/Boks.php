<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boks extends Model
{
    protected $table = 'boks';

    protected $fillable = [
        'nomor_boks',
        'tahun',
        'rak_id',
        'unit_id',
        'keterangan',
    ];

    protected $casts = [
        'nomor_boks' => 'integer',
        'tahun' => 'integer',
    ];

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function arsips()
    {
        return $this->hasMany(Arsip::class, 'boks_id');
    }

    public static function findOrCreateFromNomor($nomorBoks, $tahun, $unitId = null): ?self
    {
        if (empty($nomorBoks)) return null;

        preg_match('/\d+/', (string) $nomorBoks, $matches);
        if (empty($matches[0])) return null;

        $nomorBoksInt = (int) $matches[0];
        $attributes = ['nomor_boks' => $nomorBoksInt, 'tahun' => (int) $tahun];
        if ($unitId) {
            $attributes['unit_id'] = $unitId;
        }

        $boks = static::firstOrCreate($attributes);

        if ($unitId && !$boks->unit_id) {
            $boks->update(['unit_id' => $unitId]);
        }

        return $boks;
    }

    /**
     * Pre-load range_berkas for a collection of Boks in batch (1 query per unit+tahun pair).
     * This avoids N+1 query problems when displaying many boks on one page.
     */
    public static function preloadRangeBerkas($boksCollection): void
    {
        // Collect all unique unit_id + tahun pairs
        $pairs = collect();
        foreach ($boksCollection as $boks) {
            if ($boks->unit_id && $boks->tahun) {
                $key = "{$boks->unit_id}_{$boks->tahun}";
                $pairs[$key] = ['unit_id' => $boks->unit_id, 'tahun' => $boks->tahun];
            }
        }

        if ($pairs->isEmpty()) return;

        // Load all arsip positions for all pairs in one batch
        $arsipData = [];
        foreach ($pairs as $key => $pair) {
            $arsipData[$key] = Arsip::where('unit_id', $pair['unit_id'])
                ->where('kurun_waktu', $pair['tahun'])
                ->orderBy('id', 'asc')
                ->pluck('boks_id', 'id');
        }

        // Compute and inject range_berkas into each boks
        foreach ($boksCollection as $boks) {
            if (!$boks->unit_id || !$boks->tahun) {
                $boks->preloaded_range_berkas = null;
                continue;
            }

            $cacheKey = "{$boks->unit_id}_{$boks->tahun}";
            $allArsips = $arsipData[$cacheKey] ?? collect();
            $myArsipIds = $boks->arsips->pluck('id')->toArray();
            $positions = [];
            $index = 0;
            foreach ($allArsips as $arsipId => $boksId) {
                $index++;
                if (in_array($arsipId, $myArsipIds)) {
                    $positions[] = $index;
                }
            }

            if (empty($positions)) {
                $boks->preloaded_range_berkas = null;
            } else {
                sort($positions);
                $min = $positions[0];
                $max = end($positions);
                $boks->preloaded_range_berkas = $min === $max ? "No. {$min}" : "No. {$min}-{$max}";
            }
        }
    }

    public function getRangeBerkasAttribute()
    {
        // Use preloaded data if available
        if (property_exists($this, 'preloaded_range_berkas') && $this->preloaded_range_berkas !== null) {
            return $this->preloaded_range_berkas;
        }
        // Check if it was explicitly set (even null)
        if (array_key_exists('preloaded_range_berkas', $this->attributes ?? [])) {
            return $this->preloaded_range_berkas ?? null;
        }

        // Fallback: compute on-the-fly (single boks, e.g. detail page)
        if (!$this->unit_id || !$this->tahun) return null;

        $allArsips = Arsip::where('unit_id', $this->unit_id)
            ->where('kurun_waktu', $this->tahun)
            ->orderBy('id', 'asc')
            ->pluck('boks_id', 'id');

        $myArsipIds = $this->arsips->pluck('id')->toArray();
        $positions = [];
        $index = 0;
        foreach ($allArsips as $arsipId => $boksId) {
            $index++;
            if (in_array($arsipId, $myArsipIds)) {
                $positions[] = $index;
            }
        }

        if (empty($positions)) return null;

        sort($positions);
        $min = $positions[0];
        $max = end($positions);

        return $min === $max ? "No. {$min}" : "No. {$min}-{$max}";
    }
}

