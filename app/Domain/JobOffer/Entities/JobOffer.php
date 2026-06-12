<?php

declare(strict_types=1);

namespace App\Domain\JobOffer\Entities;

use App\Domain\JobOffer\ValueObjects\JobSource;
use App\Domain\JobOffer\ValueObjects\JobType;

final class JobOffer
{
    /**
     * @param array<string> $tags
     */
    public function __construct(
        public readonly string $title,
        public readonly string $company,
        public readonly ?string $location,
        public readonly bool $isRemote,
        public readonly JobType $type,
        public readonly ?int $salaryMin,
        public readonly ?int $salaryMax,
        public readonly ?string $salaryCurrency,
        public readonly ?string $description,
        public readonly array $tags,
        public readonly JobSource $source,
        public readonly string $sourceUrl,
        public readonly string $sourceId,
        public readonly string $deduplicationHash,
        public readonly \DateTimeImmutable $publishedAt,
        public readonly ?string $id = null,
    ) {}

    /**
     * Creates a JobOffer and computes its deduplication hash from title, company and location.
     *
     * @param array<string> $tags
     */
    public static function create(
        string $title,
        string $company,
        ?string $location,
        bool $isRemote,
        JobType $type,
        ?int $salaryMin,
        ?int $salaryMax,
        ?string $salaryCurrency,
        ?string $description,
        array $tags,
        JobSource $source,
        string $sourceUrl,
        string $sourceId,
        \DateTimeImmutable $publishedAt,
    ): self {
        $hash = md5(
            strtolower(trim($title))
            . '|' . strtolower(trim($company))
            . '|' . strtolower(trim($location ?? ''))
        );

        return new self(
            title: $title,
            company: $company,
            location: $location,
            isRemote: $isRemote,
            type: $type,
            salaryMin: $salaryMin,
            salaryMax: $salaryMax,
            salaryCurrency: $salaryCurrency,
            description: $description,
            tags: $tags,
            source: $source,
            sourceUrl: $sourceUrl,
            sourceId: $sourceId,
            deduplicationHash: $hash,
            publishedAt: $publishedAt,
        );
    }
}
