<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\JobOffer\UseCases\ListJobOffersUseCase;
use App\Domain\JobOffer\ValueObjects\JobOfferFilters;
use App\Domain\JobOffer\ValueObjects\JobSource;
use App\Domain\JobOffer\ValueObjects\JobType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListJobOffersRequest;
use App\Http\Resources\JobOfferResource;
use Illuminate\Http\JsonResponse;

final class JobOfferController extends Controller
{
    public function __construct(
        private readonly ListJobOffersUseCase $listJobOffersUseCase,
    ) {}

    public function index(ListJobOffersRequest $request): JsonResponse
    {
        $filters = new JobOfferFilters(
            search: $request->input('search'),
            sources: array_map(fn (string $s) => JobSource::from($s), $request->array('sources')),
            type: $request->filled('type') ? JobType::from($request->string('type')->value()) : null,
            isRemote: $request->has('is_remote') ? $request->boolean('is_remote') : null,
            tags: $request->array('tags'),
        );

        $page = $this->listJobOffersUseCase->execute(
            filters: $filters,
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 20),
        );

        return response()->json([
            'data' => JobOfferResource::collection($page->items),
            'meta' => [
                'total' => $page->total,
                'per_page' => $page->perPage,
                'current_page' => $page->currentPage,
                'last_page' => $page->lastPage,
            ],
        ]);
    }
}
