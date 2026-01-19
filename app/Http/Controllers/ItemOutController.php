<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemOut;
use App\Models\Invoice;
use App\Models\SupplierItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemOutController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()->orderBy('name')->get();
        $items = Item::query()->where('item_type', 'regular')->orderBy('name')->get();
        $year = (int) ($request->query('year') ?? now()->year);
        $month = (int) ($request->query('month') ?? now()->month);
        if ($year < 2000) $year = (int) now()->year;
        if ($month < 1 || $month > 12) $month = (int) now()->month;

        $periodStart = now()->setDate($year, $month, 1)->startOfDay();
        $periodEnd = now()->setDate($year, $month, 1)->endOfMonth()->endOfDay();

        $history = ItemOut::query()
            ->with(['customer', 'item'])
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $salesInvoices = Invoice::query()
            ->with(['customer', 'details.item'])
            ->where('status', 'posted')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $years = range((int) now()->year, (int) now()->year - 5);

        return view('masters.items_out', [
            'customers' => $customers,
            'items' => $items,
            'salesInvoices' => $salesInvoices,
            'history' => $history,
            'selectedYear' => $year,
            'selectedMonth' => $month,
            'years' => $years,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:damaged,expired,other'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($validated) {
            $item = Item::query()->lockForUpdate()->findOrFail($validated['item_id']);
            $qty = (int) $validated['qty'];
            if ($item->stock < $qty) {
                throw ValidationException::withMessages([
                    'qty' => 'Stok tidak mencukupi.',
                ]);
            }

            $buyPrice = (int) (SupplierItem::query()
                ->where('item_id', $item->id)
                ->orderBy('buy_price')
                ->value('buy_price') ?? 0);

            $sellPrice = 0;

            ItemOut::query()->create([
                'type' => $validated['type'],
                'customer_id' => null,
                'item_id' => $item->id,
                'qty' => $qty,
                'date' => $validated['date'],
                'buy_price' => $buyPrice,
                'sell_price' => $sellPrice,
            ]);

            $item->decrement('stock', $qty);
        });

        return redirect()->route('masters.items_out');
    }

    public function update(Request $request, ItemOut $itemOut)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
        ]);

        if (($itemOut->type ?? '') === 'sale' && empty($validated['customer_id'])) {
            return back()->withErrors(['customer_id' => 'Customer wajib dipilih untuk tipe penjualan.'])->withInput();
        }

        DB::transaction(function () use ($itemOut, $validated) {
            $oldItemId = (int) $itemOut->item_id;
            $oldQty = (int) $itemOut->qty;

            Item::query()->whereKey($oldItemId)->increment('stock', $oldQty);

            $newItem = Item::query()->lockForUpdate()->findOrFail($validated['item_id']);
            $newQty = (int) $validated['qty'];
            if ($newItem->stock < $newQty) {
                throw ValidationException::withMessages([
                    'qty' => 'Stok tidak mencukupi.',
                ]);
            }

            $itemOut->customer_id = ($itemOut->type ?? '') === 'sale' ? ($validated['customer_id'] ?? null) : null;
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
