<?php

declare(strict_types=1);

namespace App\Domain\JobOffer\ValueObjects;

enum JobSource: string
{
    case Remotive = 'remotive';
    case Adzuna = 'adzuna';
}
