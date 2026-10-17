<?php

namespace App\Models;

use App\Enums\DocumentCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'file_path',
        'original_filename',
    ];

    protected function casts(): array
    {
        return [
            'category' => DocumentCategory::class,
        ];
    }

    protected static function booted(): void
    {
        // Fichier remplacé : l'ancien est supprimé seulement après la sauvegarde réussie.
        static::updated(function (Document $document): void {
            if ($document->wasChanged('file_path')) {
                self::deleteStoredFile($document->getOriginal('file_path'));
            }
        });

        // Document supprimé (y compris en lot) : son fichier l'est aussi.
        static::deleted(function (Document $document): void {
            self::deleteStoredFile($document->file_path);
        });
    }

    private static function deleteStoredFile(?string $path): void
    {
        // Le chemin vient de la base de données ; on refuse tout ce qui contient un répertoire.
        if (! $path || $path !== basename($path)) {
            return;
        }

        // afterCommit : si une transaction est annulée, le fichier est conservé.
        DB::afterCommit(function () use ($path): void {
            try {
                Storage::disk('documents')->delete($path);
            } catch (\Throwable $e) {
                report($e);
            }
        });
    }
}