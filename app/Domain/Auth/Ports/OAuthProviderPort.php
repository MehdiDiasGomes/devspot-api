<?php

declare(strict_types=1);

namespace App\Domain\Auth\Ports;

use App\Domain\Auth\ValueObjects\SocialUser;

interface OAuthProviderPort
{
    /** Fetches the authenticated user from the given OAuth provider. */
    public function getUser(string $provider): SocialUser;
}