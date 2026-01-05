<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::query()->orderBy('name')->get();

        return view('masters.customers', [
            'customers' => $customers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
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
