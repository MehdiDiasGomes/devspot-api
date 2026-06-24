<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\JobApplication\UseCases\CreateJobApplicationUseCase;
use App\Application\JobApplication\UseCases\DeleteJobApplicationUseCase;
use App\Application\JobApplication\UseCases\ListJobApplicationsUseCase;
use App\Application\JobApplication\UseCases\UpdateJobApplicationUseCase;
use App\Domain\JobApplication\Ports\JobApplicationRepositoryPort;
use App\Infrastructure\Persistence\Repositories\EloquentJobApplicationRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

final class JobApplicationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(JobApplicationRepositoryPort::class, EloquentJobApplicationRepository::class);

        $this->app->bind(ListJobApplicationsUseCase::class, function (Application $app): ListJobApplicationsUseCase {
            return new ListJobApplicationsUseCase($app->make(JobApplicationRepositoryPort::class));
        });

        $this->app->bind(CreateJobApplicationUseCase::class, function (Application $app): CreateJobApplicationUseCase {
            return new CreateJobApplicationUseCase($app->make(JobApplicationRepositoryPort::class));
        });

        $this->app->bind(UpdateJobApplicationUseCase::class, function (Application $app): UpdateJobApplicationUseCase {
            return new UpdateJobApplicationUseCase($app->make(JobApplicationRepositoryPort::class));
        });

        $this->app->bind(DeleteJobApplicationUseCase::class, function (Application $app): DeleteJobApplicationUseCase {
            return new DeleteJobApplicationUseCase($app->make(JobApplicationRepositoryPort::class));
        });
    }
}
