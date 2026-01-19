<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemIn;
use App\Models\Supplier;
use App\Models\SupplierItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemInController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::query()->orderBy('name')->get();
        $itemsBySupplier = SupplierItem::query()
            ->with('item')
            ->orderBy('supplier_id')
            ->orderBy('id')
            ->get()
            ->groupBy('supplier_id')
            ->map(fn ($rows) => $rows
                ->map(fn ($si) => [
                    'id' => $si->item_id,
                    'name' => $si->item?->name,
                ])
                ->values()
                ->all())
            ->all();

        $supplierPriceMap = SupplierItem::query()
            ->select(['supplier_id', 'item_id', 'buy_price'])
            ->get()
            ->groupBy('supplier_id')
            ->map(fn ($rows) => $rows
                ->mapWithKeys(fn ($si) => [(int) $si->item_id => (int) ($si->buy_price ?? 0)])
                ->all())
            ->all();

        $items = Item::query()->orderBy('name')->get();
        $history = ItemIn::query()->with(['supplier', 'item'])->orderByDesc('date')->orderByDesc('id')->get();

        $historyGrandTotal = 0;
        foreach ($history as $row) {
            $supplierId = (int) ($row->supplier_id ?? 0);
            $itemId = (int) ($row->item_id ?? 0);
            $price = (int) ($supplierPriceMap[$supplierId][$itemId] ?? 0);
            $qty = (int) ($row->qty ?? 0);
            $historyGrandTotal += ($price * $qty);
        }

        return view('masters.items_in', [
            'suppliers' => $suppliers,
            'items' => $items,
            'itemsBySupplier' => $itemsBySupplier,
            'history' => $history,
            'supplierPriceMap' => $supplierPriceMap,
            'historyGrandTotal' => $historyGrandTotal,
        ]);
    }

    public function store(Request $request)
    {
        abort(403, 'Barang masuk dicatat otomatis saat permintaan supplier di-ACCEPT.');
    }

    public function update(Request $request, ItemIn $itemIn)
    {
        abort(403, 'Barang masuk dicatat otomatis saat permintaan supplier di-ACCEPT.');
    }

    public function destroy(ItemIn $itemIn)
    {
        abort(403, 'Barang masuk dicatat otomatis saat permintaan supplier di-ACCEPT.');
    }
}
