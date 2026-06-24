<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\JobApplication\Entities\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ApplicationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var JobApplication $app */
        $app = $this->resource;

        return [
            'id'                => $app->id,
            'company'           => $app->company,
            'position'          => $app->position,
            'status'            => $app->status->value,
            'source'            => $app->source->value,
            'applied_at'        => $app->appliedAt?->format('Y-m-d'),
            'location'          => $app->location,
            'salary'            => $app->salary,
            'tags'              => $app->tags,
            'is_remote'         => $app->isRemote,
            'offer_url'         => $app->offerUrl,
            'notes'             => $app->notes,
            'cover_letter_path' => $app->coverLetterPath,
            'message'           => $app->message,
            'created_at'        => $app->createdAt?->format('Y-m-d\TH:i:s\Z'),
            'updated_at'        => $app->updatedAt?->format('Y-m-d\TH:i:s\Z'),
        ];
    }
}
