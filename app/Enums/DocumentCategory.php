<?php

namespace App\Enums;

enum DocumentCategory: string
{
    case EnvironmentReport = 'rapport_environnement';
    case FinancialReport = 'rapport_financier';
    case Minutes = 'compte_rendu';
    case Other = 'divers';

    public function label(): string
    {
        return match ($this) {
            self::EnvironmentReport => 'Rapport environnement',
            self::FinancialReport => 'Rapport financier',
            self::Minutes => 'Compte rendu',
            self::Other => 'Divers',
        };
    }

    /** @return array<string, string> valeur => libellé */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $category) => [$category->value => $category->label()])
            ->all();
    }
}