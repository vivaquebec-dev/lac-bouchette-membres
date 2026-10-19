<?php

namespace App\Rules;

use App\Support\DocumentFileTypes;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use ZipArchive;

class AllowedDocumentFile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $files = is_array($value) ? $value : [$value];

        foreach ($files as $file) {
            // Un fichier déjà enregistré (chaîne) n'est pas revalidé.
            if (! $file instanceof TemporaryUploadedFile) {
                continue;
            }

            if (! $this->isValid($file)) {
                $fail('Le fichier doit être un PDF, un document Word (.doc, .docx) ou un classeur Excel (.xls, .xlsx) valide.');

                return;
            }
        }
    }

    private function isValid(TemporaryUploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if (! in_array($extension, DocumentFileTypes::extensions(), true)
            || ! is_string($path)
            || ! is_readable($path)) {
            return false;
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return false;
        }

        $header = (string) fread($handle, 1024);
        fclose($handle);

        return match ($extension) {
            'pdf' => str_contains($header, '%PDF-'),
            'doc', 'xls' => str_starts_with($header, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"),
            'docx' => $this->isOpenXmlPackage($path, 'word/'),
            'xlsx' => $this->isOpenXmlPackage($path, 'xl/'),
            default => false,
        };
    }

    private function isOpenXmlPackage(string $path, string $folder): bool
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            return false;
        }

        $hasContentTypes = $zip->locateName('[Content_Types].xml') !== false;
        $hasFolder = false;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            if (str_starts_with((string) $zip->getNameIndex($i), $folder)) {
                $hasFolder = true;
                break;
            }
        }

        $zip->close();

        return $hasContentTypes && $hasFolder;
    }
}