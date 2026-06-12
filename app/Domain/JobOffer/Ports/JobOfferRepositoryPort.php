<?php

declare(strict_types=1);

namespace App\Domain\JobOffer\Ports;

use App\Domain\JobOffer\Entities\JobOffer;
use App\Domain\JobOffer\ValueObjects\JobOfferFilters;
use App\Domain\JobOffer\ValueObjects\Page;

interface JobOfferRepositoryPort
{
    /**
     * Persists multiple job offers in bulk.
     *
     * @param array<JobOffer> $offers
     */
    public function saveMany(array $offers): void;

    /**
     * Returns the hashes that already exist among the given list.
     *
     * @param  array<string> $hashes
     * @return array<string>
     */
    public function findExistingHashes(array $hashes): array;

    /**
     * Returns a paginated list of job offers matching the given filters.
     */
    public function findPaginated(JobOfferFilters $filters, int $page, int $perPage): Page;
}
