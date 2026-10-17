<?php

namespace App\Http\Controllers;

use App\Enums\DocumentCategory;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    private const CONTENT_TYPES = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Document::class);

        $value = $request->query('categorie');
        $selected = is_string($value) ? DocumentCategory::tryFrom($value) : null;

        $documents = Document::query()
            ->when($selected, fn ($query) => $query->where('category', $selected->value))
            ->latest()
            ->latest('id')
            ->get();

        return view('documents.index', [
            'documents' => $documents,
            'categories' => DocumentCategory::cases(),
            'selected' => $selected,
        ]);
    }

    public function download(Document $document): StreamedResponse
    {
        Gate::authorize('view', $document);

        $path = (string) $document->file_path;

        // Défense en profondeur : le chemin vient de la base de données, jamais
        // du navigateur, et il ne doit contenir aucun répertoire.
        abort_unless($path !== '' && $path === basename($path), 404);

        $disk = Storage::disk('documents');

        if (! $disk->exists($path)) {
            Log::warning('Fichier de document introuvable sur le disque.', ['document_id' => $document->id]);

            abort(404);
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        abort_unless(isset(self::CONTENT_TYPES[$extension]), 404);

        return $disk->download($path, $this->downloadName($document, $extension), [
            'Content-Type' => self::CONTENT_TYPES[$extension],
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function downloadName(Document $document, string $extension): string
    {
        $base = pathinfo($document->original_filename ?: $document->title, PATHINFO_FILENAME);

        $base = Str::of($base)
            ->replaceMatches('/[\x00-\x1F\x7F\\\\\/:*?"<>|]+/u', '')
            ->squish()
            ->limit(120, '')
            ->trim('. ')
            ->toString();

        return ($base !== '' ? $base : 'document').'.'.$extension;
    }
}