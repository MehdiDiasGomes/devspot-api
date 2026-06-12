<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\JobOffer\UseCases\FetchJobOffersUseCase;
use Illuminate\Console\Command;

final class FetchJobOffersCommand extends Command
{
    protected $signature = 'jobs:fetch';

    protected $description = 'Fetch job offers from all configured sources and persist new ones';

    public function handle(FetchJobOffersUseCase $fetchJobOffersUseCase): int
    {
        $this->info('Fetching job offers from all sources...');

        $count = $fetchJobOffersUseCase->execute();

        $this->info("Done. {$count} new offer(s) saved.");

        return self::SUCCESS;
    }
}
