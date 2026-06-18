<?php

declare(strict_types=1);

namespace App\Application\JobApplication\UseCases;

use App\Domain\JobApplication\Entities\JobApplication;
use App\Domain\JobApplication\Ports\JobApplicationRepositoryPort;

final class ListJobApplicationsUseCase
{
    public function __construct(
        private readonly JobApplicationRepositoryPort $repository,
    ) {}

    /**
     * Returns all applications belonging to the given user.
     *
     * @return JobApplication[]
     */
    public function execute(int $userId): array
    {
        return $this->repository->findAllForUser($userId);
    }
}
