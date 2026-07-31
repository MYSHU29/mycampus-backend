# AGENTS.md

## Project Overview

Laravel 13 MyCampus (Academic Information System) on Laragon (Windows). PHP 8.3+, Vite + Tailwind CSS v4. Indonesian-language UI using SB Admin 2 Bootstrap template.

## Commands

- `composer setup` — full project setup (install, .env, key, migrate, npm install, build)
- `composer dev` — start dev (artisan serve + queue + pail + vite concurrently)
- `composer test` — clears config cache, then runs `php artisan test`
- `php artisan test --filter=TestName` — run a single test class
- `php artisan test --filter=TestName::testMethod` — run a single test
- `npx vite build` — production frontend build

There is **no linter or formatter script** configured. Laravel Pint is a dev dependency but has no project-level config — it uses defaults if run manually (`vendor/bin/pint`).

## Database

- **Production/dev**: MySQL (`PROJECT1` database, root@localhost:3306, no password) — configured in `.env`
- **Testing**: SQLite in-memory (configured in `phpunit.xml`, overrides `.env`)
- Default in `.env.example` is SQLite; the actual `.env` uses MySQL
- SQLite config in `config/database.php` is hardcoded to `database/database.sqlite` path (ignores env)
- Run `php artisan migrate` after schema changes. For fresh start: `php artisan migrate:fresh --seed`

## Architecture

### Custom Role/Permission System

No package — entirely custom, stored in `roles`, `permissions`, `role_permissions` tables.

- `Role` model: `nama_role` field (e.g. `admin`, `operator`, `mahasiswa`)
- `Permission` model: `nama_permission` field uses `<module>.<action>` format (e.g. `mahasiswa.view`, `prestasi.create`, `prestasi.verify`)
- **`admin` role bypasses all permission checks** — hardcoded in `User::hasPermission()` at `app/Models/User.php:50`
- Middleware aliases registered in `bootstrap/app.php`: `role` → `RoleMiddleware`, `permission` → `PermissionMiddleware`
- Permission middleware accepts variadic args; `middleware('permission:foo,bar')` = user needs **any** of those permissions
- Role middleware: `middleware('role:operator')` = user must have that exact role name

### Route Structure

All routes in `routes/web.php`. No API routes.

- `/` — login redirect or dashboard
- `/dashboard` — main dashboard
- `/data-mahasiswa`, `/pembayaran-spp`, etc. — feature pages (permission-gated)
- `/operator/*` — operator section, gated by `role:operator` middleware
- `/operator/users` — user CRUD
- `/operator/activity-logs` — activity log viewer

### Key Models

All in `app/Models/`: `User`, `Role`, `Permission`, `Mahasiswa`, `PembayaranSpp`, `PengambilanMatakuliah`, `PeminjamanBuku`, `PrestasiMahasiswa`, `JenisPrestasi`, `TingkatPrestasi`, `AdminPrestasi`, `VerifikasiPrestasi`, `ActivityLog`

### Activity Logging

- `app/Traits/LogsActivity.php` — add to any model for auto-logging create/update/delete
- `ActivityLog::log($type, $module, $description, $old, $new)` for manual logging
- Operator section provides UI for viewing/filtering/exporting logs

## Dev Scripts

- `start-mycampus.bat` — double-click to start full dev + ngrok tunnel:
  - Runs `composer dev` (artisan serve + queue + pail + vite)
  - Runs ngrok tunnel to port 8000
  - Opens browser to `localhost:8000`
  - Access `http://127.0.0.1:4040` to see the public ngrok URL
  - Close the window to kill all processes

## Conventions

- All user-facing text is in **Bahasa Indonesia** — controllers, views, error messages, permission names
- Route names use dot notation: `prestasi-mahasiswa.index`, `operator.users.create`
- Views are Blade templates in `resources/views/`, organized by feature
- No form request classes — validation is inline in controllers
- Eloquent relationships defined directly on models (no concerns/traits beyond LogsActivity)
- Foreign keys cascade on delete (enforced in migration `2026_06_09_030930`)
- `.npmrc` has `ignore-scripts=true` — npm lifecycle scripts are disabled

## Gotchas

- The `permission` middleware variadic means `permission:a,b` is OR logic, not AND. A user with permission `a` passes even without `b`.
- `composer dev` requires `npx concurrently` globally or locally — it runs 4 processes simultaneously
- Tests use SQLite in-memory regardless of `.env` DB config — no MySQL needed for test suite
- There is a stray `db_prestasi.db` file at project root — not used by the app
- `app/Mahasiswa.php` and `app/user.php` exist outside `app/Models/` — appear to be unused/legacy files
