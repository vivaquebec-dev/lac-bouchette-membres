@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="card card--narrow">
    <h1>Mot de passe oublié</h1>
    <p class="muted">Saisissez votre adresse courriel : si un compte actif y est associé, vous recevrez un lien pour choisir un nouveau mot de passe.</p>

    @if (session('status'))
        <div class="alert alert--success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email">Adresse courriel</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email')<p class="error">{{ $message }}</p>@enderror

        <button type="submit" class="btn">Envoyer le lien</button>
    </form>

    <p class="muted"><a href="{{ route('login') }}">Retour à la connexion</a></p>
</div>
@endsection