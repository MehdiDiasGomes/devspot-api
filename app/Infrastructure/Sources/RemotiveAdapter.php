<?php

declare(strict_types=1);

namespace App\Infrastructure\Sources;

use App\Domain\JobOffer\Entities\JobOffer;
use App\Domain\JobOffer\Ports\JobSourcePort;
use App\Domain\JobOffer\ValueObjects\JobSource;
use App\Domain\JobOffer\ValueObjects\JobType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class RemotiveAdapter implements JobSourcePort
{
    private const string ENDPOINT = 'https://remotive.com/api/remote-jobs';

    private const array JOB_TYPE_MAP = [
        'full_time' => JobType::FullTime,
        'contract' => JobType::Contract,
        'part_time' => JobType::PartTime,
        'freelance' => JobType::Freelance,
        'internship' => JobType::Internship,
    ];

    public function fetchOffers(): array
    {
        try {
            $response = Http::timeout(15)->get(self::ENDPOINT, [
                'category' => 'software-dev',
            ]);

            if (!$response->successful()) {
                Log::error('Remotive API request failed', ['status' => $response->status()]);
                return [];
            }

            return array_filter(
                array_map(
                    fn (array $job) => $this->toJobOffer($job),
                    $response->json('jobs', [])
                )
            );
        } catch (\Throwable $e) {
            Log::error('Remotive adapter error', ['message' => $e->getMessage()]);
            return [];
        }
    }

    private function toJobOffer(array $job): ?JobOffer
    {
        try {
            $location = $job['candidate_required_location'] ?? null;
            $isRemote = $location === null
                || str_contains(strtolower((string) $location), 'worldwide')
                || str_contains(strtolower((string) $location), 'remote');

            return JobOffer::create(
                title: $job['title'],
                company: $job['company_name'],
                location: $isRemote ? null : $location,
                isRemote: $isRemote,
                type: self::JOB_TYPE_MAP[$job['job_type'] ?? ''] ?? JobType::Other,
                salaryMin: null,
                salaryMax: null,
                salaryCurrency: null,
                description: $job['description'] ?? null,
                tags: array_map('strtolower', $job['tags'] ?? []),
                source: JobSource::Remotive,
                sourceUrl: $job['url'],
                sourceId: (string) $job['id'],
                publishedAt: new \DateTimeImmutable($job['publication_date']),
            );
        } catch (\Throwable $e) {
            Log::warning('Remotive: failed to map job offer', ['job' => $job, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
