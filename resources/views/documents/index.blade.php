@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<h1>Documents</h1>

@forelse ($documents as $document)
    <article class="doc">
        <div>
            <h2>{{ $document->title }}</h2>
            @if ($document->description)
                <p>{{ $document->description }}</p>
            @endif
            <p class="muted">Publié le {{ $document->created_at->translatedFormat('j F Y') }}</p>
        </div>
        <div class="doc__action">
            {{-- Le téléchargement sécurisé sera branché à l'étape 7. --}}
            <span class="btn btn--disabled" aria-disabled="true">Télécharger</span>
        </div>
    </article>
@empty
    <p class="muted">Aucun document n'est disponible pour le moment.</p>
@endforelse
@endsection