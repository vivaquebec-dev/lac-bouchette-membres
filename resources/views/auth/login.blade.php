@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="card card--narrow">
    <h1>Connexion</h1>

    @if (session('status'))
        <div class="alert alert--success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">Adresse courriel</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email')<p class="error">{{ $message }}</p>@enderror

        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">
        @error('password')<p class="error">{{ $message }}</p>@enderror

        <button type="submit" class="btn">Se connecter</button>
    </form>

    <p class="muted"><a href="{{ route('password.request') }}">Mot de passe oublié ?</a></p>
</div>
@endsection