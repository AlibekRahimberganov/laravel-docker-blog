# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Laravel 12 blog application (PHP 8.2) with Blade templates, Tailwind CSS 4 (via Vite), and Docker (Nginx + PHP-FPM + MySQL) deployment. Standard Laravel MVC — no API layer, no SPA framework.

Database: local/non-Docker dev defaults to SQLite (`database/database.sqlite`); Docker Compose runs MySQL 8 (`db` service) and `.env.example`/`.env` are configured for `DB_CONNECTION=mysql`. Tests always run against in-memory SQLite regardless of `.env` (see `phpunit.xml`). The `default-mysql-client` and `pdo_mysql` were added to the `Dockerfile` for this migration.

## Commands

### Local (no Docker)
```bash
composer install
npm install
touch database/database.sqlite   # if not present
php artisan migrate --seed
npm run dev                      # Vite dev server
php artisan serve                # separately, or use `composer run dev` to launch both plus queue + log tail together
```

### Docker
```bash
cp .env.example .env
docker compose up -d
```
App is served at `http://localhost:9001` (Nginx proxies to PHP-FPM; see `default.conf` and `compose.yaml`). The `app` container waits on the `db` (MySQL 8) service's healthcheck before starting.

### Admin access
```bash
php artisan user:promote {email}   # promotes an existing user to the admin role
```
`DatabaseSeeder` also seeds `admin@example.com` (login `admin`) as an admin.

### Tests
```bash
php artisan test                 # equivalent to `composer test`; clears config cache first
php artisan test --filter=testName
php artisan test tests/Feature/FullApplicationTest.php
```
Tests run against an in-memory SQLite DB with the `testing` environment config baked into `phpunit.xml` (array session/cache, sync queue, `BCRYPT_ROUNDS=4`).

### Linting
```bash
vendor/bin/pint          # Laravel Pint, project's code style tool
```

## Architecture

### Request flow
Routes are defined only in `routes/web.php` (no `api.php` in use). Middleware groups that matter:
- `auth` — post CRUD, reactions, profile, stats (`routes/web.php:18-29`)
- `auth` + `admin` — everything under the `admin.*` route group (`routes/web.php:37-48`)
- `guest` — login/register, each throttled `throttle:5,1`
- ungated — home, single post view, about/contact

`bootstrap/app.php` globally appends `App\Http\Middleware\SecurityHeaders` (sets `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy`) and registers the `admin` middleware alias to `App\Http\Middleware\EnsureUserIsAdmin`, which 403s any request where `$request->user()?->isAdmin()` is false. CSRF verification is the Laravel default (active on every stateful route) — Blade forms need `@csrf`, and JS requests must go through `window.axios` (configured in `resources/js/bootstrap.js`) so the `XSRF-TOKEN` cookie is attached automatically; raw `fetch()` calls will get a 419.

### Controllers (`app/Http/Controllers`)
- `PageController` — renders views (home feed, post detail, profile, static pages) and records `PostView` rows (one per session per post, deduped via a `viewed_post_{id}` session key, not a DB unique constraint).
- `BlogController` — post create/edit/update/delete. Authorization is manual (`Auth::user()->id !== $post->user_id` checks per method), not via Policies/Gates.
- `UserController` — register/login/logout. Registration validates a separate `login` (username) field distinct from `email`; `Auth::attempt` authenticates on `email`+`password` only.
- `ReactionController` — toggles `like`/`dislike`/`recommend` reactions per post; like and dislike are mutually exclusive (toggling one clears the other), recommend is independent.
- `StatsController` — per-user post analytics (views/likes/dislikes/recommends via `withCount`) and CSV export (`text/csv` streamed response).

### Admin panel (`app/Http/Controllers/Admin`, views under `resources/views/admin`)
Gated by the `admin` middleware alias, not Policies/Gates. Self-modification is blocked in code (a user can't change their own role or delete their own account), not by the middleware.
- `DashboardController` — aggregate counts (users/posts/categories/views/likes/dislikes/recommends).
- `UserController` — list users (`withCount('posts')`), change role (`user`/`admin`), delete user.
- `PostController` — list all posts, delete any post (no ownership check — admin bypasses `BlogController`'s owner checks entirely).
- `CategoryController` — full CRUD; `destroy` refuses to delete a category that still has posts assigned.

### Models (`app/Models`)
- `Posts` overrides Eloquent's timestamp constants: `CREATED_AT` stays default, `UPDATED_AT` is remapped to `edited_at` (not `updated_at`) — relevant whenever you touch post timestamps or ordering.
- `Posts` exposes computed accessors (`views_count`, `likes_count`, `dislikes_count`, `recommends_count`, `hasReaction()`) built on the `views()`/`reactions()` relations — prefer these over raw queries when displaying counts.
- `Category` 1:many `Posts`; `Reaction` and `PostView` both belong to `User` + `Posts`.
- `User` has a `role` column (`user`|`admin` enum, default `user`, added by the `add_role_to_users_table` migration) and an `isAdmin()` helper used by the `admin` middleware.

### File uploads
`content_media` on posts accepts `jpg,jpeg,png,mp4,mp3`, stored via `store('media', 'public')` (i.e. `storage/app/public/media`, requires `php artisan storage:link`). Note `store` (create) caps size at 2097152 KB while `update` caps at 10240 KB — this asymmetry is pre-existing, not intentional design.

### Seeders
`DatabaseSeeder` creates an admin user (`admin@example.com`), 10 regular users, then calls `CategorySeeder` and `RealPostsSeeder` (`PostsSeeder` exists in the same directory but is currently unused/superseded).
