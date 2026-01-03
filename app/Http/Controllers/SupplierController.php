<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\SupplierItem;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::query()
            ->with(['supplierItems' => fn ($q) => $q->with('item')->orderBy('id')])
            ->orderBy('name')
            ->get();

        $items = Item::query()->orderBy('name')->get();

        return view('masters.suppliers', [
            'suppliers' => $suppliers,
            'items' => $items,
        ]);
    }

    public function storeItem(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'buy_price' => ['nullable', 'integer', 'min:0'],
        ]);

        SupplierItem::query()->updateOrCreate(
            [
                'supplier_id' => $supplier->id,
                'item_id' => $validated['item_id'],
            ],
            [
                'buy_price' => $validated['buy_price'] ?? 0,
                'is_active' => true,
            ]
        );

        return redirect()->route('masters.suppliers', ['open_supplier_id' => $supplier->id]);
    }

    public function destroyItem(Supplier $supplier, SupplierItem $supplierItem)
    {
        if ((int) $supplierItem->supplier_id !== (int) $supplier->id) {
            abort(404);
        }

        $supplierItem->delete();

        return redirect()->route('masters.suppliers', ['open_supplier_id' => $supplier->id]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Supplier::query()->create([
            'name' => $validated['name'],
            'contact_name' => $validated['contact_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('masters.suppliers');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $supplier->name = $validated['name'];
        $supplier->contact_name = $validated['contact_name'] ?? null;
        $supplier->phone = $validated['phone'] ?? null;
        $supplier->email = $validated['email'] ?? null;
        $supplier->address = $validated['address'] ?? null;
        $supplier->is_active = $request->boolean('is_active');
        $supplier->save();

        return redirect()->route('masters.suppliers');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('masters.suppliers');
    }
}
