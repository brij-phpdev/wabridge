# WA Bridge — Foundation Stage: Setup & Deploy

I can't run `composer`/`npm` in my own sandbox (no Packagist access there), so this is scaffolded via Laravel's own tooling by you, with the WA Bridge-specific files in this bundle dropped on top. Run these where you have real internet access — locally, or directly over SSH on Hostinger (both work since SSH/Composer are confirmed available there).

## 1. Generate the base application

```bash
composer create-project laravel/laravel wabridge "^11.0"
cd wabridge
composer require laravel/breeze --dev
php artisan breeze:install react --typescript
npm install
```

This gives you: Laravel 11, Breeze auth scaffolding, Inertia + React + TypeScript + Tailwind — all four items 1–4 of the First Objective list, via the standard, well-tested path rather than hand-typed framework boilerplate.

## 2. Drop in the WA Bridge foundation files (this bundle)

Copy these into the generated project, preserving paths:

```
database/migrations/2026_01_01_000001_create_organizations_table.php
database/migrations/2026_01_01_000002_add_organization_and_role_to_users_table.php
database/migrations/2026_01_01_000003_create_audit_logs_table.php
app/Models/Organization.php
app/Models/User.php                          ← overwrites Breeze's default User model
app/Models/AuditLog.php
app/Models/Concerns/BelongsToOrganization.php
app/Enums/UserRole.php
app/Http/Middleware/EnsurePlatformAdmin.php
app/Http/Controllers/Admin/DashboardController.php
app/Console/Commands/GenerateScheduledExecutions.php
routes/console.php                            ← overwrites Breeze's default console.php
resources/js/Pages/Admin/Dashboard.tsx
```

`routes/web-additions.php` is **not** a file to copy as-is — open it and merge its route group into the `routes/web.php` Breeze already generated (don't overwrite Breeze's own dashboard/profile routes).

Merge `env-additions.txt` into the generated `.env.example` (and your local `.env`) — replace values, don't just append duplicates of keys Breeze already wrote (e.g. `APP_NAME`, `DB_CONNECTION`, `QUEUE_CONNECTION` already exist; edit those lines rather than adding new ones).

## 3. Publish the queue tables (Laravel 11 doesn't include these by default)

```bash
php artisan queue:table
php artisan queue:failed-table
```

## 4. Register the middleware alias

In `bootstrap/app.php`, inside the `->withMiddleware()` closure, add:

```php
$middleware->alias([
    'platform_admin' => \App\Http\Middleware\EnsurePlatformAdmin::class,
]);
```

(The route group in `web-additions.php` references the class directly, so this alias is optional convenience for later routes — but register it now while you're in this file.)

## 5. Local verification, before touching production

```bash
php artisan migrate          # against your LOCAL .env database only
npm run build
php artisan serve
```

Confirm: registration/login works (Breeze), visiting `/admin` as a non-`platform_admin` user 403s, and `php artisan automation:generate-executions` prints its stub message.

## 6. Git

```bash
git init
git add .
git commit -m "Initialize WA Bridge Laravel SaaS foundation"
git remote add origin https://github.com/brij-phpdev/wabridge.git
git push -u origin main
```

I have no credentials to do this step myself — this has to run from your machine or your existing CI/CD path.

## 7. Hostinger production deployment

Given your existing Git-based deploy workflow (same one Bedrock/WordPress already uses):

1. Pull/deploy the repo into `public_html/wabridge` as usual.
2. SSH in, `cd public_html/wabridge`, run `composer install --no-dev --optimize-autoloader`.
3. Build frontend assets **before** or **during** deploy — either `npm run build` over SSH if Node is available there, or build locally and upload the resulting `public/build/` directory (safer if Hostinger's Node version is uncertain).
4. Create `.env` directly on the server (never from git) with: `APP_NAME`, `APP_URL=https://wabridge.brijraj.tech`, `APP_KEY` (generate via `php artisan key:generate` — do this on the server, don't reuse a local key), the DB credentials for the database you already created, `QUEUE_CONNECTION=database`, mail settings once decided.
5. **Document root**: in hPanel, set `wabridge.brijraj.tech`'s document root to `public_html/wabridge/public` — verify this explicitly before step 9; this is the step most likely to be silently wrong.
6. `php artisan migrate --force` (safe here: it's only creating WA Bridge's own new tables in the database you already created — nothing pre-existing to collide with).
7. `php artisan storage:link`
8. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
9. Cron — **two separate entries**, kept independent so a slow/stuck queue batch can never delay the scheduler tick:

   **Cron 1 — Scheduler** (dispatches `automation:generate-executions` per `routes/console.php`):
   ```
   * * * * * cd /home/<hostinger-user>/public_html/wabridge && php artisan schedule:run >> /dev/null 2>&1
   ```

   **Cron 2 — Queue worker** (bounded, self-terminating — no Supervisor needed):
   ```
   * * * * * cd /home/<hostinger-user>/public_html/wabridge && php artisan queue:work database --stop-when-empty --max-time=55 --tries=3 >> /dev/null 2>&1
   ```

   These are independent responsibilities, not one fanning out into the other — the scheduler never invokes the worker, and the worker never depends on a web request or on `schedule:run`.

10. Verify each independently: check `storage/logs/laravel.log` for the `automation:generate-executions` stub message appearing once a minute (confirms Cron 1 → scheduler → command), then separately dispatch a trivial test job and confirm it's picked up within a minute (confirms Cron 2 → worker → job, on its own). Also confirm `https://wabridge.brijraj.tech` loads over HTTPS, a test registration works, and `/admin` 403s for a non-`platform_admin` account.

## Genuine blocker for this stage

None. Everything in the First Objective list (1–13) is covered by the base scaffold + these files, and none of it depends on Meta credentials, payment gateway decisions, or Redis/Supervisor — consistent with keeping this commit to foundation only.
