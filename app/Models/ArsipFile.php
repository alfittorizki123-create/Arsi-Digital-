<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ArsipFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'arsip_id',
        'nama_file',
        'path_file',
        'tipe_file',
        'ukuran_file',
    ];

    public function arsip(): BelongsTo
    {
        return $this->belongsTo(Arsip::class, 'arsip_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path_file);
    }

    public function getIsImageAttribute(): bool
    {
        $ext = strtolower(pathinfo($this->nama_file, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function getIsPdfAttribute(): bool
    {
        $ext = strtolower(pathinfo($this->nama_file, PATHINFO_EXTENSION));
        return $ext === 'pdf';
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->ukuran_file;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
