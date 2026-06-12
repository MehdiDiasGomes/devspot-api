<?php

declare(strict_types=1);

namespace App\Domain\JobOffer\ValueObjects;

enum JobSource: string
{
    case Adzuna = 'adzuna';
    case FranceTravail = 'france_travail';
}
