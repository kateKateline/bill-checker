<?php

namespace App\Http\Controllers;

use App\Actions\AnalyzeBillAction;
use App\Models\Bill;

/**
 * Analysis Controller
 * 
 * Handles AI analysis of bills.
 */
class AnalysisController extends Controller
{
    /**
     * Analyze bill with AI
     * 
     * Processes the bill through AI analysis and redirects back to bill view.
     * 
     * @param Bill $bill
     * @param AnalyzeBillAction $action
     * @return \Illuminate\Http\RedirectResponse
     */
    public function analyze(Bill $bill, AnalyzeBillAction $action)
    {
        $action->execute($bill);

        return redirect()->route('bill.show', $bill);
    }
}
