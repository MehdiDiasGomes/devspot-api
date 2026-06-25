<?php

declare(strict_types=1);

namespace App\Domain\Auth\Ports;

use App\Domain\Auth\Entities\User;
use App\Domain\Auth\ValueObjects\SocialUser;

interface AuthRepositoryPort
{
    /** Finds an existing user by email or creates a new one from OAuth data. */
    public function findOrCreateFromOAuth(
        SocialUser $socialUser,
        string $provider
    ): User;
}
