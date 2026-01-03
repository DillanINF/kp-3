<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $regularItems = Item::query()->where('item_type', 'regular')->orderBy('name')->get();

        return view('masters.items', [
            'regularItems' => $regularItems,
        ]);
    }

    public function supplierIndex(Request $request)
    {
        $suppliers = Supplier::query()->orderBy('name')->get();

        $selectedSupplierId = $request->query('supplier_id');

        $supplierItemsQuery = Item::query()
            ->with('supplier')
            ->where('item_type', 'supplier')
            ->orderBy('name');

        if ($selectedSupplierId) {
            $supplierItemsQuery->where('supplier_id', $selectedSupplierId);
        }

        $supplierItems = $supplierItemsQuery->get();

        return view('masters.items_supplier', [
            'suppliers' => $suppliers,
            'supplierItems' => $supplierItems,
            'selectedSupplierId' => $selectedSupplierId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_type' => ['required', 'string', 'in:supplier,regular'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'price' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validated['item_type'] === 'supplier' && empty($validated['supplier_id'])) {
            return back()->withErrors(['supplier_id' => 'Supplier wajib dipilih untuk barang supplier.'])->withInput();
        }

        Item::query()->create([
            'item_type' => $validated['item_type'],
            'supplier_id' => $validated['item_type'] === 'supplier' ? $validated['supplier_id'] : null,
            'name' => $validated['name'],
            'unit' => $validated['unit'],
            'price' => $validated['price'] ?? 0,
            'stock' => 0,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        $redirectRoute = trim((string) $request->input('redirect_route', ''));
        $openSupplierId = $request->input('open_supplier_id');
        $allowedRedirectRoutes = [
            'masters.suppliers',
            'masters.items_supplier',
            'masters.items',
        ];
        if ($redirectRoute !== '' && in_array($redirectRoute, $allowedRedirectRoutes, true)) {
            if ($redirectRoute === 'masters.suppliers' && $openSupplierId) {
                return redirect()->route($redirectRoute, ['open_supplier_id' => $openSupplierId]);
            }

            return redirect()->route($redirectRoute);
        }

        return $validated['item_type'] === 'supplier'
            ? redirect()->route('masters.items_supplier', ['supplier_id' => $validated['supplier_id']])
            : redirect()->route('masters.items');
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'item_type' => ['required', 'string', 'in:supplier,regular'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'price' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validated['item_type'] === 'supplier' && empty($validated['supplier_id'])) {
            return back()->withErrors(['supplier_id' => 'Supplier wajib dipilih untuk barang supplier.'])->withInput();
        }

        $item->item_type = $validated['item_type'];
        $item->supplier_id = $validated['item_type'] === 'supplier' ? $validated['supplier_id'] : null;
        $item->name = $validated['name'];
        $item->unit = $validated['unit'];
        $item->price = $validated['price'] ?? 0;
        $item->is_active = $request->has('is_active') ? $request->boolean('is_active') : true;
        $item->save();

        $redirectRoute = trim((string) $request->input('redirect_route', ''));
        $openSupplierId = $request->input('open_supplier_id');
        $allowedRedirectRoutes = [
            'masters.suppliers',
            'masters.items_supplier',
            'masters.items',
        ];
        if ($redirectRoute !== '' && in_array($redirectRoute, $allowedRedirectRoutes, true)) {
            if ($redirectRoute === 'masters.suppliers' && $openSupplierId) {
                return redirect()->route($redirectRoute, ['open_supplier_id' => $openSupplierId]);
            }

            return redirect()->route($redirectRoute);
        }

        return $validated['item_type'] === 'supplier'
            ? redirect()->route('masters.items_supplier', ['supplier_id' => $validated['supplier_id']])
            : redirect()->route('masters.items');
    }

    public function destroy(Item $item)
    {
        $itemType = $item->item_type;
        $supplierId = $item->supplier_id;
        $item->delete();

        $redirectRoute = trim((string) request()->input('redirect_route', ''));
        $openSupplierId = request()->input('open_supplier_id');
        $allowedRedirectRoutes = [
            'masters.suppliers',
            'masters.items_supplier',
            'masters.items',
        ];
        if ($redirectRoute !== '' && in_array($redirectRoute, $allowedRedirectRoutes, true)) {
            if ($redirectRoute === 'masters.suppliers' && $openSupplierId) {
                return redirect()->route($redirectRoute, ['open_supplier_id' => $openSupplierId]);
            }

            return redirect()->route($redirectRoute);
        }

        return $itemType === 'supplier'
            ? redirect()->route('masters.items_supplier', ['supplier_id' => $supplierId])
            : redirect()->route('masters.items');
    }
}
