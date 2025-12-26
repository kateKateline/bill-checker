<?php

namespace App\Http\Controllers;

use App\Actions\AnalyzeBillAction;
use App\Models\Bill;

class AnalysisController extends Controller
{
    public function analyze(Bill $bill, AnalyzeBillAction $action)
    {
        $action->execute($bill);

        return redirect()->route('bill.show', $bill);
    }
}
