<?php

namespace App\Actions;

use App\Models\Bill;
use App\Services\Ocr\OcrServiceInterface;
use Illuminate\Http\UploadedFile;

/**
 * Upload Bill Action
 * 
 * Handles file upload, storage, and OCR processing.
 */
class UploadBillAction
{
    public function __construct(
        protected OcrServiceInterface $ocrService
    ) {}

    /**
     * Execute bill upload
     * 
     * Stores the file, creates a bill record, and performs OCR extraction.
     * 
     * @param UploadedFile $file
     * @param string $sessionId
     * @return Bill
     * @throws \Throwable
     */
    public function execute(UploadedFile $file, string $sessionId): Bill
    {
        $path = $file->store('bills', 'public');

        $bill = Bill::create([
            'file_path' => $path,
            'status' => 'pending',
            'ocr_engine' => 'paddle',
            'session_id' => $sessionId,
        ]);

        try {
            $rawText = $this->ocrService->extractText($file);
            $bill->markAsOcrCompleted($rawText);
        } catch (\Throwable $e) {
            $bill->markAsFailed();
            throw $e;
        }

        return $bill;
    }
}
