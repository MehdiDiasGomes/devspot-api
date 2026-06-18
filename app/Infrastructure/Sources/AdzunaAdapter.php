<?php

declare(strict_types=1);

namespace App\Infrastructure\Sources;

use App\Domain\JobOffer\Entities\JobOffer;
use App\Domain\JobOffer\Ports\JobSourcePort;
use App\Domain\JobOffer\ValueObjects\JobSource;
use App\Domain\JobOffer\ValueObjects\JobType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class AdzunaAdapter implements JobSourcePort
{
    private const string ENDPOINT = 'https://api.adzuna.com/v1/api/jobs/%s/search/1';

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

    public function __construct(
        private readonly string $appId,
        private readonly string $appKey,
        private readonly string $country,
        private readonly ?string $location = null,
    ) {}

    public function fetchOffers(): array
    {
        try {
            $endpoint = sprintf(self::ENDPOINT, $this->country);

            $params = [
                'app_id' => $this->appId,
                'app_key' => $this->appKey,
                'results_per_page' => 50,
                'content-type' => 'application/json',
            ];

            if ($this->location !== null) {
                $params['where'] = $this->location;
                $params['distance'] = 50;
            }

            $response = Http::timeout(15)->get($endpoint, $params);

            if (!$response->successful()) {
                Log::error('Adzuna API request failed', [
                    'country' => $this->country,
                    'status' => $response->status(),
                ]);
                return [];
            }

            return array_values(array_filter(
                array_map(
                    fn (array $job) => $this->toJobOffer($job),
                    $response->json('results', [])
                )
            ));
        } catch (\Throwable $e) {
            Log::error('Adzuna adapter error', ['country' => $this->country, 'message' => $e->getMessage()]);
            return [];
        }
    }

    private function toJobOffer(array $job): ?JobOffer
    {
        try {
            $location = $job['location']['display_name'] ?? null;
            $salaryMin = isset($job['salary_min']) ? (int) $job['salary_min'] : null;
            $salaryMax = isset($job['salary_max']) ? (int) $job['salary_max'] : null;
            $contractType = $job['contract_type'] ?? $job['contract_time'] ?? '';
            $currency = self::CURRENCY_MAP[$this->country] ?? 'EUR';

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
