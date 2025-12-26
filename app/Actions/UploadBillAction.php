<?php
namespace App\Actions;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadBillAction
{
    public function execute(Request $request): Bill
    {
        $file = $request->file('bill_file');

        $path = $file->store('bills', 'public');

        return Bill::create([
            'file_path' => $path,
            'status' => 'pending',
            'session_id' => $request->session()->getId(),
        ]);
    }
}
