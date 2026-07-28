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
        return static::firstOrCreate(
            ['nomor_boks' => $nomorBoksInt, 'tahun' => (int) $tahun],
            ['unit_id' => $unitId]
        );
    }

    public function getRangeBerkasAttribute()
    {
        if (!$this->unit_id || !$this->tahun) return null;

        static $cache = [];

        $cacheKey = "{$this->unit_id}_{$this->tahun}";

        if (!isset($cache[$cacheKey])) {
            $cache[$cacheKey] = Arsip::where('unit_id', $this->unit_id)
                ->where('kurun_waktu', $this->tahun)
                ->orderBy('id', 'asc')
                ->pluck('boks_id', 'id');
        }

        $allArsips = $cache[$cacheKey];
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
