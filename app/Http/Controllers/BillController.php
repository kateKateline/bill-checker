<?php

namespace App\Http\Controllers;

use App\Actions\UploadBillAction;
use App\Http\Requests\UploadBillRequest;
use App\Models\Bill;

class BillController extends Controller
{
    public function store(
        UploadBillRequest $request,
        UploadBillAction $action
    ) {
        $bill = $action->execute($request);

        return redirect()->route('bill.show', $bill);
    }

public function show(Bill $bill)
{
    $results = $bill->items->map(function ($item) {
        return [
            'id' => 'item-' . $item->id,
            'itemName' => $item->item_name,
            'category' => $item->category,
            'price' => number_format($item->price, 0, ',', '.'),
            'status' => $item->status,
            'label' => $item->label,
            'description' => $item->description,
        ];
    });

    return view('scan', compact('bill', 'results'));
}

}
