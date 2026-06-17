<?php

declare(strict_types=1);

namespace App\Domain\JobApplication\Ports;

use App\Domain\JobApplication\Entities\JobApplication;
use App\Domain\JobApplication\ValueObjects\ApplicationData;

interface JobApplicationRepositoryPort
{
    /**
     * Returns all applications belonging to the given user.
     *
     * @return JobApplication[]
     */
    public function findAllForUser(int $userId): array;

    /**
     * Returns a single application by ID, scoped to the given user.
     * Returns null if not found or the application belongs to another user.
     */
    public function findByIdForUser(int $id, int $userId): ?JobApplication;

    /**
     * Persists a new application and returns it with its generated ID.
     */
    public function create(int $userId, ApplicationData $data): JobApplication;

    /**
     * Updates an existing application and returns the updated entity.
     */
    public function update(int $id, int $userId, ApplicationData $data): JobApplication;

    /**
     * Deletes an application scoped to the given user.
     */
    public function delete(int $id, int $userId): void;
}
