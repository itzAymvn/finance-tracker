# Salary & Finance Tracker

A self-hosted personal finance app built with Laravel 11, Inertia.js, and React 19.
Track monthly salary payouts, a signed-amount transaction ledger, recurring subscriptions,
and category spending — with FIFO salary allocation, XLSX bank-statement import, and JSON
backup/restore out of the box.

> Default currency: **MAD** (Moroccan Dirham). Designed for single-user use, auth-protected.

---

## Features

- **Dashboard** — salary status at a glance, cumulative rollover, income/expense chart, and category breakdown pie.
- **Salary Months** — one row per month with `expected_salary`; tracks `paid` / `partial` / `unpaid` / `overpaid` status with FIFO rollover.
- **Transactions** — signed-amount ledger (credit `+`, debit `−`), filterable by search, year, month, type, and category.
- **FIFO Salary Allocation** — salary credits are split across eligible months; each month is capped at `expected_salary` and surplus rolls forward.
- **Categories** — transaction categorization with a single designated "salary" category (enforced via partial unique index).
- **Subscriptions** — recurring-transaction generator (weekly / biweekly / monthly / quarterly / yearly) with pause / resume / cancel and a monthly-cost projection.
- **Bank Import** — parse `.xlsx` bank exports; salary is auto-detected from sender-name patterns.
- **Backup & Restore** — full JSON database dumps, manual or scheduled (6 / 12 / 24 / 168h), with upload-and-restore.
- **Auth** — Laravel Breeze scaffolding (login, register, password reset, email verification, profile, account deletion).

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+ · Laravel 11 |
| Frontend | React 19 · TypeScript · Vite 5 · Tailwind v4 · shadcn/ui |
| Bridge | Inertia.js (no separate API) |
| Database | SQLite (default, file-based) |
| Notable libs | PhpSpreadsheet, Recharts, @tanstack/react-table, react-hook-form, zod |
| Deploy | Docker (Nginx + PHP-FPM + scheduler sidecar) |

---

## Prerequisites

- [Docker](https://www.docker.com/) (recommended path)
- _or_ locally: PHP 8.2+, Composer, Node 20+, npm

---

## Quick Start (Docker)

```bash
# 1. Copy the Docker env file and generate the app key
cp .env.docker .env
docker compose run --rm app php artisan key:generate

# 2. Build and start containers
docker compose up -d --build

# 3. Run migrations
docker compose exec app php artisan migrate

# 4. (Optional) Seed sample data
docker compose exec app php artisan db:seed

# 5. Create the storage symlink
docker compose exec app php artisan storage:link
```

Then visit **http://localhost** (served by Nginx on port 80).

### Services started

| Service | Role |
|---|---|
| `app` | PHP-FPM (Laravel) |
| `web` | Nginx (port 80) |
| `scheduler` | Long-running `php artisan schedule:work` |
| `node` *(opt-in profile)* | Asset builder — `docker compose --profile assets run --rm node sh -c "npm ci && npm run build"` |

---

## Native Development

```bash
# Install dependencies
composer install            # auto-copies .env.example → .env if missing
npm ci

php artisan key:generate    # if not already set
php artisan migrate

# Run dev servers (run in separate terminals)
php artisan serve           # Laravel at http://localhost:8000
npm run dev                 # Vite HMR for frontend assets
```

---

## Build, Lint & Test

```bash
# Frontend build
npm run build               # outputs to public/build/

# Lint
npm run lint                # ESLint on resources/js
npm run lint:fix
vendor/bin/pint             # PHP formatter (fix)
vendor/bin/pint --test      # PHP formatter (check only)

# Tests (PHPUnit)
vendor/bin/phpunit
# or
php artisan test
```

Inside Docker, prefix artisan/vendor commands with `docker compose exec app`.

---

## Artisan Commands

| Command | Purpose |
|---|---|
| `php artisan transactions:import {file}` | Import transactions from an `.xlsx` bank export |
| `php artisan transactions:retag` | Re-evaluate salary tagging on all credits |
| `php artisan subscriptions:generate` | Generate due subscription transactions |
| `php artisan backup:auto` | Run conditional auto-backup |
| `php artisan db:export` | Raw database export |
| `php artisan db:import` | Raw database import |

### Scheduled jobs

- `backup:auto` — hourly
- `subscriptions:generate` — every 5 minutes

---

## Environment Variables

Only `APP_KEY` is strictly required — everything else has sensible defaults.

```bash
cp .env.docker .env     # Docker
# or
cp .env.example .env    # Native
php artisan key:generate
```

| Variable | Default | Notes |
|---|---|---|
| `APP_NAME` | `Salary Tracker` | |
| `APP_ENV` | `local` (`production` in `.env.docker`) | Drives composer install flags in entrypoint |
| `APP_KEY` | — | **Required** |
| `APP_DEBUG` | `true` (`false` in `.env.docker`) | |
| `APP_URL` | `http://localhost` | |
| `DB_CONNECTION` | `sqlite` | |
| `DB_DATABASE` | `database/database.sqlite` | Absolute path inside Docker |
| `CACHE_STORE` | `database` (`file` in Docker) | |
| `SESSION_DRIVER` | `database` (`file` in Docker) | |
| `QUEUE_CONNECTION` | `database` (`sync` in Docker) | |
| `FILESYSTEM_DISK` | `local` | |
| `VITE_APP_NAME` | `${APP_NAME}` | Exposed to frontend |

Optional seed user (consumed by `Database\Seeders\UserSeeder`):

```
SEED_USER_EMAIL=...
SEED_USER_PASSWORD=...
SEED_USER_NAME=...
```

---

## How FIFO Salary Allocation Works

When a salary transaction is recorded, the `AllocationService` distributes it across months:

1. Salary months are fetched in ascending `month_key` order (oldest first).
2. For each month with a positive remaining balance (`expected_salary − already_paid`),
   the service allocates as much of the credit as needed to close that month.
3. Once a month is covered, the remainder rolls forward to the next month.
4. Any leftover after all months are satisfied stays unallocated.

**Example:** October remaining = 1500, salary credit = 2000 → 1500 closes October, 500 rolls into November.

---

## Architecture

Layered Laravel + Inertia SPA monolith:

```
routes/            → HTTP entry points (web.php, auth.php, console.php)
app/Http/Controllers/   → Thin controllers, render Inertia pages
app/Http/Requests/      → Form requests (validation + authorization)
app/Services/           → Business logic (AllocationService, SubscriptionService)
app/Models/             → Eloquent models
resources/js/Pages/     → React pages (1:1 with controller views)
```

### Data model

| Table | Purpose |
|---|---|
| `users` | Standard Laravel auth |
| `salary_months` | One row per calendar month with `expected_salary` |
| `transactions` | Signed-amount ledger; dedup index on `(paid_at, amount, label)` |
| `salary_allocations` | Pivot linking salary transactions to months (split allocations) |
| `categories` | Transaction categories; single `is_salary` flag enforced |
| `subscriptions` | Recurring transaction templates |
| `settings` | Key-value store (e.g. backup config) |

Computed values (total paid, remaining, status, progress %) are derived at runtime — nothing redundant is stored.

---

## Useful Docker Commands

```bash
docker compose ps                                  # list running containers
docker compose logs -f app                         # tail app logs
docker compose down                                # stop containers
docker compose down -v                             # stop + drop volumes (clears DB)
docker compose exec app bash                       # shell into app container
docker compose exec app php artisan migrate:fresh --seed   # fresh DB + seed
```

---

## License

Personal project — see repository for details.
