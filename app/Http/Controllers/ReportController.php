<?php

namespace App\Http\Controllers;

use App\Models\ItemOut;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
        $toDate = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        $rows = ItemOut::query()
            ->with(['customer', 'item'])
            ->whereDate('date', '>=', $fromDate->toDateString())
            ->whereDate('date', '<=', $toDate->toDateString())
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $salesRevenue = $rows
            ->where('type', 'sale')
            ->sum(fn ($r) => (int) $r->sell_price * (int) $r->qty);

        $salesCogs = $rows
            ->where('type', 'sale')
            ->sum(fn ($r) => (int) $r->buy_price * (int) $r->qty);

        $salesProfit = $salesRevenue - $salesCogs;

        $lossDamagedExpired = $rows
            ->whereIn('type', ['damaged', 'expired'])
            ->sum(fn ($r) => (int) $r->buy_price * (int) $r->qty);

        $net = $salesProfit - $lossDamagedExpired;

        return view('reports.index', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'rows' => $rows,
            'salesRevenue' => $salesRevenue,
            'salesCogs' => $salesCogs,
            'salesProfit' => $salesProfit,
            'lossDamagedExpired' => $lossDamagedExpired,
            'net' => $net,
        ]);
    }
}
