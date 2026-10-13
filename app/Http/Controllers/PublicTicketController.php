<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PublicTicketController extends Controller
{
    public function show($token)
    {
        $transaction = Transaction::with(['items', 'cashier'])
            ->where('po_token', $token)
            ->firstOrFail();

        $storeName = Setting::get('store_name', 'Danusan Official');

        return view('ticket.show', compact('transaction', 'storeName'));
    }
}
