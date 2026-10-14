<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'string', 'email']]);

        // Seuls les comptes actifs reçoivent un lien, mais la réponse est
        // identique dans tous les cas pour ne pas révéler quels courriels existent.
        Password::sendResetLink([
            'email' => Str::lower(trim($request->input('email'))),
            'is_active' => true,
        ]);

        return back()->with('status', 'Si un compte actif correspond à cette adresse, un courriel contenant un lien de réinitialisation vient d\'être envoyé.');
    }
}