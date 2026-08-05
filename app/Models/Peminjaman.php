<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamen';

    protected $fillable = [
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

    public function arsips()
    {
        return $this->belongsToMany(Arsip::class, 'peminjaman_arsip', 'peminjaman_id', 'arsip_id')->withTimestamps();
    }

    public function getIsTerlambatAttribute(): bool
    {
        return $this->status === 'dipinjam' 
            && $this->tanggal_kembali_rencana 
            && $this->tanggal_kembali_rencana->startOfDay() < now()->startOfDay();
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_terlambat) {
            return 'bg-error text-white font-bold shadow-sm animate-pulse';
        }

        return match ($this->status) {
            'dipinjam' => 'bg-warning-container text-on-warning-container font-bold',
            'dikembalikan' => 'bg-primary/20 text-primary font-bold',
            'terlambat' => 'bg-error text-white font-bold shadow-sm',
            default => 'bg-surface-container-highest text-on-surface-variant font-bold',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_terlambat) {
            return 'TERLAMBAT';
        }

        return match ($this->status) {
            'dipinjam' => 'DIPINJAM',
            'dikembalikan' => 'DIKEMBALIKAN',
            'terlambat' => 'TERLAMBAT',
            default => strtoupper($this->status),
        };
    }
}
