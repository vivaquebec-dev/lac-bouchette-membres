<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Notifications\PasswordLinkNotification;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Le rôle n'est volontairement PAS assignable en masse :
     * il ne peut être défini que par du code explicite (commande de création
     * des administrateurs), jamais par un formulaire.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'role' => 'member',
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->isAdmin();
    }

    protected static function booted(): void
    {
        static::updating(function (User $user): void {
            if ($user->isDirty('role')) {
                throw new \DomainException("Le rôle d'un compte ne peut pas être modifié après sa création.");
            }

            if (
                $user->isAdmin()
                && $user->isDirty('is_active')
                && ! $user->is_active
                && ! $user->hasOtherActiveAdmin()
            ) {
                throw new \DomainException('Impossible de désactiver le dernier administrateur actif.');
            }
        });

        static::deleting(function (User $user): void {
            // Filet de sécurité : seul le dernier administrateur ACTIF est protégé.
            if ($user->isAdmin() && $user->is_active && ! $user->hasOtherActiveAdmin()) {
                throw new \DomainException('Impossible de supprimer le dernier administrateur actif.');
            }
        });

        static::deleted(function (User $user): void {
            // Déconnecte le compte supprimé et efface ses jetons en attente.
            DB::table('sessions')->where('user_id', $user->getKey())->delete();
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        });
    }

    protected function hasOtherActiveAdmin(): bool
    {
        return static::query()
            ->where('role', UserRole::Admin->value)
            ->where('is_active', true)
            ->whereKeyNot($this->getKey())
            ->exists();
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PasswordLinkNotification($token));
    }

    public function sendInvitation(): void
    {
        $token = Password::broker('invitations')->createToken($this);

        $this->notify(new PasswordLinkNotification($token, isInvitation: true));
    }
}