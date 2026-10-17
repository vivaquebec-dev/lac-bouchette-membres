<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Supprimer')
                ->modalHeading('Supprimer ce compte ?')
                ->modalDescription('Cette action est définitive : le compte sera supprimé et la personne sera déconnectée immédiatement. Pour une mesure réversible, désactivez plutôt le compte.')
                ->modalSubmitActionLabel('Supprimer définitivement'),
        ];
    }
}
