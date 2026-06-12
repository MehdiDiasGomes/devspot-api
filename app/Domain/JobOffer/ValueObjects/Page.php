<?php

declare(strict_types=1);

namespace App\Domain\JobOffer\ValueObjects;

use App\Domain\JobOffer\Entities\JobOffer;

final class Page
{
    public readonly int $lastPage;

    /**
     * @param array<JobOffer> $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage,
    ) {
        $this->lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
    }
}
