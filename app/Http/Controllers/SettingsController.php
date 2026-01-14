<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        return view('settings');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->fill($validated);
        $user->save();

        return redirect()
            ->route('settings', ['tab' => 'profil'])
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return redirect()
                ->route('settings', ['tab' => 'password'])
                ->withErrors(['current_password' => 'Password lama yang kamu masukkan salah.'])
                ->withInput();
        }

        $user->password = $validated['password'];
        $user->save();

        return redirect()
            ->route('settings', ['tab' => 'password'])
            ->with('success', 'Password berhasil diperbarui.');
    }

    public function storeManager(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'manager',
        ]);

        return redirect()
            ->route('settings', ['tab' => 'manager'])
            ->with('success', 'Akun manager berhasil dibuat.');
    }
}
