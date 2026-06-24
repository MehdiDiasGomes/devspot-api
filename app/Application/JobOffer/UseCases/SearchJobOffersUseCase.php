<?php

declare(strict_types=1);

namespace App\Application\JobOffer\UseCases;

use App\Domain\JobOffer\ValueObjects\JobOfferFilters;
use App\Domain\JobOffer\ValueObjects\Page;
use App\Infrastructure\Sources\AdzunaLiveSearchAdapter;

final class SearchJobOffersUseCase
{
    private const string DEFAULT_KEYWORD = 'développeur';

    public function __construct(
        private readonly AdzunaLiveSearchAdapter $liveSearch,
    ) {}

    /**
     * Returns job offers from Adzuna live search.
     * Falls back to a default keyword when no search term is provided.
     */
    public function execute(JobOfferFilters $filters, int $page = 1, int $perPage = 20): Page
    {
        ['offers' => $offers, 'total' => $total] = $this->liveSearch->search(
            keyword: $filters->search ?? self::DEFAULT_KEYWORD,
            location: $filters->location,
            page: $page,
            perPage: $perPage,
            radius: $filters->radius,
        );

        return new Page(
            items: $offers,
            total: $total,
            perPage: $perPage,
            currentPage: $page,
        );
    }
}
