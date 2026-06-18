<?php

declare(strict_types=1);

namespace App\Infrastructure\Sources;

use App\Domain\JobOffer\Entities\JobOffer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class AdzunaLiveSearchAdapter
{
    private const string ENDPOINT = 'https://api.adzuna.com/v1/api/jobs/%s/search/%d';

    public function __construct(
        private readonly string $appId,
        private readonly string $appKey,
        private readonly string $country,
    ) {}

    /**
     * Search Adzuna live with the given keyword, optional location and radius.
     *
     * @return array{offers: array<JobOffer>, total: int}
     */
    public function search(string $keyword, ?string $location, int $page, int $perPage, ?int $radius = null): array
    {
        try {
            $endpoint = sprintf(self::ENDPOINT, $this->country, $page);

            $params = [
                'app_id' => $this->appId,
                'app_key' => $this->appKey,
                'results_per_page' => $perPage,
                'what' => $keyword,
                'content-type' => 'application/json',
            ];

            if ($location !== null) {
                $params['where'] = $location;
                $params['distance'] = $radius ?? 30;
            }

            $response = Http::timeout(15)->get($endpoint, $params);

            if (!$response->successful()) {
                Log::error('Adzuna live search failed', [
                    'status' => $response->status(),
                    'keyword' => $keyword,
                    'location' => $location,
                ]);
                return ['offers' => [], 'total' => 0];
            }

            $offers = array_values(array_filter(
                array_map(
                    fn (array $job) => AdzunaJobMapper::toJobOffer($job, $this->country),
                    $response->json('results', [])
                )
            ));

            return [
                'offers' => $offers,
                'total' => $response->json('count', 0),
            ];
        } catch (\Throwable $e) {
            Log::error('Adzuna live search error', ['message' => $e->getMessage()]);
            return ['offers' => [], 'total' => 0];
        }
    }
}
