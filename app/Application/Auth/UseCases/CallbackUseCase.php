<?php

declare(strict_types=1);

namespace App\Application\Auth\UseCases;

use App\Domain\Auth\Ports\AuthRepositoryPort;
use App\Domain\Auth\Ports\OAuthProviderPort;
use Illuminate\Support\Facades\Auth;

final class CallbackUseCase
{
    public function __construct(
        private readonly AuthRepositoryPort $repository,
        private readonly OAuthProviderPort $oauthProvider
    ) {
    }

    /** Handles the OAuth callback, persists the user, starts a session, and returns the frontend redirect URL. */
    public function execute(string $provider): string
    {
        $socialUser = $this->oauthProvider->getUser($provider);
        $user = $this->repository->findOrCreateFromOAuth($socialUser, $provider);
        Auth::login($user);
        return config('app.frontend_url') . '/auth/callback';
    }
}