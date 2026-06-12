<?php

declare(strict_types=1);

namespace App\Domain\JobOffer\ValueObjects;

final class JobOfferFilters
{
    /**
     * @param array<JobSource> $sources
     * @param array<string>    $tags
     */
    public function __construct(
        public readonly ?string $search = null,
        public readonly array $sources = [],
        public readonly ?JobType $type = null,
        public readonly ?bool $isRemote = null,
        public readonly array $tags = [],
    ) {}
}
