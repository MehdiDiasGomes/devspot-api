<?php

declare(strict_types=1);

namespace App\Domain\JobOffer\ValueObjects;

final class JobOfferFilters
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $location = null,
        public readonly ?int $radius = null,
    ) {}
}
