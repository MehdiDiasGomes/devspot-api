<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\JobApplication\Entities\JobApplication;
use App\Domain\JobApplication\Ports\JobApplicationRepositoryPort;
use App\Domain\JobApplication\ValueObjects\ApplicationData;
use App\Domain\JobApplication\ValueObjects\ApplicationSource;
use App\Domain\JobApplication\ValueObjects\ApplicationStatus;
use App\Infrastructure\Persistence\Models\JobApplicationModel;

final class EloquentJobApplicationRepository implements JobApplicationRepositoryPort
{
    public function findAllForUser(int $userId): array
    {
        return JobApplicationModel::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (JobApplicationModel $model) => $this->toDomain($model))
            ->all();
    }

    public function findByIdForUser(int $id, int $userId): ?JobApplication
    {
        $model = JobApplicationModel::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function create(int $userId, ApplicationData $data): JobApplication
    {
        $model = JobApplicationModel::create([
            'user_id'    => $userId,
            ...$this->toRecord($data),
        ]);

        return $this->toDomain($model);
    }

    public function update(int $id, int $userId, ApplicationData $data): JobApplication
    {
        $model = JobApplicationModel::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $model->update($this->toRecord($data));

        return $this->toDomain($model->fresh());
    }

    public function delete(int $id, int $userId): void
    {
        JobApplicationModel::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail()
            ->delete();
    }

    /** @return array<string, mixed> */
    private function toRecord(ApplicationData $data): array
    {
        return [
            'company'    => $data->company,
            'position'   => $data->position,
            'status'     => $data->status->value,
            'source'     => $data->source->value,
            'applied_at' => $data->appliedAt?->format('Y-m-d'),
            'location'   => $data->location,
            'salary'     => $data->salary,
            'tags'       => $data->tags,
            'is_remote'  => $data->isRemote,
            'offer_url'  => $data->offerUrl,
            'notes'      => $data->notes,
            'message'    => $data->message,
        ];
    }

    private function toDomain(JobApplicationModel $model): JobApplication
    {
        return new JobApplication(
            userId:          $model->user_id,
            company:         $model->company,
            position:        $model->position,
            status:          ApplicationStatus::from($model->status),
            source:          ApplicationSource::from($model->source),
            appliedAt:       $model->applied_at
                                 ? new \DateTimeImmutable($model->applied_at->toDateString())
                                 : null,
            location:        $model->location,
            salary:          $model->salary,
            tags:            $model->tags ?? [],
            isRemote:        $model->is_remote,
            offerUrl:        $model->offer_url,
            notes:           $model->notes,
            coverLetterPath: $model->cover_letter_path,
            message:         $model->message,
            id:              $model->id,
            createdAt:       new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt:       new \DateTimeImmutable($model->updated_at->toDateTimeString()),
        );
    }
}
