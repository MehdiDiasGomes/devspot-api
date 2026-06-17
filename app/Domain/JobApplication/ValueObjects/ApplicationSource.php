<?php

declare(strict_types=1);

namespace App\Domain\JobApplication\ValueObjects;

enum ApplicationSource: string
{
    case LinkedIn   = 'linkedin';
    case Indeed     = 'indeed';
    case Reseau     = 'reseau';
    case SiteDirect = 'site_direct';
    case Autre      = 'autre';
}
