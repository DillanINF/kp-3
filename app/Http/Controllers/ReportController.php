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
        $year = (int) $request->query('year', now()->year);
        if ($year < 2000 || $year > 2100) {
            $year = (int) now()->year;
        }

        $monthly = [];

        for ($m = 1; $m <= 12; $m++) {
            $fromDate = Carbon::create($year, $m, 1)->startOfMonth();
            $toDate = (clone $fromDate)->endOfMonth();

            $rows = ItemOut::query()
                ->whereDate('date', '>=', $fromDate->toDateString())
                ->whereDate('date', '<=', $toDate->toDateString())
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

            $monthly[] = [
                'month' => $fromDate->format('Y-m'),
                'label' => $fromDate->translatedFormat('F Y'),
                'salesRevenue' => (int) $salesRevenue,
                'salesCogs' => (int) $salesCogs,
                'salesProfit' => (int) $salesProfit,
                'lossDamaged' => (int) $lossDamaged,
                'lossExpired' => (int) $lossExpired,
                'net' => (int) $net,
            ];
        }

        return view('reports.index', [
            'year' => $year,
            'monthly' => $monthly,
        ]);
    }

    public function pdf(Request $request)
    {
        $month = trim((string) $request->query('month', ''));
        $from = $request->query('from');
        $to = $request->query('to');

        if ($month !== '') {
            $fromDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $toDate = (clone $fromDate)->endOfMonth();
        } else {
            $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
            $toDate = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();
        }

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
            'month' => $month,
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
