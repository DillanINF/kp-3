<?php

namespace App\Http\Controllers;

use App\Models\Pengirim;
use Illuminate\Http\Request;

class PengirimController extends Controller
{
    public function index()
    {
        $pengirims = Pengirim::query()->orderBy('name')->get();
        return view('masters.pengirim', compact('pengirims'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Pengirim::create($validated);

        return back()->with('success', 'Data pengirim berhasil ditambahkan.');
    }

    public function update(Request $request, Pengirim $pengirim)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
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
