<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Courriel')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn (UserRole $state): string => $state->label())
                    ->color(fn (UserRole $state): string => $state === UserRole::Admin ? 'warning' : 'gray')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rôle')
                    ->options(
                        collect(UserRole::cases())
                            ->mapWithKeys(fn (UserRole $role) => [$role->value => $role->label()])
                            ->all()
                    ),

                TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('toggleActive')
                    ->label(fn (User $record): string => $record->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn (User $record): string => $record->is_active ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                    ->color(fn (User $record): string => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->authorize('toggleActive')
                    ->action(fn (User $record) => $record->update(['is_active' => ! $record->is_active])),

                Action::make('resendInvitation')
                    ->label("Renvoyer l'invitation")
                    ->icon('heroicon-o-envelope')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => $record->is_active)
                    ->authorize('update')
                    ->action(function (User $record): void {
                        try {
                            $record->sendInvitation();

                            Notification::make()->title('Invitation envoyée')->success()->send();
                        } catch (\Throwable $e) {
                            report($e);

                            Notification::make()->title("L'envoi a échoué")->danger()->send();
                        }
                    }),
            ])
            ->defaultSort('name');
    }
}