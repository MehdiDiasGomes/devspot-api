<?php

declare(strict_types=1);

namespace App\Infrastructure\Sources;

use App\Domain\JobOffer\Entities\JobOffer;
use App\Domain\JobOffer\ValueObjects\JobSource;
use App\Domain\JobOffer\ValueObjects\JobType;
use Illuminate\Support\Facades\Log;

final class AdzunaJobMapper
{
    private const array CURRENCY_MAP = [
        'fr' => 'EUR',
        'lu' => 'EUR',
        'gb' => 'GBP',
        'us' => 'USD',
    ];

    private const array JOB_TYPE_MAP = [
        'permanent' => JobType::FullTime,
        'contract' => JobType::Contract,
        'part_time' => JobType::PartTime,
    ];

    public static function toJobOffer(array $job, string $country): ?JobOffer
    {
        try {
            $location = $job['location']['display_name'] ?? null;
            $salaryMin = isset($job['salary_min']) ? (int) $job['salary_min'] : null;
            $salaryMax = isset($job['salary_max']) ? (int) $job['salary_max'] : null;
            $contractType = $job['contract_type'] ?? $job['contract_time'] ?? '';
            $currency = self::CURRENCY_MAP[$country] ?? 'EUR';

            return JobOffer::create(
                title: $job['title'],
                company: $job['company']['display_name'] ?? 'Unknown',
                location: $location,
                isRemote: false,
                type: self::JOB_TYPE_MAP[$contractType] ?? JobType::Other,
                contractLabel: null,
                salaryMin: $salaryMin,
                salaryMax: $salaryMax,
                salaryCurrency: $currency,
                description: $job['description'] ?? null,
                tags: [],
                source: JobSource::Adzuna,
                sourceUrl: $job['redirect_url'],
                sourceId: (string) $job['id'],
                publishedAt: new \DateTimeImmutable($job['created']),
                latitude: isset($job['latitude']) ? (float) $job['latitude'] : null,
                longitude: isset($job['longitude']) ? (float) $job['longitude'] : null,
            );
        } catch (\Throwable $e) {
            Log::warning('Adzuna: failed to map job offer', ['job' => $job, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
