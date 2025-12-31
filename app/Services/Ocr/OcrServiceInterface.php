<?php

namespace App\Services\Ocr;

use Illuminate\Http\UploadedFile;

/**
 * OCR Service Interface
 * 
 * Defines the contract for OCR services.
 */
interface OcrServiceInterface
{
    /**
     * Perform OCR on uploaded file and return raw text
     * 
     * @param UploadedFile $file
     * @return string
     * @throws OcrException
     */
    public function extractText(UploadedFile $file): string;
}
