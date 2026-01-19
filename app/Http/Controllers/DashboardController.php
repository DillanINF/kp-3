<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\ItemIn;
use App\Models\ItemOut;
use App\Models\PoPendingItem;
use App\Models\Supplier;
use App\Models\SupplierRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = now();

        $invoicesCount = Invoice::query()->count();

        $expenseTotal = (int) (SupplierRequest::query()
            ->where('status', 'accepted')
            ->sum('total_amount') ?? 0);

        $revenueTotal = (int) (Invoice::query()
            ->where('status', 'posted')
            ->sum('grand_total') ?? 0);

        $customersCount = Customer::query()->count();
        $suppliersCount = Supplier::query()->count();
        $itemsCount = Item::query()->where('item_type', 'regular')->count();

        $goodsOutTotalQty = (int) (ItemOut::query()->sum('qty') ?? 0);
        $goodsOutSalesQty = (int) (ItemOut::query()->where('type', 'sale')->sum('qty') ?? 0);
        $goodsOutLossQty = (int) (ItemOut::query()->whereIn('type', ['damaged', 'expired', 'other'])->sum('qty') ?? 0);
        $goodsInTotalQty = (int) (ItemIn::query()->sum('qty') ?? 0);

        $profitTotal = (int) (ItemOut::query()
            ->where('type', 'sale')
            ->selectRaw('COALESCE(SUM((sell_price - buy_price) * qty), 0) as profit')
            ->value('profit') ?? 0);

        $latestInvoices = Invoice::query()
            ->with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $latestItemIns = ItemIn::query()
            ->with(['supplier', 'item'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $latestPoPending = PoPendingItem::query()
            ->with(['customer', 'item'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $activities = [];

        foreach ($latestInvoices as $inv) {
            $activities[] = [
                'at' => $inv->created_at,
                'label' => 'Invoice ' . ($inv->invoice_no ?? '-') . ' dibuat (' . ($inv->customer?->name ?? '-') . ')',
                'status' => ($inv->status ?? '') === 'posted' ? 'Selesai' : 'Draft',
                'tone' => ($inv->status ?? '') === 'posted' ? 'success' : 'neutral',
                'url' => route('invoices.index'),
            ];
        }

        foreach ($latestItemIns as $row) {
            $activities[] = [
                'at' => $row->created_at,
                'label' => 'Barang masuk: ' . ($row->item?->name ?? '-') . ' (' . ((int) $row->qty) . ') dari ' . ($row->supplier?->name ?? '-'),
                'status' => 'Tercatat',
                'tone' => 'neutral',
                'url' => route('masters.items_in'),
            ];
        }

        foreach ($latestPoPending as $row) {
            $activities[] = [
                'at' => $row->created_at,
                'label' => 'PO belum terkirim: ' . ($row->item?->name ?? '-') . ' (' . ((int) $row->qty) . ') untuk ' . ($row->customer?->name ?? '-'),
                'status' => 'Pending',
                'tone' => 'warning',
                'url' => route('invoices.po_pending'),
            ];
        }

        usort($activities, function ($a, $b) {
            $atA = $a['at'] ?? null;
            $atB = $b['at'] ?? null;
            if (!$atA && !$atB) return 0;
            if (!$atA) return 1;
            if (!$atB) return -1;
            return $atB <=> $atA;
        });
        $activities = array_slice($activities, 0, 10);

        return view('dashboard', [
            'invoicesCount' => $invoicesCount,
            'expenseTotal' => $expenseTotal,
            'revenueTotal' => $revenueTotal,
            'profitTotal' => $profitTotal,
            'customersCount' => $customersCount,
            'suppliersCount' => $suppliersCount,
            'itemsCount' => $itemsCount,
            'goodsOutTotalQty' => $goodsOutTotalQty,
            'goodsOutSalesQty' => $goodsOutSalesQty,
            'goodsOutLossQty' => $goodsOutLossQty,
            'goodsInTotalQty' => $goodsInTotalQty,
            'activities' => $activities,
        ]);
    }
}
