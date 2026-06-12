<?php

declare(strict_types=1);

namespace App\Domain\JobOffer\Ports;

use App\Domain\JobOffer\Entities\JobOffer;

interface JobSourcePort
{
    /**
     * Fetches job offers from the external source.
     *
     * @return array<JobOffer>
     */
    public function fetchOffers(): array;
}
