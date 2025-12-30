<?php

namespace App\Actions;

use App\Models\Bill;
use App\Services\Ocr\OcrServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadBillAction
{
    public function __construct(
        protected OcrServiceInterface $ocrService
    ) {}

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
