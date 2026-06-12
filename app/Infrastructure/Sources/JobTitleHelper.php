<?php

declare(strict_types=1);

namespace App\Infrastructure\Sources;

final class JobTitleHelper
{
    private const array ALTERNANCE_KEYWORDS = [
        'alternance',
        'alternant',
        'alternante',
        'apprentissage',
        'apprenti',
        'apprentie',
        'professionnalisation',
    ];

    public static function isAlternance(string $title): bool
    {
        $lower = strtolower($title);

        foreach (self::ALTERNANCE_KEYWORDS as $keyword) {
            if (str_contains($lower, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
