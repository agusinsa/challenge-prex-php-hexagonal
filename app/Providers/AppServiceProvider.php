<?php

namespace App\Providers;

use App\Domain\Auth\PasswordVerifier;
use App\Domain\Auth\TokenIssuer;
use App\Domain\Auth\UserRepository;
use App\Domain\Favorite\FavoriteRepository;
use App\Domain\Gif\GifCatalog;
use App\Infrastructure\Auth\EloquentUserRepository;
use App\Infrastructure\Auth\HashPasswordVerifier;
use App\Infrastructure\Auth\PassportTokenIssuer;
use App\Infrastructure\Favorite\EloquentFavoriteRepository;
use App\Infrastructure\Gif\GiphyGifCatalog;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(PasswordVerifier::class, HashPasswordVerifier::class);
        $this->app->bind(TokenIssuer::class, PassportTokenIssuer::class);
        $this->app->bind(GifCatalog::class, fn () => new GiphyGifCatalog((string) config('services.giphy.key')));
        $this->app->bind(FavoriteRepository::class, EloquentFavoriteRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::personalAccessTokensExpireIn(now()->addMinutes(30));
    }
}
