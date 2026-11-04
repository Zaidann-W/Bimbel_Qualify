<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $guardian = Guardian::where('user_id', $user->id)->first();

        $children = $guardian
            ? $guardian->students()->orderBy('full_name')->get()
            : collect();

        $hubunganUtama = $children->first(fn ($c) => $c->pivot->is_primary_contact)?->pivot->relationship
            ?? $children->first()?->pivot->relationship
            ?? '-';

        $idWali = $guardian
            ? 'WAL-'.str_pad((string) $guardian->id, 6, '0', STR_PAD_LEFT)
            : null;

        return view('wali.profil', [
            'guardian' => $guardian,
            'children' => $children,
            'user' => $user,
            'idWali' => $idWali,
            'hubunganUtama' => $hubunganUtama,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $guardian = Guardian::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'occupation' => 'nullable|string|max:100',
            'phone_wa' => 'required|string|max:20',
            'email' => 'required|email|max:190',
            'address' => 'nullable|string',
        ]);

        $guardian->update([
            'name' => $validated['name'],
            'occupation' => $validated['occupation'],
            'phone_wa' => $validated['phone_wa'],
            'email' => $validated['email'],
            'address' => $validated['address'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone_wa'],
        ]);

        return back()->with('success', 'Profil wali berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini tidak sesuai.',
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
