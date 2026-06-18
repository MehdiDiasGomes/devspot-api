<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\JobOffer\ValueObjects\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class JobOfferCollection extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var Page $page */
        $page = $this->resource;

        return [
            'data' => JobOfferResource::collection($page->items),
            'meta' => [
                'total'        => $page->total,
                'per_page'     => $page->perPage,
                'current_page' => $page->currentPage,
                'last_page'    => $page->lastPage,
            ],
        ];
    }
}
