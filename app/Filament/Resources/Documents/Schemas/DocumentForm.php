<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Titre')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Description')
                ->rows(4)
                ->maxLength(2000),

            FileUpload::make('file_path')
                ->label('Fichier (PDF, DOC ou DOCX)')
                ->helperText('Taille maximale : 10 Mo. Pour remplacer le fichier, retirez l\'actuel puis déposez le nouveau.')
                ->disk('documents')
                ->visibility('private')
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])
                ->maxSize(10240)
                ->storeFileNamesIn('original_filename')
                // Filament ne supprime jamais de fichier : c'est le modèle qui s'en charge.
                ->deleteUploadedFileUsing(fn () => null)
                ->required(),

            Hidden::make('original_filename'),
        ]);
    }
}