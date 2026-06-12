<?php

declare(strict_types=1);

namespace App\Infrastructure\Sources;

use App\Domain\JobOffer\Entities\JobOffer;
use App\Domain\JobOffer\Ports\JobSourcePort;
use App\Domain\JobOffer\ValueObjects\JobSource;
use App\Domain\JobOffer\ValueObjects\JobType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class FranceTravailAdapter implements JobSourcePort
{
    private const string TOKEN_ENDPOINT = 'https://entreprise.francetravail.fr/connexion/oauth2/access_token';
    private const string SEARCH_ENDPOINT = 'https://api.francetravail.io/partenaire/offresdemploi/v2/offres/search';
    private const string OFFER_URL = 'https://candidat.francetravail.fr/offres/emploi/detail-offre/%s';

    private const array JOB_TYPE_MAP = [
        'CDI' => JobType::FullTime,
        'CDD' => JobType::Contract,
        'MIS' => JobType::Contract,
        'SAI' => JobType::Contract,
        'LIB' => JobType::Freelance,
        'FRA' => JobType::Freelance,
        'ALT' => JobType::Internship,
        'TTI' => JobType::Contract,
    ];

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly ?string $department = null,
    ) {}

    public function fetchOffers(): array
    {
        $token = $this->fetchAccessToken();

        if ($token === null) {
            return [];
        }

        try {
            $params = [
                'motsCles' => 'développeur fullstack',
                'nbMaxResultats' => 150,
            ];

            if ($this->department !== null) {
                $params['departement'] = $this->department;
            }

            $response = Http::timeout(15)
                ->withToken($token)
                ->get(self::SEARCH_ENDPOINT, $params);

            if (!$response->successful()) {
                Log::error('FranceTravail search failed', ['status' => $response->status()]);
                return [];
            }

            return array_values(array_filter(
                array_map(
                    fn (array $job) => $this->toJobOffer($job),
                    $response->json('resultats', [])
                )
            ));
        } catch (\Throwable $e) {
            Log::error('FranceTravail adapter error', ['message' => $e->getMessage()]);
            return [];
        }
    }

    private function fetchAccessToken(): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post(self::TOKEN_ENDPOINT . '?realm=%2Fpartenaire', [
                    'grant_type' => 'client_credentials',
                    'scope' => 'api_offresdemploiv2 o2dsoffre',
                ]);

            if (!$response->successful()) {
                Log::error('FranceTravail token request failed', ['status' => $response->status()]);
                return null;
            }

            return $response->json('access_token');
        } catch (\Throwable $e) {
            Log::error('FranceTravail token error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function toJobOffer(array $job): ?JobOffer
    {
        try {
            $location = $job['lieuTravail']['libelle'] ?? null;
            $typeContrat = $job['typeContrat'] ?? '';

            $sourceUrl = isset($job['origineOffre']['urlOrigine'])
                ? $job['origineOffre']['urlOrigine']
                : sprintf(self::OFFER_URL, $job['id']);

            $tags = array_map(
                fn (array $c) => strtolower($c['libelle']),
                $job['competences'] ?? []
            );

            return JobOffer::create(
                title: $job['intitule'],
                company: $job['entreprise']['nom'] ?? 'Non renseigné',
                location: $location,
                isRemote: false,
                type: self::JOB_TYPE_MAP[$typeContrat] ?? JobType::Other,
                salaryMin: null,
                salaryMax: null,
                salaryCurrency: null,
                description: $job['description'] ?? null,
                tags: $tags,
                source: JobSource::FranceTravail,
                sourceUrl: $sourceUrl,
                sourceId: $job['id'],
                publishedAt: new \DateTimeImmutable($job['dateCreation']),
                latitude: isset($job['lieuTravail']['latitude']) ? (float) $job['lieuTravail']['latitude'] : null,
                longitude: isset($job['lieuTravail']['longitude']) ? (float) $job['lieuTravail']['longitude'] : null,
            );
        } catch (\Throwable $e) {
            Log::warning('FranceTravail: failed to map job offer', ['job' => $job, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
