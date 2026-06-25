<?php

declare(strict_types=1);

namespace App\Domain\Auth\ValueObjects;

final class SocialUser
{
    public function __construct(
        public readonly string $email,
        public readonly string $name,
        public readonly string $providerId,
        public readonly string $avatar,
    ) {
    }
}