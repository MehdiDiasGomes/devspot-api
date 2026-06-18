<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Infrastructure\Persistence\Models\JobOfferModel;
use App\Infrastructure\Services\BrandfetchService;
use Illuminate\Console\Command;

final class ResolveCompanyLogosCommand extends Command
{
    protected $signature = 'companies:resolve-logos';

    protected $description = 'Resolve company logos via Brandfetch for job offers without a logo';

    public function handle(BrandfetchService $brandfetch): int
    {
        $companies = JobOfferModel::query()
            ->whereNotNull('company')
            ->where('company', '!=', '')
            ->whereNull('company_logo_url')
            ->distinct()
            ->pluck('company');

        if ($companies->isEmpty()) {
            $this->info('No companies to resolve.');
            return self::SUCCESS;
        }

        $this->info("Resolving logos for {$companies->count()} unique companies...");

        $resolved = 0;

        foreach ($companies as $company) {
            $logoUrl = $brandfetch->resolveLogoUrl($company);

            if ($logoUrl === null) {
                // API error — skip, will retry next run
                continue;
            }

            JobOfferModel::where('company', $company)
                ->whereNull('company_logo_url')
                ->update(['company_logo_url' => $logoUrl]);

            if ($logoUrl !== '') {
                $resolved++;
            }

            // Respect Brandfetch rate limits
            usleep(200_000); // 200ms between requests
        }

        $this->info("Done. {$resolved}/{$companies->count()} logo(s) resolved.");

        return self::SUCCESS;
    }
}
