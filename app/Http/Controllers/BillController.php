<?php

namespace App\Http\Controllers;

use App\Actions\UploadBillAction;
use App\Http\Requests\UploadBillRequest;
use App\Models\Bill;
use App\Models\BillItem;

class BillController extends Controller
{
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

    public function show(Bill $bill)
    {
        $results = $bill->items->map(function (BillItem $item) {
            return [
                'id' => 'item-' . $item->id,
                'itemName' => $item->item_name,
                'category' => $item->category,
                'price' => $item->price,
                'status' => $item->status,
                'label' => $item->label,
                'description' => $item->description,
            ];
        });

        return view('scan', [
            'bill' => $bill,
            'results' => $results,
        ]);
    }
}
