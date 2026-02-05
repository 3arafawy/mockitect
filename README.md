# Mockitect

> Local-first HTTP mocking service. Intercept requests, return configured responses.

## Quick Start

```bash
# Clone and setup
git clone https://github.com/3arafawy/mockitect.git
cd mockitect
composer install && npm install

# Start services
./vendor/bin/sail up
./vendor/bin/sail artisan migrate
npm run dev

# Create your first mock
./vendor/bin/sail artisan mockitect:create
# Or visit http://localhost/__mockitect
```

**Use it:** Point your app to `http://localhost` instead of real APIs. Mockitect intercepts requests and returns configured responses.

## Features

**Current (Phase 1):**
- 🔄 RESTful request matching (path, method, headers)
- 🔄 Static JSON/XML responses
- 🔄 Request logging & inspection
- 🔄 Web UI + CLI

**Coming Soon:**
- Dynamic templates with Faker
- Stateful scenarios
- Proxy mode & recording
- Import/Export (Postman, OpenAPI)

See [PLAN.md](docs/PLAN.md) for full roadmap.

## Documentation

| Document | Purpose |
|----------|---------|
| [docs/ADR.md](docs/ADR.md) | Architecture decisions & design rationale |
| [docs/PLAN.md](docs/PLAN.md) | Development phases, tasks & workflow |

## CLI Commands

```bash
php artisan mockitect:list          # List mocks
php artisan mockitect:create        # Create mock interactively
php artisan mockitect:import file   # Import from JSON
php artisan mockitect:export        # Export all mocks
```

## Development

```bash
# Run tests
./vendor/bin/sail test

# Code formatting
./vendor/bin/sail pint
```

**Branching:** We use [GitHub Flow](https://docs.github.com/en/get-started/quickstart/github-flow). All work in `feature/*` branches, merge via PRs to `master`.

## Tech Stack

Laravel 12 · PHP 8.2+ · Vue 3 · Inertia.js · Tailwind CSS v4 · SQLite · Pest

## License

MIT
