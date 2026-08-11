# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Zion Industries — a demo products/customers/orders app. Vue 3 SPA frontend, Laravel 13 API-only backend, and a separate Node/Express server running better-auth for email/password authentication. All three processes share one SQLite database file. See `README.md` for full architecture, data model (with ERD), access-rules table, order-status state machine, and the API reference — read it before making non-trivial changes, it's kept up to date.

## Commands

All backend commands run from `backend/`; frontend commands run from `frontend/`.

**Setup** (first time, three separate terminals needed to run everything):
```bash
# backend/ — Laravel
composer install
php artisan key:generate
php artisan migrate --seed

# backend/ — auth (needs BETTER_AUTH_SECRET + BETTER_AUTH_URL in backend/.env)
npm install
npx @better-auth/cli@latest migrate   # creates user/session/account/verification tables
npm run seed:admin                    # seeds zion.admin@zionindustries.com / ZionAdmin!

# frontend/
npm install
```

**Running in development** (needs all three running concurrently):
```bash
cd backend && php artisan serve   # :8000 — must be run separately
cd backend && npm run dev         # :3001 — auth server + Laravel's Vite asset build, via concurrently
cd frontend && npm run dev        # :5173 — Vite dev server, proxies /api/auth/* to :3001 and /api/* to :8000
```

**Tests / lint / build:**
```bash
cd backend && php artisan test                      # PHPUnit, all tests
cd backend && php artisan test --filter=TestName     # single test
cd backend && ./vendor/bin/pint                      # Laravel Pint (code style)
cd backend && npm run build                          # Vite asset build (Laravel-side assets)
cd frontend && npm run build                         # frontend production build
cd frontend && npm run preview                       # preview a frontend production build
```

There is no frontend test runner or linter configured — `frontend/package.json` only has `dev`/`build`/`preview`. Backend test coverage is minimal (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php` are the Laravel-generated stubs — no feature tests exist yet for Products/Customers/Orders).

**Resetting the database:** `php artisan migrate:fresh --seed` drops *all* tables including better-auth's — re-run `npx @better-auth/cli@latest migrate` and `npm run seed:admin` after. If `node auth-server.js` was already running, restart it — its `node:sqlite` connection won't see the drop/recreate and will keep authenticating a now-deleted admin (symptom: sign-in "succeeds" but every API call 401s).

**Promoting a user to admin:** `php artisan auth:promote you@example.com` (custom Artisan command, `app/Console/Commands/PromoteAdmin.php`).

## Architecture

Three processes, one SQLite file:

```
backend/
├── (Laravel)  API for products/customers/orders — :8000
└── (Node)     better-auth server — auth.js, auth-server.js — :3001
frontend/      Vue 3 + Vite SPA, consumes both — :5173
```

**Auth is split across two runtimes, and Laravel never issues its own sessions.** better-auth is JS-only, so it runs as a small standalone Express app (`backend/auth-server.js` hosting `backend/auth.js`), using Node's built-in `node:sqlite` pointed at the *same* database file Laravel uses. It owns `user`/`session`/`account`/`verification` and everything under `/api/auth/*`. On the Laravel side, `App\Http\Middleware\BetterAuthSession` (prepended to the whole `api` middleware group in `bootstrap/app.php`) reads that same session cookie on every request and resolves it against the shared tables via two read-only Eloquent models, `AuthUser` and `AuthSession` (non-incrementing string/cuid primary keys). `RequireAuth` (aliased `auth.required`) aborts 401 if there's no resolved user; admin-only checks happen *inside controllers* via `Controller::requireAdmin()` (abort 403), not via route middleware — check the controller, not just `routes/api.php`, to see who can actually call an endpoint.

**`user.role` (`customer` default, or `admin`) is a custom better-auth `additionalFields` entry** defined in `backend/auth.js`, with `input: false` so it can never be set by the client during signup — only via `php artisan auth:promote` or direct DB edit.

**`Customer` rows are not created at signup.** The first time a non-admin places an order, `OrderController::resolveOwnCustomer()` finds-or-creates a `Customer` matched by email and links `user_id` — this is a soft link (no real DB foreign key, since Laravel's migrations can't reference better-auth's `user` table), enforced only at the application layer.

**Order totals and status are server-computed, never client-supplied.** Order creation is wrapped in a DB transaction (order + all line items created together); `unit_price` is snapshotted per `OrderItem` at order time so historical totals survive later product price changes. `App\Enums\OrderStatus` is a backed enum with a real state machine (`availableTransitions()`): forward-only `pending → processing → shipped → completed`, with `cancelled` reachable from any non-terminal state. This is enforced server-side in `UpdateOrderStatusRequest` (422 if the target status isn't in `availableTransitions()`) — that's the actual boundary, not the frontend dropdown.

**Backend request/response layering:** one `Http\Requests` class per resource for validation, one `Http\Resources` class per resource for JSON shaping — controllers stay thin and the API JSON shape is decoupled from the Eloquent/DB schema. Follow this pattern for new endpoints rather than validating/serializing inline in the controller.

**Frontend has no state management library and no UI component library.** Each view fetches what it needs directly via thin axios wrappers in `src/api/` (one file per resource); auth/modal state lives in a couple of small reactive singletons in `src/lib/` (`auth-client.js` wraps `better-auth/vue`, `authModal.js` tracks open/close state for the login modal). Keep new features consistent with this — don't introduce Pinia or a component library for a single feature.

**Dev-only proxying:** `frontend/vite.config.js` proxies `/api/auth/*` to the Node server (:3001) and everything else under `/api/*` to Laravel (:8000), so both land on the SPA's origin and cookies flow with no CORS config needed. This doesn't exist in production — a real deploy needs a reverse proxy in front of both backends.

For the full database schema (including an ERD) and the complete REST API endpoint reference, see the `## Database schema` and `## API reference` sections in `README.md` — they're kept accurate against the actual migrations and `routes/api.php` and shouldn't be duplicated here.
