<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPajak extends Model
{
    protected $table = 'jenis_pajaks';

    protected $fillable = ['nama_jenis_pajak', 'kode'];

    public function arsips()
    {
        return $this->belongsToMany(Arsip::class, 'arsip_jenis_pajak', 'jenis_pajak_id', 'arsip_id')->withTimestamps();
    }
}