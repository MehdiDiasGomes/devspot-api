<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\JobOffer\UseCases\FetchJobOffersUseCase;
use App\Application\JobOffer\UseCases\ListJobOffersUseCase;
use App\Domain\JobOffer\Ports\JobOfferRepositoryPort;
use App\Infrastructure\Persistence\Repositories\EloquentJobOfferRepository;
use App\Infrastructure\Services\BrandfetchService;
use App\Infrastructure\Sources\AdzunaAdapter;
use App\Infrastructure\Sources\FranceTravailAdapter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

final class JobOfferServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(JobOfferRepositoryPort::class, EloquentJobOfferRepository::class);

        $this->app->bind(BrandfetchService::class, fn () => new BrandfetchService(
            apiKey: config('services.brandfetch.api_key'),
        ));

        $this->app->bind(FetchJobOffersUseCase::class, function (Application $app): FetchJobOffersUseCase {
            $appId = config('services.adzuna.app_id');
            $appKey = config('services.adzuna.app_key');

            return new FetchJobOffersUseCase(
                sources: [
                    new AdzunaAdapter(appId: $appId, appKey: $appKey, country: 'fr'),
                    new FranceTravailAdapter(
                        clientId: config('services.france_travail.client_id'),
                        clientSecret: config('services.france_travail.client_secret'),
                    ),
                ],
                repository: $app->make(JobOfferRepositoryPort::class),
            );
        });

        $this->app->bind(ListJobOffersUseCase::class, function (Application $app): ListJobOffersUseCase {
            return new ListJobOffersUseCase(
                repository: $app->make(JobOfferRepositoryPort::class),
            );
        });
    }
}
