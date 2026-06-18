<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class BrandfetchService
{
    private const string ENDPOINT = 'https://api.brandfetch.io/v2/search/%s';

    public function __construct(
        private readonly string $apiKey,
    ) {}

    /**
     * Returns a Brandfetch request attached to a pool for parallel execution.
     */
    public function poolRequest(Pool $pool, string $companyName): mixed
    {
        return $pool->timeout(5)
            ->withHeader('Authorization', "Bearer {$this->apiKey}")
            ->get(sprintf(self::ENDPOINT, urlencode($companyName)));
    }
}
