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

        $lossDamaged = $rows
            ->where('type', 'damaged')
            ->sum(fn ($r) => (int) $r->buy_price * (int) $r->qty);

        $lossExpired = $rows
            ->where('type', 'expired')
            ->sum(fn ($r) => (int) $r->buy_price * (int) $r->qty);

        $lossOther = $rows
            ->where('type', 'other')
            ->sum(fn ($r) => (int) $r->buy_price * (int) $r->qty);

        $lossTotal = $lossDamaged + $lossExpired + $lossOther;

        $net = $salesProfit - $lossTotal;

        return view('reports.index', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'rows' => $rows,
            'salesRevenue' => $salesRevenue,
            'salesCogs' => $salesCogs,
            'salesProfit' => $salesProfit,
            'lossDamagedExpired' => $lossDamaged + $lossExpired,
            'lossDamaged' => $lossDamaged,
            'lossExpired' => $lossExpired,
            'lossOther' => $lossOther,
            'lossTotal' => $lossTotal,
            'net' => $net,
        ]);
    }
}
