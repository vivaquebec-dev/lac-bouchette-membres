@extends('layouts.app')

@section('title', $invitation ? 'Définir votre mot de passe' : 'Nouveau mot de passe')

@section('content')
<div class="card card--narrow">
    <h1>{{ $invitation ? 'Définir votre mot de passe' : 'Nouveau mot de passe' }}</h1>

    <form method="POST" action="{{ $action }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label for="email">Adresse courriel</label>
        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" readonly required>
        @error('email')<p class="error">{{ $message }}</p>@enderror

        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" required autocomplete="new-password" minlength="12">
        <p class="muted">12 caractères minimum, avec des lettres et des chiffres.</p>
        @error('password')<p class="error">{{ $message }}</p>@enderror

        <label for="password_confirmation">Confirmer le mot de passe</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

        <button type="submit" class="btn">Enregistrer le mot de passe</button>
    </form>
</div>
@endsection