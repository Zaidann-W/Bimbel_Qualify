<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use Illuminate\Http\Request;
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
}
