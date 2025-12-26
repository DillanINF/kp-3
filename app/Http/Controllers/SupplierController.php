<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::query()
            ->with(['supplierItems' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('masters.suppliers', [
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Supplier::query()->create([
            'name' => $validated['name'],
            'contact_name' => $validated['contact_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
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
