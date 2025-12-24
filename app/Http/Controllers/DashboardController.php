<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard', [
            'invoiceThisMonth' => 0,
            'customersCount' => Customer::query()->count(),
            'suppliersCount' => 0,
            'itemsCount' => 0,
            'poPendingCount' => 0,
            'goodsOutLastMonth' => 0,
            'goodsInLastMonth' => 0,
        ]);
    }
}
