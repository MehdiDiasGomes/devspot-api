<?php

declare(strict_types=1);

namespace App\Application\JobApplication\UseCases;

use App\Domain\JobApplication\Entities\JobApplication;
use App\Domain\JobApplication\Ports\JobApplicationRepositoryPort;
use App\Domain\JobApplication\ValueObjects\ApplicationData;

final class CreateJobApplicationUseCase
{
    public function __construct(
        private readonly JobApplicationRepositoryPort $repository,
    ) {}

    /**
     * Creates and persists a new job application for the given user.
     */
    public function execute(int $userId, ApplicationData $data): JobApplication
    {
        return $this->repository->create($userId, $data);
    }
}
