<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\JobOffer\UseCases\SearchJobOffersUseCase;
use App\Infrastructure\Services\BrandfetchService;
use App\Infrastructure\Services\CompanyLogoEnricher;
use App\Infrastructure\Sources\AdzunaLiveSearchAdapter;
use Illuminate\Support\ServiceProvider;

final class JobOfferServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AdzunaLiveSearchAdapter::class, fn () => new AdzunaLiveSearchAdapter(
            appId: config('services.adzuna.app_id'),
            appKey: config('services.adzuna.app_key'),
            country: 'fr',
        ));

        $this->app->bind(BrandfetchService::class, fn () => new BrandfetchService(
            apiKey: config('services.brandfetch.api_key'),
        ));

        $this->app->bind(CompanyLogoEnricher::class, fn () => new CompanyLogoEnricher(
            brandfetch: $this->app->make(BrandfetchService::class),
        ));

        $this->app->bind(SearchJobOffersUseCase::class, fn () => new SearchJobOffersUseCase(
            liveSearch: $this->app->make(AdzunaLiveSearchAdapter::class),
            logoEnricher: $this->app->make(CompanyLogoEnricher::class),
        ));
    }
}
