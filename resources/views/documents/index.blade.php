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
            <p class="muted">
                <span class="badge">{{ $document->category->label() }}</span>
                Publié le {{ $document->created_at->translatedFormat('j F Y') }}
            </p>
        </div>
        <div class="doc__action">
            <a class="btn" href="{{ route('documents.download', $document) }}">Télécharger</a>
        </div>
    </article>
@empty
    <p class="muted">Aucun document n'est disponible pour le moment.</p>
@endforelse
@endsection