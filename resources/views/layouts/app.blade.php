<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Accueil') – {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/portail.css') }}">
</head>
<body>
    <header class="topbar">
        <div class="container topbar__inner">
            <a class="brand" href="{{ url('/') }}">{{ config('app.name') }}</a>
            @auth
                <nav class="topbar__nav">
                    @if (auth()->user()->isAdmin())
                        <a href="{{ url('/admin') }}">Administration</a>
                    @endif
                    <span class="muted">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="link-button">Se déconnecter</button>
                    </form>
                </nav>
            @endauth
        </div>
    </header>

    <main class="container">
        @yield('content')
    </main>
</body>
</html>