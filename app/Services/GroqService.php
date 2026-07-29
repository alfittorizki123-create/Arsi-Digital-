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
     * Analisa full header text + sample data Excel untuk mendeteksi unit UPT.
     */
    public function matchUnitWithAI(string $sheetName, string $fullHeaderText, array $sampleRows, array $unitList): ?array
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

            $sampleText = '';
            foreach ($sampleRows as $i => $row) {
                $sampleText .= ($i + 1) . '. ' . implode(' | ', array_map(fn($v) => trim((string)($v ?? ''))), array_slice($row, 0, 10)) . "\n";
            }

            $prompt = <<<PROMPT
Kamu adalah asisten pengenal unit organisasi Bapenda Provinsi Riau. Tugasmu membaca teks header dokumen Excel dan menentukan dari UPT/Unit mana dokumen ini berasal.

DAFTAR UNIT TERDAFTAR:
{$unitsString}

NAMA SHEET/TAB EXCEL: "{$sheetName}"

TEKS HEADER (10 BARIS PERTAMA DOKUMEN):
{$fullHeaderText}

SAMPLE DATA BARIS (ISI TABEL):
{$sampleText}

INSTRUKSI:
1. Baca SEMUA teks header di atas — cari nama unit/UPT/UP/Samsat di dalamnya.
2. Unit mungkin muncul dalam berbagai format: "UPT PEKANBARU KOTA", "UNIT PELAKSANA TEKNIS PANAM", "UP PELALAWAN", dll.
3. Cocokkan unit yang kamu temukan dengan daftar unit terdaftar di atas.
4. HANYA kembalikan matched_unit_id jika benar-benar yakin cocok. Jika tidak yakin, kembalikan null.
5. Jangan mencocokkan berdasarkan spekulasi — harus ada bukti dari teks header.

Jawab HANYA dalam format JSON valid:
{"matched_unit_id": <ID_NUMERIC_ATAU_NULL>, "confidence": "high|medium|low|none", "reason": "<alasan_singkat>"}
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
