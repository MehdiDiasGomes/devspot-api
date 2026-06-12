<?php

declare(strict_types=1);

namespace App\Domain\JobOffer\ValueObjects;

enum JobType: string
{
    case FullTime = 'full_time';
    case Contract = 'contract';
    case Freelance = 'freelance';
    case PartTime = 'part_time';
    case Internship = 'internship';
    case Other = 'other';
}
