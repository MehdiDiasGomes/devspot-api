<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\JobOffer\UseCases\SearchJobOffersUseCase;
use App\Domain\JobOffer\ValueObjects\JobOfferFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListJobOffersRequest;
use App\Http\Resources\JobOfferCollection;
use Illuminate\Http\JsonResponse;

final class JobOfferController extends Controller
{
    public function __construct(
        private readonly SearchJobOffersUseCase $searchJobOffersUseCase,
    ) {}

    public function index(ListJobOffersRequest $request): JsonResponse
    {
        $filters = new JobOfferFilters(
            search: $request->input('search'),
            location: $request->input('location'),
            radius: $request->filled('radius') ? $request->integer('radius') : null,
        );

        $page = $this->searchJobOffersUseCase->execute(
            filters: $filters,
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 20),
        );

        return (new JobOfferCollection($page))->response();
    }
}
