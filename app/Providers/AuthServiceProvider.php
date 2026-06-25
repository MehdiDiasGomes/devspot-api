<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Auth\Ports\AuthRepositoryPort;
use App\Domain\Auth\Ports\OAuthProviderPort;
use App\Infrastructure\Auth\EloquentAuthRepository;
use App\Infrastructure\Auth\SocialiteOAuthProvider;
use Illuminate\Support\ServiceProvider;

final class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OAuthProviderPort::class, SocialiteOAuthProvider::class);
        $this->app->bind(AuthRepositoryPort::class, EloquentAuthRepository::class);
    }
}
