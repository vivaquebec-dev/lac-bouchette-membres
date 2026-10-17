@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<h1>Documents</h1>

<nav class="filters" aria-label="Filtrer par catégorie">
    <a href="{{ route('documents.index') }}"
       @class(['filters__item', 'is-active' => $selected === null])
       @if ($selected === null) aria-current="true" @endif>Tous</a>

    @foreach ($categories as $category)
        <a href="{{ route('documents.index', ['categorie' => $category->value]) }}"
           @class(['filters__item', 'is-active' => $selected === $category])
           @if ($selected === $category) aria-current="true" @endif>{{ $category->label() }}</a>
    @endforeach
</nav>

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
    <p class="muted">
        @if ($selected)
            Aucun document dans la catégorie « {{ $selected->label() }} ».
        @else
            Aucun document n'est disponible pour le moment.
        @endif
    </p>
@endforelse
@endsection