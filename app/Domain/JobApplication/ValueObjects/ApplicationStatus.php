<?php

declare(strict_types=1);

namespace App\Domain\JobApplication\ValueObjects;

enum ApplicationStatus: string
{
    case Repere    = 'repere';
    case Postule   = 'postule';
    case Relance   = 'relance';
    case Entretien = 'entretien';
    case TestTech  = 'test_tech';
    case Offre     = 'offre';
    case Refus     = 'refus';
    case Abandonne = 'abandonne';
}
