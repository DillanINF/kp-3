<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class InvoiceController extends Controller
{
    public function index()
    {
        $customers = Customer::query()->orderBy('name')->get();

        return view('invoices.index', [
            'customers' => $customers,
        ]);
    }

    public function inputPo()
    {
        return view('invoices.input_po');
    }
}
