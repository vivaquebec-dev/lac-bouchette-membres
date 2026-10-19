<?php

namespace App\Providers;

use Carbon\Carbon;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        Password::defaults(fn () => Password::min(12)->letters()->numbers());
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn (): string => '<p style="text-align: center; margin-top: 1rem; font-size: 0.875rem;">'
                .'<a href="'.e(route('password.request')).'" style="text-decoration: underline;">Mot de passe oublié ?</a>'
                .'</p>',
        );
    }
}
