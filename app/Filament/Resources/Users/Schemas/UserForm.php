<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nom complet')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Adresse courriel')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->dehydrateStateUsing(fn (string $state): string => Str::lower(trim($state))),

            // Le rôle n'est choisi qu'à la création.
            Select::make('role')
                ->label('Rôle')
                ->options(
                    collect(UserRole::cases())
                        ->mapWithKeys(fn (UserRole $role) => [$role->value => $role->label()])
                        ->all()
                )
                ->default(UserRole::Member->value)
                ->required()
                ->visibleOn('create'),

            // Un administrateur ne peut pas modifier le statut de son propre compte.
            Toggle::make('is_active')
                ->label('Compte actif')
                ->default(true)
                ->disabled(fn (?User $record): bool => $record?->is(auth()->user()) ?? false),
        ]);
    }
}