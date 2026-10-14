<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Le rôle n'est pas assignable en masse : on le fixe explicitement.
        $role = $data['role'];
        unset($data['role']);

        $user = new User($data);
        $user->role = $role;
        // Mot de passe aléatoire inutilisable : le compte définira le sien
        // via le lien d'invitation (étape 6).
        $user->password = Str::random(64);
        $user->save();

        return $user;
    }
}
