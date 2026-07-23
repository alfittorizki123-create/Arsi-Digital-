<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arsip extends Model
{
    protected $table = 'arsips';

    protected $fillable = [
        'tipe_arsip',
        'kode_klasifikasi',
        'nomor_arsip_berkas',
        'uraian_informasi_arsip',
        'kurun_waktu',
        'jumlah',
        'satuan',
        'tingkat_perkembangan',
        'nomor_boks',
        'kondisi',
        'klasifikasi_keamanan',
        'status',
        'unit_id',
        'jenis_pajak_id',
        'path_file',
        'tipe_file',
    ];

    protected $casts = [
        'kurun_waktu' => 'integer',
        'jumlah' => 'integer',
    ];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->path_file ? asset('storage/arsip/' . $this->path_file) : null;
    }

    public function isPdf(): bool
    {
        return $this->tipe_file === 'pdf'
            || ($this->path_file && str_ends_with(strtolower($this->path_file), '.pdf'));
    }

    public function isImage(): bool
    {
        return $this->tipe_file === 'image'
            || ($this->path_file && preg_match('/\.(jpe?g|png)$/i', $this->path_file));
    }
}