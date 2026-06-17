<?php

declare(strict_types=1);

namespace App\Domain\JobApplication\ValueObjects;

/**
 * Data transfer object carrying the mutable fields of a job application.
 * Used for both creation and update operations.
 */
final class ApplicationData
{
    /**
     * @param string[] $tags
     */
    public function __construct(
        public readonly string            $company,
        public readonly string            $position,
        public readonly ApplicationStatus $status,
        public readonly ApplicationSource $source,
        public readonly ?\DateTimeImmutable $appliedAt,
        public readonly ?string           $location,
        public readonly ?string           $salary,
        public readonly array             $tags,
        public readonly bool              $isRemote,
        public readonly ?string           $offerUrl,
        public readonly ?string           $notes,
        public readonly ?string           $message,
    ) {}
}
