<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Ports\OAuthProviderPort;
use App\Domain\Auth\ValueObjects\SocialUser;
use Laravel\Socialite\Facades\Socialite;

final class SocialiteOAuthProvider implements OAuthProviderPort
{
    /** Fetches the user from Socialite and maps it to a SocialUser value object. */
    public function getUser(string $provider): SocialUser
    {
        $socialiteUser = Socialite::driver($provider)->user();

        return new SocialUser(
            email: $socialiteUser->getEmail(),
            name: $socialiteUser->getName() ?? $socialiteUser->getNickname() ?? 'User',
            providerId: (string) $socialiteUser->getId(),
            avatar: $socialiteUser->getAvatar() ?? '',
        );
    }
}
