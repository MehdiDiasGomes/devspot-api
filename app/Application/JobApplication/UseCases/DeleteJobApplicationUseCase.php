<?php

declare(strict_types=1);

namespace App\Application\JobApplication\UseCases;

use App\Domain\JobApplication\Ports\JobApplicationRepositoryPort;

final class DeleteJobApplicationUseCase
{
    public function __construct(
        private readonly JobApplicationRepositoryPort $repository,
    ) {}

    /**
     * Deletes a job application scoped to the given user.
     * Throws a ModelNotFoundException if the application does not exist or belongs to another user.
     */
    public function execute(int $id, int $userId): void
    {
        $this->repository->delete($id, $userId);
    }
}
