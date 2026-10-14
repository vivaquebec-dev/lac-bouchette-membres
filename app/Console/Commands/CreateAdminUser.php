<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Crée un compte administrateur (saisie interactive, mot de passe masqué).';

    public function handle(): int
    {
        $name = text(
            label: 'Nom complet',
            required: true,
            validate: fn (string $value) => mb_strlen($value) > 255 ? 'Le nom est trop long.' : null,
        );

        $email = Str::lower(trim(text(
            label: 'Adresse courriel',
            required: true,
            validate: function (string $value) {
                $validator = Validator::make(
                    ['email' => Str::lower(trim($value))],
                    ['email' => ['email:rfc', 'max:255', 'unique:users,email']],
                );

                return $validator->fails() ? $validator->errors()->first('email') : null;
            },
        )));

        $plainPassword = password(
            label: 'Mot de passe (12 caractères minimum, lettres et chiffres)',
            required: true,
            validate: function (string $value) {
                $validator = Validator::make(
                    ['password' => $value],
                    ['password' => [Password::min(12)->letters()->numbers()]],
                );

                return $validator->fails() ? $validator->errors()->first('password') : null;
            },
        );

        password(
            label: 'Confirmer le mot de passe',
            required: true,
            validate: fn (string $value) => $value !== $plainPassword ? 'Les mots de passe ne correspondent pas.' : null,
        );

        $user = new User();
        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword, // haché automatiquement par le cast « hashed »
            'role' => UserRole::Admin,
            'is_active' => true,
            'email_verified_at' => now(),
        ])->save();

        $this->info("Administrateur créé : {$user->email}");

        return self::SUCCESS;
    }
}