<?php

declare(strict_types=1);

namespace App\Domain\JobApplication\Entities;

use App\Domain\JobApplication\ValueObjects\ApplicationSource;
use App\Domain\JobApplication\ValueObjects\ApplicationStatus;

final class JobApplication
{
    /**
     * @param string[] $tags
     */
    public function __construct(
        public readonly int               $userId,
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
        public readonly ?string           $coverLetterPath,
        public readonly ?string           $message,
        public readonly ?int              $id = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {}
}
