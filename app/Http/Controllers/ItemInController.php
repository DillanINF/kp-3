<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemIn;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemInController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::query()->orderBy('name')->get();
        $items = Item::query()->where('item_type', 'supplier')->orderBy('name')->get();
        $history = ItemIn::query()->with(['supplier', 'item'])->orderByDesc('date')->orderByDesc('id')->get();

        return view('masters.items_in', [
            'suppliers' => $suppliers,
            'items' => $items,
            'history' => $history,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
        ]);

        $item = Item::query()->findOrFail($validated['item_id']);
        if ($item->item_type !== 'supplier' || (int) $item->supplier_id !== (int) $validated['supplier_id']) {
            return back()->withErrors(['item_id' => 'Barang yang dipilih tidak sesuai dengan supplier.'])->withInput();
        }

        DB::transaction(function () use ($validated) {
            ItemIn::query()->create($validated);

            Item::query()->whereKey($validated['item_id'])->increment('stock', (int) $validated['qty']);
        });

        return redirect()->route('masters.items_in');
    }

    public function update(Request $request, ItemIn $itemIn)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
        ]);

        $item = Item::query()->findOrFail($validated['item_id']);
        if ($item->item_type !== 'supplier' || (int) $item->supplier_id !== (int) $validated['supplier_id']) {
            return back()->withErrors(['item_id' => 'Barang yang dipilih tidak sesuai dengan supplier.'])->withInput();
        }

        DB::transaction(function () use ($itemIn, $validated) {
            $oldItemId = (int) $itemIn->item_id;
            $oldQty = (int) $itemIn->qty;

            $itemIn->supplier_id = $validated['supplier_id'];
            $itemIn->item_id = $validated['item_id'];
            $itemIn->qty = $validated['qty'];
            $itemIn->date = $validated['date'];
            $itemIn->save();

            Item::query()->whereKey($oldItemId)->decrement('stock', $oldQty);
            Item::query()->whereKey($validated['item_id'])->increment('stock', (int) $validated['qty']);
        });

        return redirect()->route('masters.items_in');
    }

    public function destroy(ItemIn $itemIn)
    {
        DB::transaction(function () use ($itemIn) {
            Item::query()->whereKey($itemIn->item_id)->decrement('stock', (int) $itemIn->qty);
            $itemIn->delete();
        });

        return redirect()->route('masters.items_in');
    }
}
