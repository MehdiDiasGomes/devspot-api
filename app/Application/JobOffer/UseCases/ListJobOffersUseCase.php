<?php

declare(strict_types=1);

namespace App\Application\JobOffer\UseCases;

use App\Domain\JobOffer\Ports\JobOfferRepositoryPort;
use App\Domain\JobOffer\ValueObjects\JobOfferFilters;
use App\Domain\JobOffer\ValueObjects\Page;

final class ListJobOffersUseCase
{
    public function __construct(
        private readonly JobOfferRepositoryPort $repository,
    ) {}

    /**
     * Returns a paginated list of job offers matching the given filters.
     */
    public function execute(JobOfferFilters $filters, int $page = 1, int $perPage = 20): Page
    {
        return $this->repository->findPaginated($filters, $page, $perPage);
    }
}
