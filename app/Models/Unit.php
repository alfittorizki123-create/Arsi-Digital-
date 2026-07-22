<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units';

    protected $fillable = ['nama_unit', 'kode_unit'];

    public function arsips()
    {
        return $this->hasMany(Arsip::class);
    }
}