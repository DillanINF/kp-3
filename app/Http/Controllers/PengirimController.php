<?php

namespace App\Http\Controllers;

use App\Models\Pengirim;
use Illuminate\Http\Request;

class PengirimController extends Controller
{
    public function index(Request $request)
    {
        $pengirims = Pengirim::query()
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();
        return view('masters.pengirim', compact('pengirims'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:pengirims,name',
            'phone' => 'nullable|string|max:50',
            'vehicle_type' => 'nullable|string|max:100',
            'license_plate' => 'nullable|string|max:20',
        ]);

        Pengirim::create($validated);

        return back()->with('success', 'Data pengirim berhasil ditambahkan.');
    }

    public function update(Request $request, Pengirim $pengirim)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:pengirims,name,' . $pengirim->id,
            'phone' => 'nullable|string|max:50',
            'vehicle_type' => 'nullable|string|max:100',
            'license_plate' => 'nullable|string|max:20',
        ]);

        $pengirim->update($validated);

        return back()->with('success', 'Data pengirim berhasil diperbarui.');
    }

    public function destroy(Pengirim $pengirim)
    {
        $pengirim->delete();

        return back()->with('success', 'Data pengirim berhasil dihapus.');
    }
}
