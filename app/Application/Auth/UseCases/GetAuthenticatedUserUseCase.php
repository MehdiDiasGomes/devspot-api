<?php

declare(strict_types=1);

namespace App\Application\Auth\UseCases;

use App\Domain\Auth\Entities\User;

final class GetAuthenticatedUserUseCase
{
    /** Returns the authenticated user. */
    public function execute(User $user): User
    {
        return $user;
    }
}