<?php

namespace App\Services\Ocr;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use App\Services\Ocr\OcrException;

class PaddleOcrService implements OcrServiceInterface
{
    public function extractText(UploadedFile $file): string
    {
        $endpoint = rtrim(config('services.ocr.endpoint'), '/');

        // Ensure file exists and is readable
        $filePath = $file->getRealPath();
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new OcrException('File is not readable: ' . $filePath);
        }

        $fileName = $file->getClientOriginalName();
        $fileContents = file_get_contents($filePath);
        
        // Verify file was read successfully
        if ($fileContents === false) {
            throw new OcrException('Failed to read file contents');
        }

        // Ensure file is not empty
        if (empty($fileContents)) {
            throw new OcrException('File is empty');
        }

        $response = Http::withOptions([
            'verify' => config('services.ocr.verify_ssl', false),
            'timeout' => 60, // Increase timeout for OCR processing
        ])->attach(
            'file',
            $fileContents,
            $fileName
        )->post($endpoint);

        if (! $response->successful()) {
            $status = $response->status();
            $body = $response->body();
            throw new OcrException(
                "OCR service failed with status {$status}. Response: {$body}"
            );
        }

        $payload = $response->json();

        if (! ($payload['success'] ?? false)) {
            throw new OcrException(
                'OCR service returned unsuccessful response: ' .
                    ($payload['error'] ?? 'unknown error')
            );
        }

        $text = $payload['text'] ?? '';

        if (is_array($text)) {
            $text = trim(implode("\n", array_map('strval', $text)));
        }

        return is_string($text) ? $text : '';
    }
}
