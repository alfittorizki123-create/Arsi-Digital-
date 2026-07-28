<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.key', env('GROQ_API_KEY', ''));
        $this->model = config('services.groq.model', 'llama-3.3-70b-versatile');
    }

    /**
     * Cocokkan nama sheet dan teks header Excel ke unit UPT terdaftar memakai AI Groq.
     */
    public function matchUnitWithAI(string $sheetName, string $headerText, array $unitList): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $unitsFormatted = [];
            foreach ($unitList as $u) {
                $unitsFormatted[] = "ID: {$u['id']} | Nama: {$u['nama_unit']}";
            }
            $unitsString = implode("\n", $unitsFormatted);

            $prompt = <<<PROMPT
Kamu adalah asisten pengenal unit organisasi Bapenda Provinsi Riau.
Tugasmu adalah mencocokkan Nama Sheet Excel atau Teks Header Surat Excel ke salah satu Unit UPT yang terdaftar.

DAFTAR UNIT TERDAFTAR:
{$unitsString}

INPUT UNTUK DIANALISIS:
- Nama Sheet: "{$sheetName}"
- Teks Header Baris (Unit Kerja/Organisasi): "{$headerText}"

INSTRUKSI PENTING:
1. Teks Header Baris (Unit Kerja) adalah nama instansi RESMI di dalam dokumen. Jika Teks Header berbeda dengan Nama Sheet, PRIORITASKAN Teks Header resmi (Unit Kerja).
2. Jika Teks Header (Unit Kerja) menyebutkan unit kerja yang BELUM TERDAFTAR di list (misal: "KARTAMA"), kembalikan matched_unit_id = null agar sistem dapat menyarankan pembuatan unit baru.
3. Pilih SATU unit dari daftar terdaftar HANYA jika benar-benar cocok.
Jawab HANYA dalam format JSON valid tanpa penjelasan tambahan:
{"matched_unit_id": <ID_NUMERIC_OR_NULL>, "confidence": "high|medium|low|none", "reason": "<alasan_singkat>"}
PROMPT;

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.1,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                $parsed = json_decode($content, true);
                if (is_array($parsed) && isset($parsed['matched_unit_id'])) {
                    return $parsed;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Groq AI Service matching failed: ' . $e->getMessage());
        }

        return null;
    }
}
