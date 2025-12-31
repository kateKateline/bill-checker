<?php

namespace App\Actions;

use App\Models\Bill;
use App\Models\BillItem;
use App\Services\Ai\BillAnalyzer;
use App\Services\Ai\AiException;
use App\Services\CurrencyConverter;
use Illuminate\Support\Facades\Log;

/**
 * Analyze Bill Action
 * 
 * Handles AI analysis of bills, creates bill items, and updates bill status.
 */
class AnalyzeBillAction
{
    public function __construct(
        private BillAnalyzer $billAnalyzer,
        private CurrencyConverter $currencyConverter
    ) {}

    /**
     * Execute bill analysis
     * 
     * Analyzes the bill using AI, creates bill items with currency conversion,
     * and updates the bill status.
     * 
     * @param Bill $bill
     * @return void
     * @throws AiException
     * @throws \Throwable
     */
    public function execute(Bill $bill): void
    {
        // Reset existing items if re-analyzing
        $bill->items()->delete();

        try {
            // Analyze using AI
            $analysis = $this->billAnalyzer->analyze($bill);

            // Create bill items from analysis with currency conversion
            $totalPrice = 0;
            foreach ($analysis['items'] as $itemData) {
                // Convert currency if needed
                $priceInfo = $this->currencyConverter->extractAndConvertPrice(
                    $bill->raw_text,
                    $itemData['price']
                );

                BillItem::create([
                    'bill_id' => $bill->id,
                    'item_name' => $itemData['item_name'],
                    'category' => $itemData['category'],
                    'price' => $priceInfo['converted_price'], // Store in IDR
                    'status' => $itemData['status'],
                    'label' => $itemData['label'],
                    'description' => $itemData['description'],
                ]);

                $totalPrice += $priceInfo['converted_price'];
            }

            // Update bill status
            $bill->markAsAnalyzed(
                $totalPrice,
                $analysis['hospital_name']
            );
        } catch (AiException $e) {
            Log::error('AI analysis failed', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage(),
            ]);

            $bill->markAsFailed();
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during bill analysis', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $bill->markAsFailed();
            throw $e;
        }
    }
}
