<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\JobOffer\UseCases\SearchJobOffersUseCase;
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

        $this->app->bind(SearchJobOffersUseCase::class, fn () => new SearchJobOffersUseCase(
            liveSearch: $this->app->make(AdzunaLiveSearchAdapter::class),
        ));
    }
}
