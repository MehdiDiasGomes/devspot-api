<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class BrandfetchService
{
    private const string SEARCH_ENDPOINT = 'https://api.brandfetch.io/v2/search/%s';

    public function __construct(private readonly string $apiKey) {}

    /**
     * Resolves a company logo URL from a company name via Brandfetch search.
     * Returns the CDN logo URL if found, empty string if not found, null on error.
     */
    public function resolveLogoUrl(string $companyName): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeader('Authorization', "Bearer {$this->apiKey}")
                ->get(sprintf(self::SEARCH_ENDPOINT, urlencode($companyName)));

            if (!$response->successful()) {
                Log::warning('Brandfetch search failed', [
                    'company' => $companyName,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $results = $response->json();

            if (empty($results) || !isset($results[0]['icon'])) {
                return '';
            }

            return $results[0]['icon'];
        } catch (\Throwable $e) {
            Log::error('Brandfetch service error', [
                'company' => $companyName,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
