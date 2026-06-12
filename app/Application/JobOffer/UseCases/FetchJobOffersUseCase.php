<?php

declare(strict_types=1);

namespace App\Application\JobOffer\UseCases;

use App\Domain\JobOffer\Entities\JobOffer;
use App\Domain\JobOffer\Ports\JobOfferRepositoryPort;
use App\Domain\JobOffer\Ports\JobSourcePort;

final class FetchJobOffersUseCase
{
    /**
     * @param array<JobSourcePort> $sources
     */
    public function __construct(
        private readonly array $sources,
        private readonly JobOfferRepositoryPort $repository,
    ) {}

    /**
     * Fetches offers from all sources, deduplicates, and persists new ones.
     *
     * @return int Number of new offers saved.
     */
    public function execute(): int
    {
        $allOffers = [];

        foreach ($this->sources as $source) {
            $offers = $source->fetchOffers();
            array_push($allOffers, ...$offers);
        }

        if (empty($allOffers)) {
            return 0;
        }

        $hashes = array_map(fn (JobOffer $offer) => $offer->deduplicationHash, $allOffers);
        $existingHashes = array_flip($this->repository->findExistingHashes($hashes));

        $newOffers = array_values(
            array_filter($allOffers, fn (JobOffer $offer) => !isset($existingHashes[$offer->deduplicationHash]))
        );

        if (!empty($newOffers)) {
            $this->repository->saveMany($newOffers);
        }

        return count($newOffers);
    }
}
