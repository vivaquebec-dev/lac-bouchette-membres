<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request, string $token, string $broker): View
    {
        $invitation = $broker === 'invitations';

        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
            'invitation' => $invitation,
            'action' => route($invitation ? 'invitation.store' : 'password.update'),
        ]);
    }

    public function store(Request $request, string $broker): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::broker($broker)->reset(
            [
                'email' => Str::lower(trim($request->input('email'))),
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $request->input('token'),
                'is_active' => true,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                // Ferme toutes les sessions ouvertes de ce compte.
                DB::table('sessions')->where('user_id', $user->getKey())->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return redirect()->route('login')
            ->with('status', 'Votre mot de passe a été enregistré. Vous pouvez maintenant vous connecter.');
    }
}