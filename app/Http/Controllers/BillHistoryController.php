<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;

class BillHistoryController extends Controller
{
    public function index()
    {
        $bills = Bill::latest()->paginate(10);

        return view('history.index', compact('bills'));
    }
}
