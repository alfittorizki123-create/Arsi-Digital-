<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arsip extends Model
{
    protected $table = 'arsips';

    protected $fillable = [
        'nomor_arsip',
        'jenis_pajak_id',
        'nama_wajib_pajak',
        'tahun_arsip',
        'nomor_rak',
        'status',
        'unit_id',
        'path_file',
        'tipe_file',
    ];

    protected $casts = [
        'tahun_arsip' => 'integer',
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