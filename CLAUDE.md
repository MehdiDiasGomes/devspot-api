# DevSpot API — CLAUDE.md

## Project Overview

**DevSpot** is a job aggregator platform targeting fullstack developers.
It fetches job offers from multiple third-party platforms, deduplicates them, stores them, and exposes them via a REST API consumed by the Next.js frontend (`devspot-web`).

## Architecture

- **Type**: REST API (Laravel, API-only)
- **Frontend**: separate repo `devspot-web` (Next.js + TailwindCSS + tailwindcss-animated)
- **Deployment**: independent from the frontend

## Tech Stack

| Layer | Tech |
|---|---|
| Language | PHP 8.4 |
| Framework | Laravel 12 |
| Database | PostgreSQL 17 |
| Cache | Redis |
| Queue | Redis |
| Session | Redis |
| Containers | Docker Compose (OrbStack) |

## Job Sources (MVP)

| Platform | Type | API |
|---|---|---|
| Remotive | Remote jobs | Free API |
| Arbeitnow | EU tech jobs | Free API |
| Adzuna | EU generalist | Free API |

## Domain Logic

- **Fetching**: CRON scheduler hits each source API and stores raw results
- **Deduplication**: hash on `title + company + location` to prevent duplicates across sources
- **Normalization**: all offers stored in a unified `job_offers` schema regardless of source
- **Redirection**: no proxy — frontend links directly to the original offer URL

## Git Workflow

- `main` — production-ready, protected
- `dev` — integration branch, all features merged here first
- Feature branches: `{type}/#{issue-number}-{name}` (e.g. `feat/#12-job-offer-model`)
- PRs always target `dev`, never `main` directly

## Docker

```bash
# Start Postgres only
docker compose up -d

# Start Postgres + Adminer (http://localhost:8080)
docker compose --profile tools up -d
```

**Adminer credentials:**
- System: PostgreSQL
- Server: `postgres`
- Username: `devspot`
- Password: `secret`
- Database: `devspot`

## Key Commands

```bash
# Run migrations
php artisan migrate

# Run tests
composer test

# Start dev server
composer dev
```

## Code Conventions

- English only: variables, functions, types, comments
- Clean Code + DRY: single responsibility, no duplication
- TypeScript strict equivalent in PHP: explicit types, no mixed returns
- PHPDoc only on public methods (concise, English)
- Comments only for non-obvious business logic
