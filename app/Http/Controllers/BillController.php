<?php

namespace App\Http\Controllers;

use App\Actions\UploadBillAction;
use App\Http\Requests\UploadBillRequest;
use App\Models\Bill;
use App\Models\BillItem;
use App\Services\CurrencyConverter;
use App\Services\BillValidator;

/**
 * Bill Controller
 * 
 * Handles bill upload, display, and validation.
 */
class BillController extends Controller
{
    public function __construct(
        private CurrencyConverter $currencyConverter,
        private BillValidator $billValidator
    ) {}

    /**
     * Store uploaded bill file
     * 
     * Processes the uploaded file, performs OCR, and redirects to bill view.
     * 
     * @param UploadBillRequest $request
     * @param UploadBillAction $action
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(
        UploadBillRequest $request,
        UploadBillAction $action
    ) {
        $bill = $action->execute(
            $request->file('bill_file'),
            session()->getId()
        );

        return redirect()->route('bill.show', $bill);
    }

    /**
     * Show bill details
     * 
     * Displays the bill with OCR results, validates if it's a hospital bill,
     * and groups items by status for display.
     * 
     * @param Bill $bill
     * @return \Illuminate\View\View
     */
    public function show(Bill $bill)
    {
        // Check if OCR completed but no text extracted
        $hasNoText = $bill->isOcrCompleted() && !$bill->hasRawText();
        
        // Validate if raw text is a valid hospital bill (only if OCR completed)
        $isValidBill = true;
        if ($bill->hasRawText()) {
            $isValidBill = $this->billValidator->isValidBill($bill->raw_text);
        } elseif ($hasNoText) {
            $isValidBill = false; // No text means invalid
        }
        
        // Detect currency from raw text
        $detectedCurrency = $this->currencyConverter->detectCurrency($bill->raw_text ?? '');
        
        // Group items by status: danger first, then review, then safe
        $itemsByStatus = $bill->items->groupBy('status');
        
        $groupedResults = [];
        $statusOrder = ['danger', 'review', 'safe'];
        
        foreach ($statusOrder as $status) {
            if (isset($itemsByStatus[$status])) {
                $groupedResults[$status] = $itemsByStatus[$status]->map(function (BillItem $item) use ($detectedCurrency, $bill) {
                    // Format price with currency info if original was USD
                    $priceFormatted = 'Rp ' . number_format($item->price, 0, ',', '.');
                    
                    // If original currency was USD, show both
                    if ($detectedCurrency === 'usd' && $item->price > 0) {
                        // Calculate original USD amount (reverse conversion)
                        $originalUsd = $item->price / 16000; // Assuming 16000 rate
                        $priceFormatted .= ' ($' . number_format($originalUsd, 2, '.', ',') . ')';
                    }
                    
                    return [
                        'id' => 'item-' . $item->id,
                        'itemName' => $item->item_name,
                        'category' => $item->category,
                        'price' => $priceFormatted,
                        'status' => $item->status,
                        'label' => $item->label,
                        'description' => $item->description,
                    ];
                })->values();
            }
        }

        return view('scan', [
            'bill' => $bill,
            'groupedResults' => $groupedResults,
            'isValidBill' => $isValidBill,
            'hasNoText' => $hasNoText,
        ]);
    }
}
