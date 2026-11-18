<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // Pas de tableau de bord : l'accueil du panneau est la liste des documents.
    public function mount(): void
    {
        $this->redirect(DocumentResource::getUrl('index'));
    }

    // Aucune entrée « Tableau de bord » dans le menu.
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
