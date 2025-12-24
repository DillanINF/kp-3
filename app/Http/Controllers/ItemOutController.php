<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemOutController extends Controller
{
    public function index()
    {
        $customers = Customer::query()->orderBy('name')->get();
        $items = Item::query()->where('item_type', 'regular')->orderBy('name')->get();
        $history = ItemOut::query()->with(['customer', 'item'])->orderByDesc('date')->orderByDesc('id')->get();

        return view('masters.items_out', [
            'customers' => $customers,
            'items' => $items,
            'history' => $history,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($validated) {
            $item = Item::query()->lockForUpdate()->findOrFail($validated['item_id']);
            $qty = (int) $validated['qty'];
            if ($item->stock < $qty) {
                abort(422, 'Stok tidak mencukupi.');
            }

            ItemOut::query()->create($validated);
            $item->decrement('stock', $qty);
        });

        return redirect()->route('masters.items_out');
    }

    public function update(Request $request, ItemOut $itemOut)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($itemOut, $validated) {
            $oldItemId = (int) $itemOut->item_id;
            $oldQty = (int) $itemOut->qty;

            Item::query()->whereKey($oldItemId)->increment('stock', $oldQty);

            $newItem = Item::query()->lockForUpdate()->findOrFail($validated['item_id']);
            $newQty = (int) $validated['qty'];
            if ($newItem->stock < $newQty) {
                abort(422, 'Stok tidak mencukupi.');
            }

            $itemOut->customer_id = $validated['customer_id'];
            $itemOut->item_id = $validated['item_id'];
            $itemOut->qty = $validated['qty'];
            $itemOut->date = $validated['date'];
            $itemOut->save();

            $newItem->decrement('stock', $newQty);
        });

        return redirect()->route('masters.items_out');
    }

    public function destroy(ItemOut $itemOut)
    {
        DB::transaction(function () use ($itemOut) {
            Item::query()->whereKey($itemOut->item_id)->increment('stock', (int) $itemOut->qty);
            $itemOut->delete();
        });

        return redirect()->route('masters.items_out');
    }
}
