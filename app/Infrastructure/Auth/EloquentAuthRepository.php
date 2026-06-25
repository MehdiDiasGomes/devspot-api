<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Entities\User;
use App\Domain\Auth\Ports\AuthRepositoryPort;
use App\Domain\Auth\ValueObjects\SocialUser;

final class EloquentAuthRepository implements AuthRepositoryPort
{
    /** Finds an existing user by email or creates a new one, then persists OAuth data. */
    public function findOrCreateFromOAuth(SocialUser $socialUser, string $provider): User
    {
        $user = User::firstOrNew(['email' => $socialUser->email]);
        $user->name = $socialUser->name;
        $user->provider = $provider;
        $user->provider_id = $socialUser->providerId;
        $user->avatar = $socialUser->avatar;
        $user->save();

        return $user;
    }
}
