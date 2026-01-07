<?php

namespace App\Http\Controllers;

use App\Models\ItemOut;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $lossTotal = $lossDamaged + $lossExpired;

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
            'lossTotal' => $lossTotal,
            'net' => $net,
        ]);
    }

    public function pdf(Request $request)
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

        $lossTotal = $lossDamaged + $lossExpired;

        $net = $salesProfit - $lossTotal;

        $printedAt = now();

        $pdf = Pdf::loadView('reports.pdf', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'printedAt' => $printedAt,
            'printedBy' => $request->user(),
            'rows' => $rows,
            'salesRevenue' => $salesRevenue,
            'salesCogs' => $salesCogs,
            'salesProfit' => $salesProfit,
            'lossDamagedExpired' => $lossDamaged + $lossExpired,
            'lossDamaged' => $lossDamaged,
            'lossExpired' => $lossExpired,
            'lossTotal' => $lossTotal,
            'net' => $net,
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-' . $fromDate->toDateString() . '-' . $toDate->toDateString() . '.pdf';

        return $pdf->download($filename);
    }
}
