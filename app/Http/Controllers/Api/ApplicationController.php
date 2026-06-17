<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\JobApplication\UseCases\CreateJobApplicationUseCase;
use App\Application\JobApplication\UseCases\DeleteJobApplicationUseCase;
use App\Application\JobApplication\UseCases\ListJobApplicationsUseCase;
use App\Application\JobApplication\UseCases\UpdateJobApplicationUseCase;
use App\Domain\JobApplication\ValueObjects\ApplicationData;
use App\Domain\JobApplication\ValueObjects\ApplicationSource;
use App\Domain\JobApplication\ValueObjects\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Http\Resources\ApplicationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ApplicationController extends Controller
{
    public function __construct(
        private readonly ListJobApplicationsUseCase   $list,
        private readonly CreateJobApplicationUseCase  $create,
        private readonly UpdateJobApplicationUseCase  $update,
        private readonly DeleteJobApplicationUseCase  $delete,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $applications = $this->list->execute($request->user()->id);

        return response()->json(['data' => ApplicationResource::collection($applications)]);
    }

    public function store(StoreApplicationRequest $request): JsonResponse
    {
        $application = $this->create->execute(
            $request->user()->id,
            $this->toData($request->validated()),
        );

        return response()->json(['data' => new ApplicationResource($application)], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $applications = $this->list->execute($request->user()->id);

        $application = collect($applications)->first(fn ($a) => $a->id === $id);

        abort_unless($application !== null, 404);

        return response()->json(['data' => new ApplicationResource($application)]);
    }

    public function update(UpdateApplicationRequest $request, int $id): JsonResponse
    {
        $application = $this->update->execute(
            $id,
            $request->user()->id,
            $this->toData($request->validated()),
        );

        return response()->json(['data' => new ApplicationResource($application)]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->delete->execute($id, $request->user()->id);

        return response()->json(null, 204);
    }

    /** @param array<string, mixed> $validated */
    private function toData(array $validated): ApplicationData
    {
        return new ApplicationData(
            company:   $validated['company'],
            position:  $validated['position'],
            status:    ApplicationStatus::from($validated['status']),
            source:    ApplicationSource::from($validated['source']),
            appliedAt: isset($validated['applied_at'])
                           ? new \DateTimeImmutable($validated['applied_at'])
                           : null,
            location:  $validated['location'] ?? null,
            salary:    $validated['salary'] ?? null,
            tags:      $validated['tags'] ?? [],
            isRemote:  $validated['is_remote'] ?? false,
            offerUrl:  $validated['offer_url'] ?? null,
            notes:     $validated['notes'] ?? null,
            message:   $validated['message'] ?? null,
        );
    }
}
