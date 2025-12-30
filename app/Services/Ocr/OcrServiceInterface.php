<?php

namespace App\Services\Ocr;

use Illuminate\Http\UploadedFile;

interface OcrServiceInterface
{
    /**
     * Perform OCR on uploaded file and return raw text
     */
    public function extractText(UploadedFile $file): string;
}
