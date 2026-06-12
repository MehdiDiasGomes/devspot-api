<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\JobOffer\Entities\JobOffer;
use App\Domain\JobOffer\Ports\JobOfferRepositoryPort;
use App\Domain\JobOffer\ValueObjects\JobOfferFilters;
use App\Domain\JobOffer\ValueObjects\JobSource;
use App\Domain\JobOffer\ValueObjects\JobType;
use App\Domain\JobOffer\ValueObjects\Page;
use App\Infrastructure\Persistence\Models\JobOfferModel;
use Illuminate\Support\Str;

final class EloquentJobOfferRepository implements JobOfferRepositoryPort
{
    public function saveMany(array $offers): void
    {
        $now = now()->format('Y-m-d H:i:s');

        $records = array_map(fn (JobOffer $offer) => [
            'id' => Str::uuid()->toString(),
            'title' => $offer->title,
            'company' => $offer->company,
            'location' => $offer->location,
            'is_remote' => $offer->isRemote,
            'type' => $offer->type->value,
            'salary_min' => $offer->salaryMin,
            'salary_max' => $offer->salaryMax,
            'salary_currency' => $offer->salaryCurrency,
            'description' => $offer->description,
            'tags' => json_encode($offer->tags),
            'source' => $offer->source->value,
            'source_url' => $offer->sourceUrl,
            'source_id' => $offer->sourceId,
            'deduplication_hash' => $offer->deduplicationHash,
            'published_at' => $offer->publishedAt->format('Y-m-d H:i:s'),
            'latitude' => $offer->latitude,
            'longitude' => $offer->longitude,
            'created_at' => $now,
            'updated_at' => $now,
        ], $offers);

        // insertOrIgnore handles race conditions on unique deduplication_hash
        JobOfferModel::insertOrIgnore($records);
    }

    public function findExistingHashes(array $hashes): array
    {
        return JobOfferModel::whereIn('deduplication_hash', $hashes)
            ->pluck('deduplication_hash')
            ->all();
    }

    public function findPaginated(JobOfferFilters $filters, int $page, int $perPage): Page
    {
        $query = JobOfferModel::query();

        if ($filters->search !== null) {
            $search = $filters->search;
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('company', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters->sources)) {
            $query->whereIn('source', array_map(fn (JobSource $s) => $s->value, $filters->sources));
        }

        if ($filters->type !== null) {
            $query->where('type', $filters->type->value);
        }

        if ($filters->isRemote !== null) {
            $query->where('is_remote', $filters->isRemote);
        }

        foreach ($filters->tags as $tag) {
            $query->whereJsonContains('tags', $tag);
        }

        // Haversine radius filter — only applied when lat, lng and radius are all provided
        if ($filters->latitude !== null && $filters->longitude !== null && $filters->radius !== null) {
            $lat = $filters->latitude;
            $lng = $filters->longitude;
            $radius = $filters->radius;

            $query->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->whereRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                    [$lat, $lng, $lat, $radius]
                );
        }

        $paginated = $query
            ->orderByDesc('published_at')
            ->paginate(perPage: $perPage, page: $page);

        return new Page(
            items: array_map(fn (JobOfferModel $model) => $this->toDomain($model), $paginated->items()),
            total: $paginated->total(),
            perPage: $paginated->perPage(),
            currentPage: $paginated->currentPage(),
        );
    }

    private function toDomain(JobOfferModel $model): JobOffer
    {
        return new JobOffer(
            title: $model->title,
            company: $model->company,
            location: $model->location,
            isRemote: $model->is_remote,
            type: JobType::from($model->type),
            salaryMin: $model->salary_min,
            salaryMax: $model->salary_max,
            salaryCurrency: $model->salary_currency,
            description: $model->description,
            tags: $model->tags ?? [],
            source: JobSource::from($model->source),
            sourceUrl: $model->source_url,
            sourceId: $model->source_id,
            deduplicationHash: $model->deduplication_hash,
            publishedAt: new \DateTimeImmutable($model->published_at->toDateTimeString()),
            latitude: $model->latitude,
            longitude: $model->longitude,
            id: $model->id,
        );
    }
}
