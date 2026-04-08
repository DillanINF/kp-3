<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        return view('masters.customers', [
            'customers' => $customers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:customers,name'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $customer = new Customer();
        $customer->name = $validated['name'];
        $customer->email = $validated['email'] ?? null;
        $customer->phone = $validated['phone'] ?? null;
        $customer->is_active = true;
        if (Schema::hasColumn('customers', 'address')) {
            $customer->address = $validated['address'] ?? null;
        }
        $customer->save();

        return redirect()->route('masters.customers');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:customers,name,' . $customer->id],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email,' . $customer->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $customer->name = $validated['name'];
        $customer->email = $validated['email'] ?? null;
        $customer->phone = $validated['phone'] ?? null;
        $customer->is_active = true;
        if (Schema::hasColumn('customers', 'address')) {
            $customer->address = $validated['address'] ?? null;
        }
        $customer->save();

        return redirect()->route('masters.customers');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('masters.customers');
    }
}
