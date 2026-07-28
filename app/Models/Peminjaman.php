<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamen';

    protected $fillable = [
        'arsip_id',
        'nama_peminjam',
        'instansi',
        'telp',
        'keperluan',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_dikembalikan',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_dikembalikan' => 'date',
    ];

    public function arsip()
    {
        return $this->belongsTo(Arsip::class, 'arsip_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'dipinjam' => 'bg-warning-container text-on-warning-container',
            'dikembalikan' => 'bg-primary-fixed text-on-primary-fixed',
            'terlambat' => 'bg-error-container text-on-error-container',
            default => 'bg-surface-container-highest text-on-surface-variant',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'dipinjam' => 'Dipinjam',
            'dikembalikan' => 'Dikembalikan',
            'terlambat' => 'Terlambat',
            default => '-',
        };
    }
}
