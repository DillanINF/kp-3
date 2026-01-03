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

        $items = Item::query()->orderBy('name')->get();
        $history = ItemIn::query()->with(['supplier', 'item'])->orderByDesc('date')->orderByDesc('id')->get();

        return view('masters.items_in', [
            'suppliers' => $suppliers,
            'items' => $items,
            'itemsBySupplier' => $itemsBySupplier,
            'history' => $history,
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
