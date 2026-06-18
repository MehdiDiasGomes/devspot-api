<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Domain\JobOffer\Entities\JobOffer;
use App\Infrastructure\Persistence\Models\CompanyLogoModel;
use Illuminate\Support\Facades\Http;

final class CompanyLogoEnricher
{
    public function __construct(
        private readonly BrandfetchService $brandfetch,
    ) {}

    /**
     * Enriches a list of job offers with company logo URLs.
     * Looks up cached logos first, then fetches missing ones from Brandfetch.
     *
     * @param  array<JobOffer> $offers
     * @return array<JobOffer>
     */
    public function enrich(array $offers): array
    {
        // Collect unique normalized company names
        $nameMap = []; // normalized → original
        foreach ($offers as $offer) {
            $normalized = $this->normalize($offer->company);
            if ($normalized !== '') {
                $nameMap[$normalized] = $offer->company;
            }
        }

        if (empty($nameMap)) {
            return $offers;
        }

        // Batch lookup from DB
        $cached = CompanyLogoModel::whereIn('company_name', array_keys($nameMap))
            ->get()
            ->keyBy('company_name');

        // Separate cached from missing
        $logos = []; // normalized → logo_url|null
        $missing = []; // normalized → original name

        foreach ($nameMap as $normalized => $original) {
            if ($cached->has($normalized)) {
                $logos[$normalized] = $cached[$normalized]->logo_url;
            } else {
                $missing[$normalized] = $original;
            }
        }

        // Fetch all missing logos in parallel
        if (!empty($missing)) {
            $responses = Http::pool(fn ($pool) => array_map(
                fn (string $original) => $this->brandfetch->poolRequest($pool, $original),
                array_values($missing)
            ));

            $normalizedKeys = array_keys($missing);
            $originals = array_values($missing);
            $now = now();

            foreach ($responses as $i => $response) {
                $normalized = $normalizedKeys[$i];
                $original = $originals[$i];
                $logoUrl = null;

                if (!($response instanceof \Throwable) && $response->successful()) {
                    $results = $response->json();
                    if (is_array($results) && !empty($results)) {
                        $brandName = $results[0]['name'] ?? '';
                        if ($this->isSufficientMatch($original, $brandName)) {
                            $logoUrl = $results[0]['icon'] ?? null;
                        }
                    }
                }

                CompanyLogoModel::create([
                    'company_name' => $normalized,
                    'logo_url' => $logoUrl,
                    'fetched_at' => $now,
                ]);
                $logos[$normalized] = $logoUrl;
            }
        }

        // Attach logos to offers
        return array_map(function (JobOffer $offer) use ($logos): JobOffer {
            $normalized = $this->normalize($offer->company);
            $logoUrl = $logos[$normalized] ?? null;

            return $logoUrl !== null ? $offer->withLogoUrl($logoUrl) : $offer;
        }, $offers);
    }

    /**
     * Returns true if the Brandfetch result name is a close enough match
     * to the searched company name to be trusted.
     *
     * Uses similar_text similarity with a 75% threshold, and rejects results
     * where the brand name is more than 50% longer than the searched name
     * (prevents "Open" → "OpenAI" type false positives).
     */
    private function isSufficientMatch(string $searched, string $brandName): bool
    {
        $a = $this->normalize($searched);
        $b = $this->normalize($brandName);

        if ($a === '' || $b === '') {
            return false;
        }

        // Reject if brand name is significantly longer than searched name
        if (strlen($b) >= strlen($a) * 1.5) {
            return false;
        }

        similar_text($a, $b, $percent);

        return $percent >= 75.0;
    }

    /**
     * Normalizes a company name for consistent DB lookup.
     * Strips legal suffixes, punctuation, and lowercases.
     */
    private function normalize(string $name): string
    {
        $name = strtolower($name);

        // Strip common legal suffixes
        $suffixes = ['s\.a\.s\.?', 's\.a\.?', 'sarl', 'sasu', 'eurl', 'snc', 'inc\.?', 'ltd\.?', 'gmbh', 'b\.v\.?', 'llc'];
        $pattern = '/\b(' . implode('|', $suffixes) . ')\b/i';
        $name = preg_replace($pattern, '', $name) ?? $name;

        // Strip punctuation and extra whitespace
        $name = preg_replace('/[^a-z0-9\s]/u', ' ', $name) ?? $name;
        $name = preg_replace('/\s+/', ' ', $name) ?? $name;

        return trim($name);
    }
}
