<?php

namespace App\Console\Commands;

use App\Models\ArsipFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanTempFiles extends Command
{
    protected $signature = 'arsip:clean-temp-files {--hours=24 : Hapus file temp yang lebih tua dari berapa jam}';
    protected $description = 'Menghapus file upload temporary yang tidak terikat ke arsip mana pun';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = now()->subHours($hours);

        $tempFiles = ArsipFile::whereNull('arsip_id')
            ->where('created_at', '<', $cutoff)
            ->get();

        $count = 0;
        foreach ($tempFiles as $file) {
            if ($file->path_file) {
                Storage::disk('public')->delete($file->path_file);
            }
            $file->delete();
            $count++;
        }

        $this->info("Berhasil menghapus {$count} file temporary sampah (di atas {$hours} jam).");

        return Command::SUCCESS;
    }
}
