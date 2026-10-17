<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isActiveAdmin($user);
    }

    public function view(User $user, User $model): bool
    {
        return $this->isActiveAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isActiveAdmin($user);
    }

    public function update(User $user, User $model): bool
    {
        return $this->isActiveAdmin($user);
    }

    // Un administrateur ne peut pas activer/désactiver son propre compte.
    public function toggleActive(User $user, User $model): bool
    {
        return $this->isActiveAdmin($user) && ! $user->is($model);
    }

    // Suppression : un compte à la fois, jamais le sien. Comme l'auteur est lui-même
    // un administrateur actif, il en reste toujours au moins un après la suppression.
    public function delete(User $user, User $model): bool
    {
        return $this->isActiveAdmin($user) && ! $user->is($model);
    }

    // Pas de suppression en lot.
    public function deleteAny(User $user): bool
    {
        return false;
    }

    private function isActiveAdmin(User $user): bool
    {
        return $user->is_active && $user->isAdmin();
    }
}