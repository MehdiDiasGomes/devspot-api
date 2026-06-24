<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\JobOffer\Entities\JobOffer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class JobOfferResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var JobOffer $offer */
        $offer = $this->resource;

        return [
            'id' => $offer->sourceId,
            'title' => $offer->title,
            'company' => $offer->company,
            'location' => $offer->location,
            'is_remote' => $offer->isRemote,
            'type' => $offer->type->value,
            'contract_label' => $offer->contractLabel,
            'salary_min' => $offer->salaryMin,
            'salary_max' => $offer->salaryMax,
            'salary_currency' => $offer->salaryCurrency,
            'description' => $offer->description,
            'tags' => $offer->tags,
            'source' => $offer->source->value,
            'source_url' => $offer->sourceUrl,
            'published_at' => $offer->publishedAt->format('Y-m-d'),
        ];
    }
}
