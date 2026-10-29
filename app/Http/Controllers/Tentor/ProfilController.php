<?php

namespace App\Http\Controllers\Tentor;

use App\Http\Controllers\Controller;
use App\Models\StaffProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $staffProfile = StaffProfile::where('user_id', $user->id)->first();

        return view('tentor.profil', [
            'staffProfile' => $staffProfile,
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $staffProfile = StaffProfile::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'full_name_with_title' => 'required|string|max:100',
            'job_title' => 'nullable|string|max:100',
            'education' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $staffProfile->update([
            'full_name_with_title' => $validated['full_name_with_title'],
            'job_title' => $validated['job_title'],
            'education' => $validated['education'],
            'address' => $validated['address'],
        ]);

        $user->update([
            'phone' => $validated['phone'],
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
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
