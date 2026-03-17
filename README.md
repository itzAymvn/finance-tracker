# Salary Payout Tracker

A simple Laravel 11 app for tracking monthly salary payouts with rollover allocation between months. Supports file attachments as payout proof.

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running

---

## Quick Start

### 1. Copy environment file

```bash
cp .env.docker .env
```

Then generate an application key:

```bash
docker compose run --rm app php artisan key:generate
```

Or manually set `APP_KEY` in `.env` using:

```bash
php artisan key:generate --show
```

### 2. Build and start containers

```bash
docker compose up -d --build
```

This starts three services:
- `app` — PHP 8.3-FPM (Laravel)
- `web` — Nginx (port 8080)
- `db` — MySQL 8.0

### 3. Run migrations

```bash
docker compose exec app php artisan migrate
```

### 4. Seed sample data (optional)

```bash
docker compose exec app php artisan db:seed
```

This creates 3 salary months (Oct/Nov/Dec 2025 @ 4000 MAD each) and 5 payouts with allocations matching the scenario.

### 5. Create the storage symlink

```bash
docker compose exec app php artisan storage:link
```

### 6. Open the app

Visit [http://localhost:8080](http://localhost:8080)

---

## Useful Docker Commands

```bash
# View running containers
docker compose ps

# View app logs
docker compose logs app

# Stop all containers
docker compose down

# Stop and remove volumes (clears database)
docker compose down -v

# Run artisan commands
docker compose exec app php artisan <command>

# Open a shell in the app container
docker compose exec app bash

# Fresh migrate + seed
docker compose exec app php artisan migrate:fresh --seed
```

---

## How Attachments Work

- Attachment files are uploaded and stored in `storage/app/attachments/` (inside the container, persisted via bind mount).
- Three metadata columns are stored on the `payouts` table: `attachment_path`, `attachment_name`, `attachment_mime`.
- A separate Attachments model was intentionally avoided — each payout has at most one proof file, so inline columns keep it simple.
- Allowed types: `jpg`, `jpeg`, `png`, `pdf`, `doc`, `docx`. Max size: 5 MB.
- Access a file via the download route: `GET /payouts/{id}/attachment`
- The `storage:link` command creates a symlink so publicly served files work via `public/storage`. However, attachments are served through the `AttachmentController` (not directly) to keep them private.

---

## How Auto-Allocation Works

When a payout is saved with **allocation mode = auto**:

1. All salary months are fetched in ascending `month_key` order (oldest first).
2. For each month with a positive remaining balance (`expected_salary − already_paid`), the service allocates as much of the payout as needed to bring that month to zero.
3. If the payout covers a month fully, it moves to the next month.
4. Any leftover amount after all months are covered remains unallocated (shown on the payout detail page).

**Example:**
- October remaining = 1500, payout = 2000
- → 1500 goes to October (closes it)
- → 500 rolls over to November

Manual allocation bypasses this logic entirely — you choose which months and how much.

---

## Architecture Notes

### Data model

| Table | Purpose |
|---|---|
| `salary_months` | One row per calendar month with an expected salary |
| `payouts` | One row per payment received |
| `payout_allocations` | Junction: links each payout to one or more months with an amount |

All computed values (total paid, remaining, status, progress %) are derived at runtime from `payout_allocations` — nothing redundant is stored.

### Key classes

- `AllocationService` — all allocation logic lives here, controllers stay thin
- `SalaryMonth` / `Payout` — Eloquent accessors expose computed properties (`total_paid`, `remaining`, `status`, `progress_percent`)
- Form Requests validate and authorize all input before it reaches a controller

### Seeder scenario result

| Month | Expected | Paid | Status |
|---|---|---|---|
| October 2025 | 4000 | 4000 | Paid |
| November 2025 | 4000 | 4000 | Paid |
| December 2025 | 4000 | 500 | Partial |
