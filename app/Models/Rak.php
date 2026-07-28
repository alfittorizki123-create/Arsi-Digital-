<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    protected $table = 'raks';

    protected $fillable = [
        'nomor_rak',
        'keterangan',
    ];

    public function boks()
    {
        return $this->hasMany(Boks::class, 'rak_id');
    }
}
