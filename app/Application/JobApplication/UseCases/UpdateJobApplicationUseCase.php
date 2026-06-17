<?php

declare(strict_types=1);

namespace App\Application\JobApplication\UseCases;

use App\Domain\JobApplication\Entities\JobApplication;
use App\Domain\JobApplication\Ports\JobApplicationRepositoryPort;
use App\Domain\JobApplication\ValueObjects\ApplicationData;

final class UpdateJobApplicationUseCase
{
    public function __construct(
        private readonly JobApplicationRepositoryPort $repository,
    ) {}

    /**
     * Updates an existing job application scoped to the given user.
     * Throws a ModelNotFoundException if the application does not exist or belongs to another user.
     */
    public function execute(int $id, int $userId, ApplicationData $data): JobApplication
    {
        return $this->repository->update($id, $userId, $data);
    }
}
